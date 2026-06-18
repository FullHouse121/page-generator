<?php
/**
 * builder.php — turn a data array + options into a deployable landing folder.
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/templates.php';
require_once __DIR__ . '/legal.php';

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
    $lang     = in_array($form['lang'] ?? 'en', ['en', 'es', 'pt'], true) ? $form['lang'] : 'en';
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
        'accent'       => preg_match('/^#[0-9a-f]{3,8}$/i', $form['accent'] ?? '') ? $form['accent'] : $tpl['accent'],
        'year'         => date('Y'),
        'icon'         => '',
        'screenshots'  => [],
        'privacy_url'  => 'privacy.html',
        'terms_url'    => 'terms.html',
        'S'            => lpf_strings($lang),
        'reviews'      => sample_reviews($lang),
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

    // --- Prepend Kclient include for the preloading method ---
    $indexName = 'index.' . $format;
    if ($kclient) {
        $php = "<?php\n"
             . "/* Keitaro integration — preloading method.\n"
             . "   Replace kclient.php with the file Keitaro generates for your campaign.\n"
             . "   The white page below is served; Kclient decides what the visitor gets. */\n"
             . "if (file_exists(__DIR__ . '/kclient.php')) { include __DIR__ . '/kclient.php'; }\n"
             . "?>\n";
        $html = $php . $html;
        // drop a harmless placeholder so the page renders before Keitaro's file is added
        file_put_contents($dir . '/kclient.php', kclient_placeholder());
    }

    file_put_contents($dir . '/' . $indexName, $html);
    file_put_contents($dir . '/privacy.html', legal_page('privacy', $L));
    file_put_contents($dir . '/terms.html',   legal_page('terms', $L));
    file_put_contents($dir . '/robots.txt',   "User-agent: *\nAllow: /\n");

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

function kclient_placeholder(): string {
    return "<?php\n"
        . "/**\n"
        . " * kclient.php — PLACEHOLDER.\n"
        . " *\n"
        . " * Replace this file with the Kclient.php that Keitaro generates:\n"
        . " *   Keitaro -> Campaign -> ... -> Integration -> PHP/Kclient (local).\n"
        . " * Keep the filename `kclient.php` so index.php picks it up automatically.\n"
        . " *\n"
        . " * Until you do, this no-op lets the white page render normally.\n"
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
