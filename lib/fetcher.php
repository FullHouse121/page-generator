<?php
/**
 * fetcher.php — pull app metadata from a pasted link.
 * Supports Google Play, Apple App Store, and any generic URL (Open Graph).
 * Everything is best-effort: the UI always lets the user edit the result.
 */

require_once __DIR__ . '/helpers.php';

function default_meta(): array {
    return [
        'name'         => '',
        'tagline'      => '',
        'description'  => '',
        'icon'         => '',
        'developer'    => '',
        'category'     => 'App',
        'rating'       => '4.8',
        'rating_count' => '12,480',
        'downloads'    => '1M+',
        'screenshots'  => [],
        'source'       => '',
        'source_url'   => '',
    ];
}

function detect_store(string $url): string {
    $host = strtolower(parse_url($url, PHP_URL_HOST) ?: '');
    if (strpos($host, 'play.google.com') !== false)  return 'google';
    if (strpos($host, 'apps.apple.com') !== false ||
        strpos($host, 'itunes.apple.com') !== false) return 'apple';
    return 'generic';
}

/** Clean store suffixes from a title (EN/ES/PT). */
function clean_name(string $name): string {
    // e.g. "App - Aplicaciones en Google Play", "App - Apps on Google Play",
    //      "App – Apps no Google Play", "App on the App Store", "App - App Store"
    $name = preg_replace('/\s*[-–—|:]\s*[^-–—|:]*\b(Google Play|App Store)\b.*$/iu', '', $name);
    $name = preg_replace('/\s+(on|en|no|na)\s+(the\s+)?App Store.*$/iu', '', $name);
    $name = preg_replace('/\s*[-–—|]\s*$/u', '', $name); // dangling separator
    return trim($name);
}

function fetch_app_meta(string $url): array {
    $data = default_meta();
    $url  = trim($url);
    if ($url === '' || !preg_match('#^https?://#i', $url)) {
        return $data;
    }
    $data['source_url'] = $url;
    $store = detect_store($url);
    $data['source'] = $store;

    $html = http_get($url);
    if ($html === '') {
        return $data; // network failed — user fills manually
    }

    // --- Open Graph baseline (works on almost everything) ---
    $ogTitle = clean_name(meta_prop($html, 'og:title') ?: title_tag($html));
    if ($ogTitle !== '') $data['name'] = $ogTitle;
    $ogImg = meta_prop($html, 'og:image');
    if ($ogImg !== '') $data['icon'] = $ogImg;
    $ogDesc = meta_prop($html, 'og:description');
    if ($ogDesc === '') $ogDesc = meta_prop($html, 'description');
    if ($ogDesc !== '') $data['description'] = $ogDesc;

    if ($store === 'apple')  parse_apple($html, $data);
    if ($store === 'google') parse_google($html, $data);

    // tagline fallback = first sentence of description
    if ($data['tagline'] === '' && $data['description'] !== '') {
        $first = preg_split('/(?<=[.!?])\s+/', $data['description'])[0] ?? '';
        $data['tagline'] = mb_substr(trim($first), 0, 90);
    }
    if ($data['name'] === '') $data['name'] = 'My App';

    return $data;
}

/* ------------------------------------------------------------------
 * Apple App Store — has clean JSON-LD (SoftwareApplication)
 * ------------------------------------------------------------------ */
function parse_apple(string $html, array &$data): void {
    foreach (ld_json_blocks($html) as $blk) {
        $nodes = isset($blk['@graph']) && is_array($blk['@graph']) ? $blk['@graph'] : [$blk];
        foreach ($nodes as $node) {
            $type = $node['@type'] ?? '';
            $type = is_array($type) ? implode(',', $type) : $type;
            if (stripos((string)$type, 'SoftwareApplication') === false &&
                stripos((string)$type, 'MobileApplication') === false) continue;

            if (!empty($node['name']))        $data['name']        = clean_name($node['name']);
            if (!empty($node['description'])) $data['description'] = trim($node['description']);
            if (!empty($node['image']) && is_string($node['image'])) $data['icon'] = $node['image'];
            if (!empty($node['applicationCategory'])) $data['category'] = $node['applicationCategory'];
            if (!empty($node['author']['name']))      $data['developer'] = $node['author']['name'];

            if (!empty($node['aggregateRating'])) {
                $ar = $node['aggregateRating'];
                if (!empty($ar['ratingValue'])) { $data['rating'] = (string)round((float)$ar['ratingValue'], 1); $data['_rating_real'] = true; }
                if (!empty($ar['ratingCount'])) $data['rating_count'] = number_format((int)$ar['ratingCount']);
                elseif (!empty($ar['reviewCount'])) $data['rating_count'] = number_format((int)$ar['reviewCount']);
            }
            if (!empty($node['screenshot'])) {
                $shots = is_array($node['screenshot']) ? $node['screenshot'] : [$node['screenshot']];
                foreach ($shots as $s) {
                    $u = is_array($s) ? ($s['contentUrl'] ?? $s['url'] ?? '') : $s;
                    if ($u && stripos($u, 'placeholder') === false) $data['screenshots'][] = $u;
                }
            }
        }
    }
    // Fallback screenshots: Apple serves mzstatic image URLs. Apple also reuses a
    // generic "Placeholder.mill" filler graphic for screenshot slots an app hasn't
    // filled in — the same asset across many unrelated apps — so exclude it.
    if (empty($data['screenshots'])) {
        if (preg_match_all('#https://[a-z0-9.\-]*mzstatic\.com/[^"\\\\\s]+?\.(?:png|jpg|jpeg|webp)#i', $html, $m)) {
            $real = array_values(array_filter($m[0], fn($u) => stripos($u, 'placeholder') === false));
            $data['screenshots'] = collect_shots($real, $data['icon']);
        }
    }
}

/* ------------------------------------------------------------------
 * Google Play — data is embedded, parse with heuristics
 * ------------------------------------------------------------------ */
function parse_google(string $html, array &$data): void {
    // Developer
    if (preg_match('#"name":"([^"]+)","url":"https://play.google.com/store/apps/dev#i', $html, $m)) {
        $data['developer'] = json_decode('"' . $m[1] . '"');
    }
    // Rating value — Google embeds a schema.org AggregateRating block (most reliable,
    // current page structure as of this writing); older array-index / "Rated X stars"
    // patterns kept as fallback in case Google reverts or A/B-tests the markup.
    if (preg_match('#"aggregateRating"\s*:\s*\{[^}]*?"ratingValue"\s*:\s*"?([0-5](?:\.[0-9]+)?)"?#i', $html, $m)) {
        $data['rating'] = (string)round((float)$m[1], 1); $data['_rating_real'] = true;
        if (preg_match('#"aggregateRating"\s*:\s*\{[^}]*?"ratingCount"\s*:\s*"?([0-9]+)"?#i', $html, $rc)) {
            $data['rating_count'] = number_format((int)$rc[1]);
        }
    } elseif (preg_match('#\[\[\["?([0-5]\.[0-9])"?\]#', $html, $m)) {
        $data['rating'] = $m[1]; $data['_rating_real'] = true;
    } elseif (preg_match('#Rated ([0-5](?:\.[0-9])?) stars#i', $html, $m)) {
        $data['rating'] = $m[1]; $data['_rating_real'] = true;
    }
    // Downloads e.g. 1,000,000+
    if (preg_match('#([0-9][0-9.,]*[KMB]?\+)\s*(?:downloads|descargas)#i', $html, $m)) {
        $data['downloads'] = $m[1];
    }
    // All Google CDN images, first big one = icon, rest = screenshots
    if (preg_match_all('#https://play-lh\.googleusercontent\.com/[A-Za-z0-9_\-]+#', $html, $m)) {
        $imgs = array_values(array_unique($m[0]));
        if (!empty($imgs)) {
            if ($data['icon'] === '' || strpos($data['icon'], 'play-lh') === false) {
                $data['icon'] = $imgs[0] . '=s256';
            }
            $shots = collect_shots($imgs, $data['icon']);
            $data['screenshots'] = array_map(fn($u) => $u . '=w720', array_slice($shots, 0, 6));
        }
    }
}

/** Dedupe, drop the icon, cap to 6 screenshots. */
function collect_shots(array $urls, string $icon): array {
    $iconBase = preg_replace('/=.*/', '', $icon);
    $out = [];
    foreach ($urls as $u) {
        $base = preg_replace('/=.*/', '', $u);
        if ($base === $iconBase) continue;
        if (in_array($base, $out, true)) continue;
        $out[] = $base;
        if (count($out) >= 6) break;
    }
    return $out;
}

/**
 * Real user reviews — Apple's public Customer Reviews RSS feed (documented,
 * no auth needed: itunes.apple.com/.../rss/customerreviews/...). Google Play
 * has no equivalent public API, so this returns [] for Google/generic links
 * rather than fabricating anything.
 */
function fetch_real_reviews(string $url, int $limit = 5): array {
    $url = trim($url);
    if ($url === '' || detect_store($url) !== 'apple') return [];

    if (!preg_match('#/id(\d+)#', $url, $m)) return [];
    $appId = $m[1];
    $cc = 'us';
    if (preg_match('#apps\.apple\.com/([a-z]{2})/#i', $url, $cm)) $cc = strtolower($cm[1]);

    $feedUrl = "https://itunes.apple.com/{$cc}/rss/customerreviews/id={$appId}/sortby=mostrecent/json";
    $json = http_get($feedUrl, 10);
    if ($json === '') return [];
    $data = json_decode($json, true);
    $entries = $data['feed']['entry'] ?? [];
    if (!is_array($entries)) return [];

    $out = [];
    foreach ($entries as $e) {
        // The feed's first entry is sometimes the app summary, not a review — it has no author/rating.
        $author = $e['author']['name']['label'] ?? '';
        $rating = $e['im:rating']['label'] ?? '';
        $content = $e['content']['label'] ?? '';
        if ($author === '' || $rating === '' || $content === '') continue;
        $out[] = [trim($author), (int)$rating, trim(mb_substr($content, 0, 220))];
        if (count($out) >= $limit) break;
    }
    return $out;
}
