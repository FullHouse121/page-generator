<?php
/**
 * helpers.php — shared utilities for the White LP Factory.
 * No external dependencies. Works on any PHP 7.4+ host.
 */

/* ----------------------------------------------------------------
 * Output / escaping
 * ---------------------------------------------------------------- */

/** HTML-escape for text nodes. */
function esc($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Escape for an HTML attribute value. */
function attr($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** A URL-safe slug. */
function slugify($s): string {
    $s = strtolower(trim((string)$s));
    $s = preg_replace('/[^a-z0-9]+/u', '-', $s);
    $s = trim($s, '-');
    return $s !== '' ? $s : 'app-' . substr(md5(uniqid('', true)), 0, 6);
}

/* ----------------------------------------------------------------
 * Filesystem
 * ---------------------------------------------------------------- */

function ensure_dir(string $dir): void {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

/** Recursively delete a directory. */
function rrmdir(string $dir): void {
    if (!is_dir($dir)) return;
    foreach (scandir($dir) as $f) {
        if ($f === '.' || $f === '..') continue;
        $p = $dir . '/' . $f;
        is_dir($p) ? rrmdir($p) : @unlink($p);
    }
    @rmdir($dir);
}

/** Zip a directory into $zipPath. Returns true on success. */
function zip_dir(string $sourceDir, string $zipPath): bool {
    if (!class_exists('ZipArchive')) return false;
    $zip = new ZipArchive();
    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return false;
    }
    $sourceDir = rtrim($sourceDir, '/');
    $base = basename($sourceDir);
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $file) {
        $path = $file->getPathname();
        $local = $base . '/' . ltrim(substr($path, strlen($sourceDir)), '/');
        if ($file->isDir()) {
            $zip->addEmptyDir($local);
        } else {
            $zip->addFile($path, $local);
        }
    }
    return $zip->close();
}

/* ----------------------------------------------------------------
 * HTTP
 * ---------------------------------------------------------------- */

/** A realistic desktop browser UA so stores return the rich page. */
const LPF_UA = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 '
             . '(KHTML, like Gecko) Chrome/124.0 Safari/537.36';

/** GET a URL, returns body string or '' on failure. Follows redirects. */
function http_get(string $url, int $timeout = 15): string {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_ENCODING       => '',           // accept gzip/deflate
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT      => LPF_UA,
            CURLOPT_HTTPHEADER     => [
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Language: es-MX,es;q=0.9,en;q=0.8',
            ],
        ]);
        $body = curl_exec($ch);
        curl_close($ch);
        return is_string($body) ? $body : '';
    }
    // Fallback for hosts without cURL
    $ctx = stream_context_create(['http' => [
        'method'        => 'GET',
        'timeout'       => $timeout,
        'follow_location' => 1,
        'max_redirects' => 5,
        'header'        => "User-Agent: " . LPF_UA . "\r\nAccept-Language: es-MX,es;q=0.9,en;q=0.8\r\n",
    ], 'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]]);
    $body = @file_get_contents($url, false, $ctx);
    return is_string($body) ? $body : '';
}

/** Download a remote file to $dest. Returns true on success. */
function download_file(string $url, string $dest): bool {
    $data = http_get($url, 20);
    if ($data === '' || strlen($data) < 64) return false;
    return file_put_contents($dest, $data) !== false;
}

/** Best-effort image extension from a URL or content type. */
function img_ext(string $url): string {
    $path = parse_url($url, PHP_URL_PATH) ?: '';
    $ext  = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg'], true) ? $ext : 'jpg';
}

/* ----------------------------------------------------------------
 * HTML parsing (best-effort, regex based)
 * ---------------------------------------------------------------- */

function meta_prop(string $html, string $prop): string {
    // <meta property="og:title" content="..."> (order-independent)
    if (preg_match('/<meta[^>]+(?:property|name)\s*=\s*["\']' . preg_quote($prop, '/') . '["\'][^>]*>/i', $html, $tag)) {
        if (preg_match('/content\s*=\s*["\']([^"\']*)["\']/i', $tag[0], $m)) {
            return html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
        }
    }
    return '';
}

function title_tag(string $html): string {
    if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
        return html_entity_decode(trim($m[1]), ENT_QUOTES, 'UTF-8');
    }
    return '';
}

/** Pull every application/ld+json blob and return decoded arrays. */
function ld_json_blocks(string $html): array {
    $out = [];
    if (preg_match_all('/<script[^>]+application\/ld\+json[^>]*>(.*?)<\/script>/is', $html, $m)) {
        foreach ($m[1] as $blob) {
            $decoded = json_decode(trim($blob), true);
            if (is_array($decoded)) $out[] = $decoded;
        }
    }
    return $out;
}
