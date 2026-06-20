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
            'ssl' => 'Secure & encrypted', 'verified_dev' => 'Verified developer',
            'free_safe' => 'Free · No malware', 'support' => 'Support',
            'age_note' => '18+ only — please play responsibly.',
            'cookie_text' => 'We use cookies to improve your experience.', 'cookie_ok' => 'Got it',
            'rank_title' => 'Compare Similar Apps', 'rank_sub' => 'Ratings shown come from each app\'s official store listing.', 'rank_pick' => 'This app',
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
            'ssl' => 'Seguro y cifrado', 'verified_dev' => 'Desarrollador verificado',
            'free_safe' => 'Gratis · Sin malware', 'support' => 'Soporte',
            'age_note' => 'Solo +18 — juega con responsabilidad.',
            'cookie_text' => 'Usamos cookies para mejorar tu experiencia.', 'cookie_ok' => 'Entendido',
            'rank_title' => 'Compara Apps Similares', 'rank_sub' => 'Las calificaciones provienen de la ficha oficial de cada app en la tienda.', 'rank_pick' => 'Esta app',
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
            'ssl' => 'Seguro e criptografado', 'verified_dev' => 'Desenvolvedor verificado',
            'free_safe' => 'Grátis · Sem malware', 'support' => 'Suporte',
            'age_note' => 'Apenas +18 — jogue com responsabilidade.',
            'cookie_text' => 'Usamos cookies para melhorar sua experiência.', 'cookie_ok' => 'Entendi',
            'rank_title' => 'Compare Apps Semelhantes', 'rank_sub' => 'As avaliações vêm da ficha oficial de cada app na loja.', 'rank_pick' => 'Este app',
        ],
    ];
    foreach (lpf_strings_extra() as $code => $vals) { $dict[$code] = $vals; }
    // per-key fallback to English for any string a language doesn't define
    return array_merge($dict['en'], $dict[$lang] ?? []);
}

/** Additional full translations beyond en/es/pt (merged into lpf_strings()). */
function lpf_strings_extra(): array {
    return [
        'fr' => [
            'get' => "Télécharger l'app", 'download' => 'Téléchargement gratuit', 'install' => 'Installer',
            'open' => "Ouvrir l'app", 'reviews' => 'Avis', 'features' => 'Fonctionnalités',
            'about' => "À propos de l'app", 'rating' => 'Note', 'downloads' => 'Téléchargements',
            'size' => 'Taille', 'age' => 'Âge', 'everyone' => 'Tout public', 'updated' => 'Mise à jour',
            'what_new' => 'Nouveautés', 'screens' => "Captures d'écran", 'faq' => 'FAQ',
            'privacy' => 'Politique de confidentialité', 'terms' => "Conditions d'utilisation", 'contact' => 'Contact',
            'why' => "Pourquoi on l'adore", 'join' => "Rejoignez des millions d'utilisateurs",
            'cta_sub' => 'Téléchargement gratuit. Disponible sur iOS et Android.',
            'recent' => 'Récent', 'verified' => 'Utilisateur vérifié',
            'b_fast' => "Rapide, fluide et pensée pour être simple dès le premier instant.",
            'b_secure' => 'Vos données restent privées et sécurisées grâce à une protection de pointe.',
            'b_loved' => 'Un design soigné et des mises à jour constantes que les utilisateurs adorent vraiment.',
            'b_updates' => 'De nouvelles fonctionnalités et mises à jour arrivent régulièrement.',
            'b_daily' => 'Simple, rapide et un plaisir à utiliser chaque jour.',
            'ssl' => 'Sécurisé et chiffré', 'verified_dev' => 'Développeur vérifié',
            'free_safe' => 'Gratuit · Sans malware', 'support' => 'Assistance',
            'age_note' => 'Réservé aux +18 ans — jouez de manière responsable.',
            'cookie_text' => 'Nous utilisons des cookies pour améliorer votre expérience.', 'cookie_ok' => 'Compris',
            'rank_title' => 'Comparer des Apps Similaires', 'rank_sub' => 'Les notes affichées proviennent de la fiche officielle de chaque application.', 'rank_pick' => 'Cette application',
        ],
        'de' => [
            'get' => 'App herunterladen', 'download' => 'Kostenlos herunterladen', 'install' => 'Installieren',
            'open' => 'App öffnen', 'reviews' => 'Bewertungen', 'features' => 'Funktionen',
            'about' => 'Über die App', 'rating' => 'Bewertung', 'downloads' => 'Downloads',
            'size' => 'Größe', 'age' => 'Alter', 'everyone' => 'Für alle', 'updated' => 'Aktualisiert',
            'what_new' => 'Neuigkeiten', 'screens' => 'Screenshots', 'faq' => 'FAQ',
            'privacy' => 'Datenschutzerklärung', 'terms' => 'Nutzungsbedingungen', 'contact' => 'Kontakt',
            'why' => 'Warum sie geliebt wird', 'join' => 'Schließe dich Millionen zufriedener Nutzer an',
            'cta_sub' => 'Kostenloser Download. Verfügbar für iOS und Android.',
            'recent' => 'Neu', 'verified' => 'Verifizierter Nutzer',
            'b_fast' => 'Schnell, flüssig und von Anfang an mühelos zu bedienen.',
            'b_secure' => 'Deine Daten bleiben privat und sicher durch Schutz nach Industriestandard.',
            'b_loved' => 'Durchdachtes Design und ständige Updates, die wirklich geschätzt werden.',
            'b_updates' => 'Regelmäßig neue Updates und Funktionen.',
            'b_daily' => 'Einfach, schnell und jeden Tag eine Freude.',
            'ssl' => 'Sicher & verschlüsselt', 'verified_dev' => 'Verifizierter Entwickler',
            'free_safe' => 'Kostenlos · Kein Malware', 'support' => 'Support',
            'age_note' => 'Nur ab 18 — bitte verantwortungsbewusst spielen.',
            'cookie_text' => 'Wir verwenden Cookies, um deine Erfahrung zu verbessern.', 'cookie_ok' => 'Verstanden',
            'rank_title' => 'Ähnliche Apps Vergleichen', 'rank_sub' => 'Die angezeigten Bewertungen stammen aus dem offiziellen Store-Eintrag jeder App.', 'rank_pick' => 'Diese App',
        ],
        'it' => [
            'get' => "Scarica l'app", 'download' => 'Scarica gratis', 'install' => 'Installa',
            'open' => "Apri l'app", 'reviews' => 'Recensioni', 'features' => 'Funzionalità',
            'about' => "Informazioni sull'app", 'rating' => 'Valutazione', 'downloads' => 'Download',
            'size' => 'Dimensione', 'age' => 'Età', 'everyone' => 'Per tutti', 'updated' => 'Aggiornata',
            'what_new' => 'Novità', 'screens' => 'Schermate', 'faq' => 'FAQ',
            'privacy' => 'Informativa sulla privacy', 'terms' => 'Termini di utilizzo', 'contact' => 'Contatti',
            'why' => 'Perché piace tanto', 'join' => 'Unisciti a milioni di utenti felici',
            'cta_sub' => 'Download gratuito. Disponibile per iOS e Android.',
            'recent' => 'Recente', 'verified' => 'Utente verificato',
            'b_fast' => 'Veloce, fluida e pensata per essere naturale dal primo tocco.',
            'b_secure' => 'I tuoi dati restano privati e sicuri con protezione di livello professionale.',
            'b_loved' => 'Design curato e aggiornamenti costanti che gli utenti adorano davvero.',
            'b_updates' => 'Nuovi aggiornamenti e funzionalità in arrivo regolarmente.',
            'b_daily' => 'Semplice, veloce e piacevole da usare ogni giorno.',
            'ssl' => 'Sicura e crittografata', 'verified_dev' => 'Sviluppatore verificato',
            'free_safe' => 'Gratis · Senza malware', 'support' => 'Assistenza',
            'age_note' => 'Solo +18 — gioca in modo responsabile.',
            'cookie_text' => 'Utilizziamo i cookie per migliorare la tua esperienza.', 'cookie_ok' => 'Capito',
            'rank_title' => 'Confronta App Simili', 'rank_sub' => 'Le valutazioni mostrate provengono dalla scheda ufficiale di ogni app nello store.', 'rank_pick' => 'Questa app',
        ],
        'nl' => [
            'get' => 'App downloaden', 'download' => 'Gratis downloaden', 'install' => 'Installeren',
            'open' => 'App openen', 'reviews' => 'Beoordelingen', 'features' => 'Functies',
            'about' => 'Over deze app', 'rating' => 'Beoordeling', 'downloads' => 'Downloads',
            'size' => 'Grootte', 'age' => 'Leeftijd', 'everyone' => 'Voor iedereen', 'updated' => 'Bijgewerkt',
            'what_new' => 'Nieuw', 'screens' => 'Schermafbeeldingen', 'faq' => 'Veelgestelde vragen',
            'privacy' => 'Privacybeleid', 'terms' => 'Gebruiksvoorwaarden', 'contact' => 'Contact',
            'why' => 'Waarom mensen het geweldig vinden', 'join' => 'Sluit je aan bij miljoenen tevreden gebruikers',
            'cta_sub' => 'Gratis te downloaden. Beschikbaar voor iOS en Android.',
            'recent' => 'Recent', 'verified' => 'Geverifieerde gebruiker',
            'b_fast' => 'Snel, soepel en vanaf de eerste tik moeiteloos.',
            'b_secure' => 'Je gegevens blijven privé en veilig met bescherming volgens de industriestandaard.',
            'b_loved' => 'Doordacht design en regelmatige updates die gebruikers echt waarderen.',
            'b_updates' => 'Regelmatig nieuwe updates en functies.',
            'b_daily' => 'Eenvoudig, snel en een plezier om elke dag te gebruiken.',
            'ssl' => 'Veilig en versleuteld', 'verified_dev' => 'Geverifieerde ontwikkelaar',
            'free_safe' => 'Gratis · Geen malware', 'support' => 'Ondersteuning',
            'age_note' => 'Alleen 18+ — speel verantwoord.',
            'cookie_text' => 'We gebruiken cookies om je ervaring te verbeteren.', 'cookie_ok' => 'Begrepen',
            'rank_title' => 'Vergelijk Soortgelijke Apps', 'rank_sub' => 'De getoonde beoordelingen komen van de officiële storevermelding van elke app.', 'rank_pick' => 'Deze app',
        ],
        'pl' => [
            'get' => 'Pobierz aplikację', 'download' => 'Pobierz bezpłatnie', 'install' => 'Zainstaluj',
            'open' => 'Otwórz aplikację', 'reviews' => 'Opinie', 'features' => 'Funkcje',
            'about' => 'O aplikacji', 'rating' => 'Ocena', 'downloads' => 'Pobrania',
            'size' => 'Rozmiar', 'age' => 'Wiek', 'everyone' => 'Dla wszystkich', 'updated' => 'Zaktualizowano',
            'what_new' => 'Nowości', 'screens' => 'Zrzuty ekranu', 'faq' => 'FAQ',
            'privacy' => 'Polityka prywatności', 'terms' => 'Warunki korzystania', 'contact' => 'Kontakt',
            'why' => 'Dlaczego ją kochają', 'join' => 'Dołącz do milionów zadowolonych użytkowników',
            'cta_sub' => 'Bezpłatne pobieranie. Dostępna na iOS i Android.',
            'recent' => 'Ostatnie', 'verified' => 'Zweryfikowany użytkownik',
            'b_fast' => 'Szybka, płynna i wygodna od pierwszego dotknięcia.',
            'b_secure' => 'Twoje dane są prywatne i bezpieczne dzięki standardowej ochronie branżowej.',
            'b_loved' => 'Przemyślany design i regularne aktualizacje, które użytkownicy naprawdę cenią.',
            'b_updates' => 'Nowe aktualizacje i funkcje pojawiają się regularnie.',
            'b_daily' => 'Prosta, szybka i przyjemna w codziennym użyciu.',
            'ssl' => 'Bezpieczna i szyfrowana', 'verified_dev' => 'Zweryfikowany developer',
            'free_safe' => 'Bezpłatna · Bez malware', 'support' => 'Wsparcie',
            'age_note' => 'Tylko 18+ — graj odpowiedzialnie.',
            'cookie_text' => 'Używamy plików cookie, aby poprawić twoje doświadczenie.', 'cookie_ok' => 'Rozumiem',
            'rank_title' => 'Porównaj Podobne Aplikacje', 'rank_sub' => 'Wyświetlane oceny pochodzą z oficjalnej karty każdej aplikacji w sklepie.', 'rank_pick' => 'Ta aplikacja',
        ],
        'ro' => [
            'get' => 'Descarcă aplicația', 'download' => 'Descarcă gratuit', 'install' => 'Instalează',
            'open' => 'Deschide aplicația', 'reviews' => 'Recenzii', 'features' => 'Funcții',
            'about' => 'Despre aplicație', 'rating' => 'Evaluare', 'downloads' => 'Descărcări',
            'size' => 'Dimensiune', 'age' => 'Vârstă', 'everyone' => 'Pentru toți', 'updated' => 'Actualizată',
            'what_new' => 'Noutăți', 'screens' => 'Capturi de ecran', 'faq' => 'Întrebări frecvente',
            'privacy' => 'Politica de confidențialitate', 'terms' => 'Termeni de utilizare', 'contact' => 'Contact',
            'why' => 'De ce este iubită', 'join' => 'Alătură-te milioanelor de utilizatori fericiți',
            'cta_sub' => 'Descărcare gratuită. Disponibilă pe iOS și Android.',
            'recent' => 'Recent', 'verified' => 'Utilizator verificat',
            'b_fast' => 'Rapidă, fluidă și ușor de folosit din prima clipă.',
            'b_secure' => 'Datele tale rămân private și protejate la standarde de top din industrie.',
            'b_loved' => 'Design atent și actualizări constante pe care utilizatorii le adoră.',
            'b_updates' => 'Actualizări și funcții noi vin regulat.',
            'b_daily' => 'Simplă, rapidă și plăcută de folosit zilnic.',
            'ssl' => 'Sigură și criptată', 'verified_dev' => 'Dezvoltator verificat',
            'free_safe' => 'Gratuită · Fără malware', 'support' => 'Asistență',
            'age_note' => 'Doar 18+ — joacă responsabil.',
            'cookie_text' => 'Folosim cookie-uri pentru a-ți îmbunătăți experiența.', 'cookie_ok' => 'Am înțeles',
            'rank_title' => 'Compară Aplicații Similare', 'rank_sub' => 'Evaluările afișate provin din pagina oficială a fiecărei aplicații din magazin.', 'rank_pick' => 'Această aplicație',
        ],
        'ru' => [
            'get' => 'Скачать приложение', 'download' => 'Скачать бесплатно', 'install' => 'Установить',
            'open' => 'Открыть приложение', 'reviews' => 'Отзывы', 'features' => 'Функции',
            'about' => 'О приложении', 'rating' => 'Рейтинг', 'downloads' => 'Загрузки',
            'size' => 'Размер', 'age' => 'Возраст', 'everyone' => 'Для всех', 'updated' => 'Обновлено',
            'what_new' => 'Что нового', 'screens' => 'Скриншоты', 'faq' => 'Часто задаваемые вопросы',
            'privacy' => 'Политика конфиденциальности', 'terms' => 'Условия использования', 'contact' => 'Контакты',
            'why' => 'Почему его любят', 'join' => 'Присоединяйтесь к миллионам довольных пользователей',
            'cta_sub' => 'Бесплатная загрузка. Доступно для iOS и Android.',
            'recent' => 'Недавние', 'verified' => 'Проверенный пользователь',
            'b_fast' => 'Быстрое, плавное и удобное с первого касания.',
            'b_secure' => 'Ваши данные остаются приватными и защищены по отраслевым стандартам.',
            'b_loved' => 'Продуманный дизайн и постоянные обновления, которые пользователи действительно любят.',
            'b_updates' => 'Новые обновления и функции выходят регулярно.',
            'b_daily' => 'Простое, быстрое и приятное в ежедневном использовании.',
            'ssl' => 'Безопасно и зашифровано', 'verified_dev' => 'Проверенный разработчик',
            'free_safe' => 'Бесплатно · Без вредоносного ПО', 'support' => 'Поддержка',
            'age_note' => 'Только 18+ — играйте ответственно.',
            'cookie_text' => 'Мы используем файлы cookie для улучшения вашего опыта.', 'cookie_ok' => 'Понятно',
            'rank_title' => 'Сравнить Похожие Приложения', 'rank_sub' => 'Указанные оценки взяты из официальной страницы каждого приложения в магазине.', 'rank_pick' => 'Это приложение',
        ],
        'tr' => [
            'get' => 'Uygulamayı indir', 'download' => 'Ücretsiz indir', 'install' => 'Yükle',
            'open' => 'Uygulamayı aç', 'reviews' => 'Yorumlar', 'features' => 'Özellikler',
            'about' => 'Uygulama hakkında', 'rating' => 'Puan', 'downloads' => 'İndirme',
            'size' => 'Boyut', 'age' => 'Yaş', 'everyone' => 'Herkes için', 'updated' => 'Güncellendi',
            'what_new' => 'Yenilikler', 'screens' => 'Ekran görüntüleri', 'faq' => 'Sıkça sorulan sorular',
            'privacy' => 'Gizlilik Politikası', 'terms' => 'Kullanım Şartları', 'contact' => 'İletişim',
            'why' => 'Neden seviliyor', 'join' => 'Milyonlarca mutlu kullanıcıya katıl',
            'cta_sub' => 'Ücretsiz indirme. iOS ve Android için mevcut.',
            'recent' => 'Son', 'verified' => 'Doğrulanmış kullanıcı',
            'b_fast' => 'Hızlı, akıcı ve ilk dokunuştan itibaren zahmetsiz.',
            'b_secure' => 'Verileriniz endüstri standardı koruma ile özel ve güvende kalır.',
            'b_loved' => 'Kullanıcıların gerçekten sevdiği özenli tasarım ve sürekli güncellemeler.',
            'b_updates' => 'Düzenli olarak yeni güncellemeler ve özellikler gelir.',
            'b_daily' => 'Basit, hızlı ve her gün kullanması bir zevk.',
            'ssl' => 'Güvenli ve şifreli', 'verified_dev' => 'Doğrulanmış geliştirici',
            'free_safe' => 'Ücretsiz · Zararlı yazılım yok', 'support' => 'Destek',
            'age_note' => 'Sadece 18+ — lütfen sorumlu oynayın.',
            'cookie_text' => 'Deneyiminizi iyileştirmek için çerezler kullanıyoruz.', 'cookie_ok' => 'Anladım',
            'rank_title' => 'Benzer Uygulamaları Karşılaştır', 'rank_sub' => 'Gösterilen puanlar her uygulamanın resmi mağaza sayfasından alınmıştır.', 'rank_pick' => 'Bu uygulama',
        ],
        'id' => [
            'get' => 'Unduh Aplikasi', 'download' => 'Unduh Gratis', 'install' => 'Pasang',
            'open' => 'Buka Aplikasi', 'reviews' => 'Ulasan', 'features' => 'Fitur',
            'about' => 'Tentang aplikasi ini', 'rating' => 'Penilaian', 'downloads' => 'Unduhan',
            'size' => 'Ukuran', 'age' => 'Usia', 'everyone' => 'Untuk semua', 'updated' => 'Diperbarui',
            'what_new' => 'Yang baru', 'screens' => 'Tangkapan layar', 'faq' => 'FAQ',
            'privacy' => 'Kebijakan Privasi', 'terms' => 'Ketentuan Penggunaan', 'contact' => 'Kontak',
            'why' => 'Mengapa disukai', 'join' => 'Bergabunglah dengan jutaan pengguna yang senang',
            'cta_sub' => 'Unduhan gratis. Tersedia untuk iOS dan Android.',
            'recent' => 'Terbaru', 'verified' => 'Pengguna terverifikasi',
            'b_fast' => 'Cepat, lancar, dan terasa mudah dari sentuhan pertama.',
            'b_secure' => 'Data Anda tetap pribadi dan aman dengan perlindungan standar industri.',
            'b_loved' => 'Desain yang matang dan update rutin yang benar-benar disukai pengguna.',
            'b_updates' => 'Update dan fitur baru hadir secara berkala.',
            'b_daily' => 'Sederhana, cepat, dan menyenangkan digunakan setiap hari.',
            'ssl' => 'Aman & terenkripsi', 'verified_dev' => 'Developer terverifikasi',
            'free_safe' => 'Gratis · Tanpa malware', 'support' => 'Dukungan',
            'age_note' => 'Hanya 18+ — mainkan dengan bijak.',
            'cookie_text' => 'Kami menggunakan cookie untuk meningkatkan pengalaman Anda.', 'cookie_ok' => 'Mengerti',
            'rank_title' => 'Bandingkan Aplikasi Serupa', 'rank_sub' => 'Rating yang ditampilkan berasal dari halaman resmi toko setiap aplikasi.', 'rank_pick' => 'Aplikasi ini',
        ],
        'vi' => [
            'get' => 'Tải ứng dụng', 'download' => 'Tải miễn phí', 'install' => 'Cài đặt',
            'open' => 'Mở ứng dụng', 'reviews' => 'Đánh giá', 'features' => 'Tính năng',
            'about' => 'Về ứng dụng này', 'rating' => 'Xếp hạng', 'downloads' => 'Lượt tải',
            'size' => 'Kích thước', 'age' => 'Độ tuổi', 'everyone' => 'Cho mọi người', 'updated' => 'Đã cập nhật',
            'what_new' => 'Có gì mới', 'screens' => 'Ảnh chụp màn hình', 'faq' => 'Câu hỏi thường gặp',
            'privacy' => 'Chính sách bảo mật', 'terms' => 'Điều khoản sử dụng', 'contact' => 'Liên hệ',
            'why' => 'Vì sao được yêu thích', 'join' => 'Tham gia cùng hàng triệu người dùng hài lòng',
            'cta_sub' => 'Tải miễn phí. Có sẵn cho iOS và Android.',
            'recent' => 'Gần đây', 'verified' => 'Người dùng đã xác minh',
            'b_fast' => 'Nhanh, mượt và dễ dùng ngay từ lần chạm đầu tiên.',
            'b_secure' => 'Dữ liệu của bạn luôn riêng tư và an toàn với bảo mật theo chuẩn ngành.',
            'b_loved' => 'Thiết kế chu đáo và các bản cập nhật liên tục mà người dùng thực sự yêu thích.',
            'b_updates' => 'Các bản cập nhật và tính năng mới ra mắt thường xuyên.',
            'b_daily' => 'Đơn giản, nhanh và thú vị khi dùng mỗi ngày.',
            'ssl' => 'An toàn & được mã hóa', 'verified_dev' => 'Nhà phát triển đã xác minh',
            'free_safe' => 'Miễn phí · Không có mã độc', 'support' => 'Hỗ trợ',
            'age_note' => 'Chỉ dành cho 18+ — vui chơi có trách nhiệm.',
            'cookie_text' => 'Chúng tôi sử dụng cookie để cải thiện trải nghiệm của bạn.', 'cookie_ok' => 'Đã hiểu',
            'rank_title' => 'So Sánh Các Ứng Dụng Tương Tự', 'rank_sub' => 'Xếp hạng hiển thị được lấy từ trang chính thức của từng ứng dụng trên cửa hàng.', 'rank_pick' => 'Ứng dụng này',
        ],
        'th' => [
            'get' => 'ดาวน์โหลดแอป', 'download' => 'ดาวน์โหลดฟรี', 'install' => 'ติดตั้ง',
            'open' => 'เปิดแอป', 'reviews' => 'รีวิว', 'features' => 'ฟีเจอร์',
            'about' => 'เกี่ยวกับแอปนี้', 'rating' => 'คะแนน', 'downloads' => 'ยอดดาวน์โหลด',
            'size' => 'ขนาด', 'age' => 'อายุ', 'everyone' => 'สำหรับทุกคน', 'updated' => 'อัปเดตล่าสุด',
            'what_new' => 'มีอะไรใหม่', 'screens' => 'ภาพหน้าจอ', 'faq' => 'คำถามที่พบบ่อย',
            'privacy' => 'นโยบายความเป็นส่วนตัว', 'terms' => 'ข้อกำหนดการใช้งาน', 'contact' => 'ติดต่อเรา',
            'why' => 'เหตุผลที่ผู้คนชื่นชอบ', 'join' => 'ร่วมเป็นหนึ่งในผู้ใช้หลายล้านคนที่พึงพอใจ',
            'cta_sub' => 'ดาวน์โหลดฟรี รองรับ iOS และ Android',
            'recent' => 'ล่าสุด', 'verified' => 'ผู้ใช้ที่ยืนยันแล้ว',
            'b_fast' => 'รวดเร็ว ลื่นไหล และใช้งานง่ายตั้งแต่แตะครั้งแรก',
            'b_secure' => 'ข้อมูลของคุณเป็นส่วนตัวและปลอดภัยด้วยมาตรฐานการป้องกันระดับอุตสาหกรรม',
            'b_loved' => 'ดีไซน์ที่ใส่ใจและอัปเดตอย่างต่อเนื่องที่ผู้ใช้ชื่นชอบจริงๆ',
            'b_updates' => 'มีอัปเดตและฟีเจอร์ใหม่ๆ ออกมาอย่างสม่ำเสมอ',
            'b_daily' => 'เรียบง่าย รวดเร็ว และน่าใช้ในทุกวัน',
            'ssl' => 'ปลอดภัยและเข้ารหัส', 'verified_dev' => 'นักพัฒนาที่ยืนยันแล้ว',
            'free_safe' => 'ฟรี · ไม่มีมัลแวร์', 'support' => 'ฝ่ายสนับสนุน',
            'age_note' => 'สำหรับผู้ที่มีอายุ 18 ปีขึ้นไปเท่านั้น — กรุณาเล่นอย่างมีความรับผิดชอบ',
            'cookie_text' => 'เราใช้คุกกี้เพื่อปรับปรุงประสบการณ์ของคุณ', 'cookie_ok' => 'เข้าใจแล้ว',
            'rank_title' => 'เปรียบเทียบแอปที่คล้ายกัน', 'rank_sub' => 'คะแนนที่แสดงมาจากหน้าร้านค้าทางการของแต่ละแอป', 'rank_pick' => 'แอปนี้',
        ],
        'hi' => [
            'get' => 'ऐप डाउनलोड करें', 'download' => 'मुफ़्त डाउनलोड करें', 'install' => 'इंस्टॉल करें',
            'open' => 'ऐप खोलें', 'reviews' => 'रिव्यू', 'features' => 'फ़ीचर्स',
            'about' => 'इस ऐप के बारे में', 'rating' => 'रेटिंग', 'downloads' => 'डाउनलोड',
            'size' => 'आकार', 'age' => 'उम्र', 'everyone' => 'सभी के लिए', 'updated' => 'अपडेट किया गया',
            'what_new' => 'नया क्या है', 'screens' => 'स्क्रीनशॉट', 'faq' => 'सामान्य प्रश्न',
            'privacy' => 'गोपनीयता नीति', 'terms' => 'उपयोग की शर्तें', 'contact' => 'संपर्क करें',
            'why' => 'लोग इसे क्यों पसंद करते हैं', 'join' => 'लाखों खुश यूज़र्स से जुड़ें',
            'cta_sub' => 'मुफ़्त डाउनलोड। iOS और Android के लिए उपलब्ध।',
            'recent' => 'हाल ही का', 'verified' => 'सत्यापित उपयोगकर्ता',
            'b_fast' => 'तेज़, सहज और पहले टैप से ही आसान।',
            'b_secure' => 'आपका डेटा इंडस्ट्री-स्टैंडर्ड सुरक्षा के साथ निजी और सुरक्षित रहता है।',
            'b_loved' => 'सोच-समझकर बनाई गई डिज़ाइन और निरंतर अपडेट जिन्हें लोग सच में पसंद करते हैं।',
            'b_updates' => 'नए अपडेट और फ़ीचर्स नियमित रूप से आते हैं।',
            'b_daily' => 'सरल, तेज़ और हर दिन उपयोग करने में आनंददायक।',
            'ssl' => 'सुरक्षित और एन्क्रिप्टेड', 'verified_dev' => 'सत्यापित डेवलपर',
            'free_safe' => 'मुफ़्त · मैलवेयर-मुक्त', 'support' => 'सहायता',
            'age_note' => 'केवल 18+ — ज़िम्मेदारी से खेलें।',
            'cookie_text' => 'हम आपके अनुभव को बेहतर बनाने के लिए कुकीज़ का उपयोग करते हैं।', 'cookie_ok' => 'समझ गया',
            'rank_title' => 'समान ऐप्स की तुलना करें', 'rank_sub' => 'दिखाई गई रेटिंग प्रत्येक ऐप की आधिकारिक स्टोर लिस्टिंग से ली गई हैं।', 'rank_pick' => 'यह ऐप',
        ],
        'ja' => [
            'get' => 'アプリを入手', 'download' => '無料ダウンロード', 'install' => 'インストール',
            'open' => 'アプリを開く', 'reviews' => 'レビュー', 'features' => '機能',
            'about' => 'このアプリについて', 'rating' => '評価', 'downloads' => 'ダウンロード数',
            'size' => 'サイズ', 'age' => '対象年齢', 'everyone' => '全年齢対象', 'updated' => '更新済み',
            'what_new' => '新機能', 'screens' => 'スクリーンショット', 'faq' => 'よくある質問',
            'privacy' => 'プライバシーポリシー', 'terms' => '利用規約', 'contact' => 'お問い合わせ',
            'why' => '愛される理由', 'join' => '何百万人もの満足したユーザーに加わろう',
            'cta_sub' => '無料ダウンロード。iOSとAndroidに対応。',
            'recent' => '最近', 'verified' => '認証済みユーザー',
            'b_fast' => '高速でスムーズ、最初のタップから快適に使えます。',
            'b_secure' => '業界標準の保護により、データは常にプライベートかつ安全です。',
            'b_loved' => '丁寧なデザインと継続的な更新が本当に愛されています。',
            'b_updates' => '新しい機能とアップデートが定期的に追加されます。',
            'b_daily' => 'シンプルで高速、毎日使うのが楽しいアプリです。',
            'ssl' => '安全かつ暗号化', 'verified_dev' => '認証済み開発者',
            'free_safe' => '無料 · マルウェアなし', 'support' => 'サポート',
            'age_note' => '18歳以上限定 — 責任を持ってご利用ください。',
            'cookie_text' => 'より良い体験のためにクッキーを使用しています。', 'cookie_ok' => '了解',
            'rank_title' => '似たアプリを比較', 'rank_sub' => '表示されている評価は各アプリの公式ストアページから取得しています。', 'rank_pick' => 'このアプリ',
        ],
        'ko' => [
            'get' => '앱 다운로드', 'download' => '무료 다운로드', 'install' => '설치',
            'open' => '앱 열기', 'reviews' => '리뷰', 'features' => '기능',
            'about' => '앱 소개', 'rating' => '평점', 'downloads' => '다운로드 수',
            'size' => '크기', 'age' => '연령', 'everyone' => '전체 이용가', 'updated' => '업데이트됨',
            'what_new' => '새로운 기능', 'screens' => '스크린샷', 'faq' => '자주 묻는 질문',
            'privacy' => '개인정보 보호정책', 'terms' => '이용약관', 'contact' => '문의하기',
            'why' => '사랑받는 이유', 'join' => '수백만 명의 만족한 사용자와 함께하세요',
            'cta_sub' => '무료 다운로드. iOS와 Android에서 사용 가능.',
            'recent' => '최근', 'verified' => '인증된 사용자',
            'b_fast' => '빠르고 부드러우며 처음 탭부터 편안하게 작동합니다.',
            'b_secure' => '업계 표준 보호로 데이터가 안전하고 비공개로 유지됩니다.',
            'b_loved' => '세심한 디자인과 꾸준한 업데이트로 사용자들에게 진정으로 사랑받습니다.',
            'b_updates' => '새로운 업데이트와 기능이 정기적으로 제공됩니다.',
            'b_daily' => '간단하고 빠르며 매일 사용하기 즐겁습니다.',
            'ssl' => '안전하고 암호화됨', 'verified_dev' => '인증된 개발자',
            'free_safe' => '무료 · 악성코드 없음', 'support' => '지원',
            'age_note' => '18세 이상만 — 책임감 있게 이용하세요.',
            'cookie_text' => '더 나은 경험을 위해 쿠키를 사용합니다.', 'cookie_ok' => '확인',
            'rank_title' => '유사 앱 비교', 'rank_sub' => '표시된 평점은 각 앱의 공식 스토어 페이지에서 가져온 것입니다.', 'rank_pick' => '이 앱',
        ],
        'zh' => [
            'get' => '获取应用', 'download' => '免费下载', 'install' => '安装',
            'open' => '打开应用', 'reviews' => '评价', 'features' => '功能',
            'about' => '关于此应用', 'rating' => '评分', 'downloads' => '下载量',
            'size' => '大小', 'age' => '年龄分级', 'everyone' => '所有人群', 'updated' => '已更新',
            'what_new' => '新版本特性', 'screens' => '应用截图', 'faq' => '常见问题',
            'privacy' => '隐私政策', 'terms' => '使用条款', 'contact' => '联系我们',
            'why' => '受欢迎的原因', 'join' => '加入数百万满意的用户',
            'cta_sub' => '免费下载。支持 iOS 和 Android。',
            'recent' => '最近', 'verified' => '已验证用户',
            'b_fast' => '快速流畅，从第一次点击就毫不费力。',
            'b_secure' => '采用行业标准保护，您的数据始终私密安全。',
            'b_loved' => '用心的设计与持续的更新，深受用户喜爱。',
            'b_updates' => '新功能和更新定期推出。',
            'b_daily' => '简单、快速，每天使用都很愉快。',
            'ssl' => '安全加密', 'verified_dev' => '已验证开发者',
            'free_safe' => '免费 · 无恶意软件', 'support' => '支持',
            'age_note' => '仅限18岁以上 — 请理性使用。',
            'cookie_text' => '我们使用 Cookie 来改善您的体验。', 'cookie_ok' => '知道了',
            'rank_title' => '比较类似应用', 'rank_sub' => '显示的评分来自每个应用的官方商店页面。', 'rank_pick' => '本应用',
        ],
        'ar' => [
            'get' => 'تنزيل التطبيق', 'download' => 'تنزيل مجاني', 'install' => 'تثبيت',
            'open' => 'فتح التطبيق', 'reviews' => 'التقييمات', 'features' => 'الميزات',
            'about' => 'حول هذا التطبيق', 'rating' => 'التقييم', 'downloads' => 'التنزيلات',
            'size' => 'الحجم', 'age' => 'العمر', 'everyone' => 'لجميع الأعمار', 'updated' => 'تم التحديث',
            'what_new' => 'ما الجديد', 'screens' => 'لقطات الشاشة', 'faq' => 'الأسئلة الشائعة',
            'privacy' => 'سياسة الخصوصية', 'terms' => 'شروط الاستخدام', 'contact' => 'تواصل معنا',
            'why' => 'لماذا يحبه الناس', 'join' => 'انضم إلى ملايين المستخدمين الراضين',
            'cta_sub' => 'تنزيل مجاني. متوفر لأنظمة iOS و Android.',
            'recent' => 'الأحدث', 'verified' => 'مستخدم موثّق',
            'b_fast' => 'سريع وسلس وسهل الاستخدام منذ أول لمسة.',
            'b_secure' => 'تظل بياناتك خاصة وآمنة بحماية بمعايير الصناعة.',
            'b_loved' => 'تصميم مدروس وتحديثات مستمرة يحبها المستخدمون بصدق.',
            'b_updates' => 'تحديثات وميزات جديدة تصل بانتظام.',
            'b_daily' => 'بسيط وسريع ومتعة في الاستخدام كل يوم.',
            'ssl' => 'آمن ومشفّر', 'verified_dev' => 'مطوّر موثّق',
            'free_safe' => 'مجاني · بلا برامج ضارة', 'support' => 'الدعم',
            'age_note' => 'لمن هم +18 فقط — يرجى اللعب بمسؤولية.',
            'cookie_text' => 'نستخدم ملفات تعريف الارتباط لتحسين تجربتك.', 'cookie_ok' => 'حسنًا',
            'rank_title' => 'مقارنة تطبيقات مماثلة', 'rank_sub' => 'التقييمات المعروضة مأخوذة من صفحة المتجر الرسمية لكل تطبيق.', 'rank_pick' => 'هذا التطبيق',
        ],
    ];
}

/** All languages offered in the picker (code => native name). */
function supported_langs(): array {
    return [
        'en' => 'English', 'es' => 'Español', 'pt' => 'Português', 'fr' => 'Français',
        'de' => 'Deutsch', 'it' => 'Italiano', 'nl' => 'Nederlands', 'pl' => 'Polski',
        'ro' => 'Română', 'ru' => 'Русский', 'tr' => 'Türkçe', 'id' => 'Bahasa Indonesia',
        'vi' => 'Tiếng Việt', 'th' => 'ไทย', 'hi' => 'हिन्दी', 'ja' => '日本語',
        'ko' => '한국어', 'zh' => '中文 (简体)', 'ar' => 'العربية',
    ];
}

/** Right-to-left languages (get dir="rtl"). */
function rtl_langs(): array { return ['ar', 'he', 'fa', 'ur']; }

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
        'fr' => [
            ['Jordan M.', 5, "Exactement ce qu'il me fallait. Rapide, propre et super facile à utiliser."],
            ['Priya S.', 5, 'La meilleure app de sa catégorie. Le design est magnifique et tout fonctionne parfaitement.'],
            ['Marco D.', 4, 'Vraiment solide. Les mises à jour sont fréquentes et le support réactif.'],
            ['Aisha K.', 5, "Je l'utilise tous les jours. Je n'imagine plus mon téléphone sans elle."],
        ],
        'de' => [
            ['Jordan M.', 5, 'Genau das, was ich brauchte. Schnell, übersichtlich und super einfach zu bedienen.'],
            ['Priya S.', 5, 'Die beste App ihrer Kategorie. Das Design ist wunderschön und alles funktioniert einwandfrei.'],
            ['Marco D.', 4, 'Wirklich solide. Updates kommen oft und der Support reagiert schnell.'],
            ['Aisha K.', 5, 'Ich nutze sie jeden Tag. Ohne sie kann ich mir mein Handy nicht mehr vorstellen.'],
        ],
        'it' => [
            ['Jordan M.', 5, 'Esattamente quello di cui avevo bisogno. Veloce, ordinata e facilissima da usare.'],
            ['Priya S.', 5, 'La migliore app della categoria. Il design è splendido e funziona alla perfezione.'],
            ['Marco D.', 4, 'Davvero solida. Gli aggiornamenti sono frequenti e il supporto è reattivo.'],
            ['Aisha K.', 5, 'La uso tutti i giorni. Non immagino il telefono senza di lei.'],
        ],
        'nl' => [
            ['Jordan M.', 5, 'Precies wat ik nodig had. Snel, overzichtelijk en supergemakkelijk te gebruiken.'],
            ['Priya S.', 5, 'De beste app in zijn categorie. Het design is prachtig en alles werkt perfect.'],
            ['Marco D.', 4, 'Echt solide. Updates komen vaak en de support reageert snel.'],
            ['Aisha K.', 5, 'Ik gebruik het elke dag. Ik kan me mijn telefoon er niet meer zonder voorstellen.'],
        ],
        'pl' => [
            ['Jordan M.', 5, 'Dokładnie to, czego potrzebowałem. Szybka, przejrzysta i bardzo łatwa w użyciu.'],
            ['Priya S.', 5, 'Najlepsza aplikacja w swojej kategorii. Design jest piękny i wszystko działa świetnie.'],
            ['Marco D.', 4, 'Naprawdę solidna. Aktualizacje są częste, a wsparcie szybko odpowiada.'],
            ['Aisha K.', 5, 'Używam jej codziennie. Nie wyobrażam sobie telefonu bez niej.'],
        ],
        'ro' => [
            ['Jordan M.', 5, 'Exact ce aveam nevoie. Rapidă, curată și foarte ușor de folosit.'],
            ['Priya S.', 5, 'Cea mai bună aplicație din categoria ei. Designul este superb și totul funcționează perfect.'],
            ['Marco D.', 4, 'Foarte solidă. Actualizările vin des, iar suportul răspunde rapid.'],
            ['Aisha K.', 5, 'O folosesc zilnic. Nu îmi imaginez telefonul fără ea.'],
        ],
        'ru' => [
            ['Jordan M.', 5, 'Именно то, что мне было нужно. Быстрое, аккуратное и очень простое в использовании.'],
            ['Priya S.', 5, 'Лучшее приложение в своей категории. Дизайн потрясающий, и всё работает безупречно.'],
            ['Marco D.', 4, 'Очень надёжное. Обновления выходят часто, а поддержка отвечает быстро.'],
            ['Aisha K.', 5, 'Пользуюсь каждый день. Не представляю свой телефон без него.'],
        ],
        'tr' => [
            ['Jordan M.', 5, 'Tam da ihtiyacım olan şey. Hızlı, sade ve kullanımı çok kolay.'],
            ['Priya S.', 5, 'Kategorisindeki en iyi uygulama. Tasarım muhteşem ve her şey kusursuz çalışıyor.'],
            ['Marco D.', 4, 'Gerçekten sağlam. Güncellemeler sık geliyor ve destek hızlı yanıt veriyor.'],
            ['Aisha K.', 5, 'Her gün kullanıyorum. Telefonumu onsuz hayal edemiyorum.'],
        ],
        'id' => [
            ['Jordan M.', 5, 'Persis yang saya butuhkan. Cepat, rapi, dan sangat mudah digunakan.'],
            ['Priya S.', 5, 'Aplikasi terbaik di kategorinya. Desainnya indah dan semuanya berjalan sempurna.'],
            ['Marco D.', 4, 'Benar-benar solid. Update sering muncul dan dukungannya responsif.'],
            ['Aisha K.', 5, 'Saya pakai setiap hari. Tidak terbayang ponsel saya tanpa aplikasi ini.'],
        ],
        'vi' => [
            ['Jordan M.', 5, 'Chính xác là thứ tôi cần. Nhanh, gọn gàng và rất dễ sử dụng.'],
            ['Priya S.', 5, 'Ứng dụng tốt nhất trong danh mục. Thiết kế tuyệt đẹp và mọi thứ hoạt động hoàn hảo.'],
            ['Marco D.', 4, 'Rất ổn định. Cập nhật thường xuyên và hỗ trợ phản hồi nhanh.'],
            ['Aisha K.', 5, 'Tôi dùng mỗi ngày. Không thể tưởng tượng điện thoại của mình thiếu nó.'],
        ],
        'th' => [
            ['Jordan M.', 5, 'ตรงกับที่ฉันต้องการเลย เร็ว เรียบง่าย และใช้งานง่ายมาก'],
            ['Priya S.', 5, 'แอปที่ดีที่สุดในหมวดหมู่นี้ ดีไซน์สวยงามและทุกอย่างทำงานได้สมบูรณ์แบบ'],
            ['Marco D.', 4, 'มั่นคงมาก อัปเดตบ่อยและทีมซัพพอร์ตตอบเร็ว'],
            ['Aisha K.', 5, 'ฉันใช้ทุกวัน นึกภาพโทรศัพท์ของฉันไม่มีแอปนี้ไม่ออกเลย'],
        ],
        'hi' => [
            ['Jordan M.', 5, 'बिल्कुल वही जो मुझे चाहिए था। तेज़, साफ़-सुथरा और इस्तेमाल करना बहुत आसान।'],
            ['Priya S.', 5, 'अपनी कैटेगरी में सबसे बेहतरीन ऐप। डिज़ाइन शानदार है और सब कुछ बिल्कुल सही चलता है।'],
            ['Marco D.', 4, 'सच में भरोसेमंद। अपडेट अक्सर आते हैं और सपोर्ट तुरंत जवाब देता है।'],
            ['Aisha K.', 5, 'मैं इसे हर दिन इस्तेमाल करता हूँ। इसके बिना अपना फोन सोच भी नहीं सकता।'],
        ],
        'ja' => [
            ['Jordan M.', 5, 'まさに私が求めていたものです。速くてシンプルで、とても使いやすいです。'],
            ['Priya S.', 5, 'このカテゴリで最高のアプリです。デザインが美しく、すべてが完璧に動作します。'],
            ['Marco D.', 4, '本当に安定しています。更新も頻繁で、サポートの対応も早いです。'],
            ['Aisha K.', 5, '毎日使っています。これなしのスマホはもう考えられません。'],
        ],
        'ko' => [
            ['Jordan M.', 5, '정확히 제가 필요했던 앱입니다. 빠르고 깔끔하며 사용하기 정말 쉬워요.'],
            ['Priya S.', 5, '이 분야 최고의 앱입니다. 디자인이 아름답고 모든 것이 완벽하게 작동해요.'],
            ['Marco D.', 4, '정말 안정적입니다. 업데이트가 자주 있고 지원팀도 빠르게 응답해요.'],
            ['Aisha K.', 5, '매일 사용하고 있어요. 이제 이 앱 없는 휴대폰은 상상할 수 없어요.'],
        ],
        'zh' => [
            ['Jordan M.', 5, '正是我需要的。快速、简洁，使用起来非常方便。'],
            ['Priya S.', 5, '同类应用中最好的一个。设计精美，一切运行完美。'],
            ['Marco D.', 4, '非常稳定。更新频繁，客服响应也很迅速。'],
            ['Aisha K.', 5, '我每天都在用。已经无法想象手机里没有它的样子。'],
        ],
        'ar' => [
            ['Jordan M.', 5, 'هذا تمامًا ما كنت أحتاجه. سريع وبسيط وسهل الاستخدام للغاية.'],
            ['Priya S.', 5, 'أفضل تطبيق في فئته. التصميم رائع وكل شيء يعمل بشكل مثالي.'],
            ['Marco D.', 4, 'موثوق فعلاً. التحديثات متكررة والدعم سريع الاستجابة.'],
            ['Aisha K.', 5, 'أستخدمه كل يوم. لا أستطيع تخيل هاتفي بدونه.'],
        ],
    ];
    return $r[$lang] ?? $r['en'];
}
