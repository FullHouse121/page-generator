<?php
/**
 * templates.php — template registry + render helpers shared by all templates.
 */

/** The 5 available templates. `accent` is the default brand color. */
function template_registry(): array {
    return [
        'spotlight' => [
            'name'  => 'Spotlight',
            'file'  => 'spotlight.php',
            'accent'=> '#5b8cff',
            'desc'  => 'Gradient hero, phone mockups, feature grid, reviews. All-rounder.',
        ],
        'minimal' => [
            'name'  => 'Minimal',
            'file'  => 'minimal.php',
            'accent'=> '#111827',
            'desc'  => 'Apple-style clean white, centered, one screenshot row, single CTA.',
        ],
        'storehero' => [
            'name'  => 'Store Hero',
            'file'  => 'storehero.php',
            'accent'=> '#00b27a',
            'desc'  => 'Mimics an app-store listing: install bar, rating breakdown, about.',
        ],
        'feature' => [
            'name'  => 'Feature Flow',
            'file'  => 'feature.php',
            'accent'=> '#7c5cff',
            'desc'  => 'Alternating feature blocks, stats bar, testimonials, sticky CTA.',
        ],
        'bold' => [
            'name'  => 'Bold Dark',
            'file'  => 'bold.php',
            'accent'=> '#ff5a5f',
            'desc'  => 'Dark, large type, glow, store badges, FAQ accordion.',
        ],
        'playstore' => [
            'name'  => 'Play Store',
            'file'  => 'playstore.php',
            'accent'=> '#00b27a',
            'desc'  => 'Authentic Google Play listing — install bar, data safety, rating bars.',
        ],
        'aurora' => [
            'name'  => 'Aurora',
            'file'  => 'aurora.php',
            'accent'=> '#a15bff',
            'desc'  => 'Premium glassmorphism, aurora gradient hero, glass cards.',
        ],
        'editorial' => [
            'name'  => 'Editorial',
            'file'  => 'editorial.php',
            'accent'=> '#16181d',
            'desc'  => 'Clean light, magazine big-type, large screenshots.',
        ],
    ];
}

/* ------------------------------------------------------------------
 * Render helpers — available inside every template ($L is the data array)
 * ------------------------------------------------------------------ */

/** Star rating row as inline SVG, scaled to a 0–5 value. */
function stars_svg(float $rating, string $color = '#f5b301', int $size = 18): string {
    $out = '<span class="lpf-stars" aria-label="' . attr(round($rating, 1)) . ' out of 5" role="img">';
    for ($i = 1; $i <= 5; $i++) {
        $fill = $rating >= $i ? 1.0 : max(0.0, min(1.0, $rating - ($i - 1)));
        $pct  = (int)round($fill * 100);
        $gid  = 'lpfstar' . $i . substr(md5($color . $rating . $i), 0, 4);
        $out .= '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" style="vertical-align:middle">'
              . '<defs><linearGradient id="' . $gid . '"><stop offset="' . $pct . '%" stop-color="' . attr($color) . '"/>'
              . '<stop offset="' . $pct . '%" stop-color="rgba(125,125,125,.30)"/></linearGradient></defs>'
              . '<path fill="url(#' . $gid . ')" d="M12 2l2.9 6.3 6.9.7-5.1 4.6 1.4 6.8L12 17.8 5.9 20.4l1.4-6.8L2.2 9l6.9-.7z"/></svg>';
    }
    return $out . '</span>';
}

/** A small set of reusable inline SVG icons (no external requests). */
function svg_icon(string $name, int $size = 24): string {
    $p = [
        'bolt'    => '<path d="M13 2L3 14h7l-1 8 10-12h-7z"/>',
        'shield'  => '<path d="M12 2l8 3v6c0 5-3.5 8.5-8 11-4.5-2.5-8-6-8-11V5z"/>',
        'sparkle' => '<path d="M12 2l2 6 6 2-6 2-2 6-2-6-6-2 6-2z"/>',
        'heart'   => '<path d="M12 21s-7-4.6-9.5-9C1 9 2.5 5.5 6 5.5c2 0 3.2 1.2 4 2.4.8-1.2 2-2.4 4-2.4 3.5 0 5 3.5 3.5 6.5C19 16.4 12 21 12 21z"/>',
        'globe'   => '<path d="M12 2a10 10 0 100 20 10 10 0 000-20zm0 0c3 3 3 17 0 20M2 12h20M4 7h16M4 17h16" fill="none" stroke="currentColor" stroke-width="1.6"/>',
        'lock'    => '<path d="M6 10V8a6 6 0 1112 0v2h1v12H5V10z" fill="none" stroke="currentColor" stroke-width="1.6"/>',
        'check'   => '<path d="M20 6L9 17l-5-5" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/>',
        'down'    => '<path d="M12 3v12m0 0l-5-5m5 5l5-5M5 21h14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>',
        'star'    => '<path d="M12 2l2.9 6.3 6.9.7-5.1 4.6 1.4 6.8L12 17.8 5.9 20.4l1.4-6.8L2.2 9l6.9-.7z"/>',
    ];
    $body = $p[$name] ?? $p['sparkle'];
    return '<svg class="lpf-ico" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">' . $body . '</svg>';
}

/** Apple + Google store badges as inline SVG, linking to the CTA. */
function store_badges(string $href, string $lang = 'en'): string {
    $app = $lang === 'es' ? 'App Store' : ($lang === 'pt' ? 'App Store' : 'App Store');
    $get = $lang === 'es' ? 'Disponible en' : ($lang === 'pt' ? 'Baixar na' : 'Download on the');
    $on  = $lang === 'es' ? 'Disfrútalo en' : ($lang === 'pt' ? 'Disponível no' : 'GET IT ON');
    $h = attr($href);
    return
    '<div class="lpf-badges">'
    . '<a href="' . $h . '" class="lpf-badge"><svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M16.5 12.6c0-2 1.6-3 1.7-3-1-1.4-2.4-1.6-2.9-1.6-1.2-.1-2.4.7-3 .7-.6 0-1.6-.7-2.6-.7-1.3 0-2.6.8-3.3 2-1.4 2.4-.4 6 1 8 .7.9 1.4 2 2.4 1.9 1-.1 1.3-.6 2.5-.6s1.5.6 2.5.6 1.7-.9 2.3-1.8c.7-1 1-2 1-2-.1 0-2-.8-2-3.1zM14.7 5.9c.5-.7.9-1.6.8-2.6-.8 0-1.8.6-2.4 1.2-.5.6-1 1.5-.9 2.4.9.1 1.9-.4 2.5-1z"/></svg>'
    . '<span><small>' . esc($get) . '</small><strong>App Store</strong></span></a>'
    . '<a href="' . $h . '" class="lpf-badge"><svg width="20" height="22" viewBox="0 0 24 24"><path fill="#34a853" d="M3 3l11 9-3 3z"/><path fill="#fbbc04" d="M14 12l4-2 3 2-3 2z"/><path fill="#ea4335" d="M3 3l8 6 3 3-3 3z" opacity=".0"/><path fill="#4285f4" d="M3 3l8 6-8 12z"/><path fill="#ea4335" d="M3 21l8-9 3 3z"/></svg>'
    . '<span><small>' . esc($on) . '</small><strong>Google Play</strong></span></a>'
    . '</div>';
}

/* ------------------------------------------------------------------
 * Microcopy dictionary (white-safe, neutral) per language
 * ------------------------------------------------------------------ */
function lpf_strings(string $lang): array {
    $dict = [
        'en' => [
            'get' => 'Get the App', 'download' => 'Download Free', 'install' => 'Install',
            'open' => 'Open App', 'reviews' => 'Reviews', 'features' => 'Features',
            'about' => 'About this app', 'rating' => 'Rating', 'downloads' => 'Downloads',
            'size' => 'Size', 'age' => 'Age', 'everyone' => 'Everyone', 'updated' => 'Updated',
            'what_new' => "What's new", 'screens' => 'Screenshots', 'faq' => 'FAQ',
            'privacy' => 'Privacy Policy', 'terms' => 'Terms of Use', 'contact' => 'Contact',
            'why' => 'Why people love it', 'join' => 'Join millions of happy users',
            'cta_sub' => 'Free to download. Available for iOS and Android.',
            'recent' => 'Recent', 'verified' => 'Verified user',
            'b_fast' => 'Fast, smooth and built to feel effortless from the first tap.',
            'b_secure' => 'Your data stays private and secure with industry-standard protection.',
            'b_loved' => 'Thoughtful design and constant updates that people genuinely love.',
            'b_updates' => 'Fresh updates and new features land regularly.',
            'b_daily' => 'Effortless, fast and a joy to use every day.',
        ],
        'es' => [
            'get' => 'Descargar App', 'download' => 'Descargar Gratis', 'install' => 'Instalar',
            'open' => 'Abrir App', 'reviews' => 'Reseñas', 'features' => 'Funciones',
            'about' => 'Acerca de la app', 'rating' => 'Calificación', 'downloads' => 'Descargas',
            'size' => 'Tamaño', 'age' => 'Edad', 'everyone' => 'Todos', 'updated' => 'Actualizado',
            'what_new' => 'Novedades', 'screens' => 'Capturas', 'faq' => 'Preguntas frecuentes',
            'privacy' => 'Política de Privacidad', 'terms' => 'Términos de Uso', 'contact' => 'Contacto',
            'why' => 'Por qué les encanta', 'join' => 'Únete a millones de usuarios',
            'cta_sub' => 'Descarga gratis. Disponible para iOS y Android.',
            'recent' => 'Reciente', 'verified' => 'Usuario verificado',
            'b_fast' => 'Rápida, fluida y pensada para sentirse natural desde el primer toque.',
            'b_secure' => 'Tus datos se mantienen privados y seguros con protección estándar del sector.',
            'b_loved' => 'Diseño cuidado y actualizaciones constantes que de verdad encantan.',
            'b_updates' => 'Actualizaciones y nuevas funciones llegan con frecuencia.',
            'b_daily' => 'Sencilla, rápida y un placer de usar cada día.',
        ],
        'pt' => [
            'get' => 'Baixar App', 'download' => 'Baixar Grátis', 'install' => 'Instalar',
            'open' => 'Abrir App', 'reviews' => 'Avaliações', 'features' => 'Recursos',
            'about' => 'Sobre o app', 'rating' => 'Avaliação', 'downloads' => 'Downloads',
            'size' => 'Tamanho', 'age' => 'Idade', 'everyone' => 'Todos', 'updated' => 'Atualizado',
            'what_new' => 'Novidades', 'screens' => 'Capturas', 'faq' => 'Perguntas frequentes',
            'privacy' => 'Política de Privacidade', 'terms' => 'Termos de Uso', 'contact' => 'Contato',
            'why' => 'Por que adoram', 'join' => 'Junte-se a milhões de usuários',
            'cta_sub' => 'Download grátis. Disponível para iOS e Android.',
            'recent' => 'Recente', 'verified' => 'Usuário verificado',
            'b_fast' => 'Rápido, fluido e feito para parecer natural desde o primeiro toque.',
            'b_secure' => 'Seus dados ficam privados e seguros com proteção padrão do setor.',
            'b_loved' => 'Design caprichado e atualizações constantes que as pessoas adoram.',
            'b_updates' => 'Atualizações e novos recursos chegam com frequência.',
            'b_daily' => 'Simples, rápido e um prazer de usar todos os dias.',
        ],
    ];
    return $dict[$lang] ?? $dict['en'];
}

/** Three localized [icon, title, body] feature cards shared by the templates. */
function feature_points(array $S): array {
    return [
        ['bolt',    $S['features'], $S['b_fast']],
        ['shield',  $S['privacy'],  $S['b_secure']],
        ['heart',   $S['why'],      $S['b_loved']],
    ];
}

/** Synthetic-but-plausible review snippets when a store gives none. */
function sample_reviews(string $lang): array {
    $r = [
        'en' => [
            ['Jordan M.', 5, 'Exactly what I needed. Fast, clean and super easy to use.'],
            ['Priya S.', 5, 'Best app in its category. The design is gorgeous and it just works.'],
            ['Marco D.', 4, 'Really solid. Updates come often and support is responsive.'],
            ['Aisha K.', 5, "I use it every single day. Can't imagine my phone without it."],
        ],
        'es' => [
            ['Jordan M.', 5, 'Justo lo que necesitaba. Rápida, limpia y muy fácil de usar.'],
            ['Priya S.', 5, 'La mejor app de su categoría. El diseño es precioso y funciona genial.'],
            ['Marco D.', 4, 'Muy sólida. Las actualizaciones son frecuentes y el soporte responde.'],
            ['Aisha K.', 5, 'La uso todos los días. No imagino mi teléfono sin ella.'],
        ],
        'pt' => [
            ['Jordan M.', 5, 'Exatamente o que eu precisava. Rápida, limpa e muito fácil de usar.'],
            ['Priya S.', 5, 'O melhor app da categoria. O design é lindo e funciona muito bem.'],
            ['Marco D.', 4, 'Muito sólido. As atualizações são frequentes e o suporte responde.'],
            ['Aisha K.', 5, 'Uso todos os dias. Não imagino meu celular sem ele.'],
        ],
    ];
    return $r[$lang] ?? $r['en'];
}
