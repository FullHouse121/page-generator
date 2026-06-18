<?php
/**
 * auth.php — optional HTTP Basic Auth for the generator UI.
 *
 * Active ONLY when the LPF_USER env var is set (e.g. on Render). When it's
 * unset — like the local one-click launcher — this is a no-op, so nothing
 * changes locally. Gates index.php only; generated landing pages stay public.
 */
(function () {
    $user = getenv('LPF_USER');
    if ($user === false || $user === '') {
        return; // no credentials configured → open
    }
    $pass = getenv('LPF_PASS');
    if ($pass === false) $pass = '';

    $u = $_SERVER['PHP_AUTH_USER'] ?? null;
    $p = $_SERVER['PHP_AUTH_PW'] ?? null;

    // Fallback: parse the Authorization header (some proxies forward it raw)
    if ($u === null && !empty($_SERVER['HTTP_AUTHORIZATION'])) {
        if (preg_match('/Basic\s+(.+)$/i', $_SERVER['HTTP_AUTHORIZATION'], $m)) {
            $decoded = base64_decode($m[1], true);
            if ($decoded !== false && strpos($decoded, ':') !== false) {
                [$u, $p] = array_pad(explode(':', $decoded, 2), 2, '');
            }
        }
    }

    $ok = is_string($u) && is_string($p)
        && hash_equals($user, $u) && hash_equals($pass, $p);

    if (!$ok) {
        header('WWW-Authenticate: Basic realm="White LP Factory"');
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: text/plain; charset=utf-8');
        echo 'Authentication required.';
        exit;
    }
})();
