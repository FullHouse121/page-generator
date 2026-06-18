<?php
/**
 * legal.php — generate a Privacy Policy / Terms of Use page for a landing.
 * Self-contained HTML, styled to match, localized (en/es/pt).
 * Having real legal pages linked in the footer helps ad-review approval.
 */

function legal_page(string $kind, array $L): string {
    $lang = $L['lang'] ?? 'en';
    $name = esc($L['name'] ?? 'App');
    $accent = attr($L['accent'] ?? '#5b8cff');
    $year = esc($L['year'] ?? date('Y'));
    $T = legal_strings($lang);
    $title = $kind === 'terms' ? $T['terms_title'] : $T['privacy_title'];
    $sections = $kind === 'terms' ? $T['terms'] : $T['privacy'];

    $body = '';
    foreach ($sections as $sec) {
        $body .= '<h2>' . esc($sec[0]) . '</h2>';
        foreach ((array)$sec[1] as $para) {
            $body .= '<p>' . esc(str_replace('{app}', $L['name'] ?? 'App', $para)) . '</p>';
        }
    }

    return <<<HTML
<!DOCTYPE html>
<html lang="{$lang}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{$title} — {$name}</title>
<meta name="robots" content="index,follow">
<style>
  :root{--accent:{$accent}}
  *{box-sizing:border-box;margin:0;padding:0}
  body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
       background:#f6f7fb;color:#1c2230;line-height:1.7;padding:0 20px}
  .wrap{max-width:760px;margin:0 auto;padding:56px 0 80px}
  .brand{display:inline-flex;align-items:center;gap:10px;font-weight:800;font-size:20px;
         color:var(--accent);text-decoration:none;margin-bottom:8px}
  .brand span{width:14px;height:14px;border-radius:5px;background:var(--accent)}
  h1{font-size:30px;margin:14px 0 4px;letter-spacing:-.02em}
  .updated{color:#8a93a6;font-size:14px;margin-bottom:36px}
  h2{font-size:18px;margin:30px 0 8px;letter-spacing:-.01em}
  p{margin:8px 0;color:#3a4356}
  a{color:var(--accent)}
  footer{margin-top:48px;padding-top:22px;border-top:1px solid #e4e7ef;color:#8a93a6;font-size:13px;
         display:flex;gap:18px;flex-wrap:wrap}
</style>
</head>
<body>
  <div class="wrap">
    <a class="brand" href="index.php"><span></span>{$name}</a>
    <h1>{$title}</h1>
    <p class="updated">{$T['updated']} {$year}</p>
    {$body}
    <footer>
      <a href="index.php">{$T['home']}</a>
      <a href="privacy.html">{$T['privacy_title']}</a>
      <a href="terms.html">{$T['terms_title']}</a>
      <span>© {$year} {$name}</span>
    </footer>
  </div>
</body>
</html>
HTML;
}

function legal_strings(string $lang): array {
    $en = [
        'privacy_title' => 'Privacy Policy',
        'terms_title'   => 'Terms of Use',
        'updated'       => 'Last updated',
        'home'          => 'Home',
        'privacy' => [
            ['Introduction', ['{app} ("we", "us") respects your privacy. This policy explains what information we collect when you use our application and website, how we use it, and the choices you have.']],
            ['Information we collect', ['We may collect basic device information, usage analytics, and any information you voluntarily provide, such as an email address for support. We do not sell your personal data.']],
            ['How we use information', ['We use the information to operate and improve the app, provide customer support, ensure security, and comply with legal obligations.']],
            ['Cookies & analytics', ['Our website may use cookies and third-party analytics to understand traffic and improve performance. You can disable cookies in your browser settings.']],
            ['Data sharing', ['We share data only with service providers who help us operate the app, or when required by law. These providers are bound by confidentiality obligations.']],
            ['Your rights', ['Depending on your region, you may request access, correction, or deletion of your personal data by contacting us.']],
            ['Children', ['Our app is intended for general audiences and is not directed at children under the age required by local law.']],
            ['Contact', ['For any privacy questions, contact us through the support link on our website.']],
        ],
        'terms' => [
            ['Acceptance of terms', ['By downloading or using {app}, you agree to these Terms of Use. If you do not agree, please do not use the app.']],
            ['Use of the app', ['You agree to use the app for lawful purposes only and not to misuse, copy, or interfere with its normal operation.']],
            ['Intellectual property', ['All content, trademarks, and software associated with {app} are owned by us or our licensors and are protected by applicable law.']],
            ['Disclaimer', ['The app is provided "as is" without warranties of any kind. We do not guarantee uninterrupted or error-free operation.']],
            ['Limitation of liability', ['To the maximum extent permitted by law, we are not liable for any indirect or consequential damages arising from your use of the app.']],
            ['Changes', ['We may update these terms from time to time. Continued use of the app after changes constitutes acceptance of the revised terms.']],
            ['Contact', ['Questions about these terms can be sent through the support link on our website.']],
        ],
    ];
    $es = [
        'privacy_title' => 'Política de Privacidad',
        'terms_title'   => 'Términos de Uso',
        'updated'       => 'Última actualización',
        'home'          => 'Inicio',
        'privacy' => [
            ['Introducción', ['{app} ("nosotros") respeta tu privacidad. Esta política explica qué información recopilamos cuando usas nuestra aplicación y sitio web, cómo la usamos y las opciones que tienes.']],
            ['Información que recopilamos', ['Podemos recopilar información básica del dispositivo, analíticas de uso y la información que nos proporciones voluntariamente, como un correo para soporte. No vendemos tus datos personales.']],
            ['Cómo usamos la información', ['Usamos la información para operar y mejorar la app, brindar soporte, garantizar la seguridad y cumplir obligaciones legales.']],
            ['Cookies y analíticas', ['Nuestro sitio puede usar cookies y analíticas de terceros para entender el tráfico y mejorar el rendimiento. Puedes desactivar las cookies en tu navegador.']],
            ['Compartir datos', ['Compartimos datos solo con proveedores que nos ayudan a operar la app o cuando la ley lo exige. Estos proveedores están sujetos a obligaciones de confidencialidad.']],
            ['Tus derechos', ['Según tu región, puedes solicitar acceso, corrección o eliminación de tus datos personales contactándonos.']],
            ['Menores', ['Nuestra app está dirigida al público general y no a menores de la edad requerida por la ley local.']],
            ['Contacto', ['Para cualquier consulta de privacidad, contáctanos mediante el enlace de soporte en nuestro sitio.']],
        ],
        'terms' => [
            ['Aceptación de los términos', ['Al descargar o usar {app}, aceptas estos Términos de Uso. Si no estás de acuerdo, no uses la app.']],
            ['Uso de la app', ['Aceptas usar la app solo con fines legales y no hacer mal uso, copiar o interferir con su funcionamiento normal.']],
            ['Propiedad intelectual', ['Todo el contenido, marcas y software asociados a {app} son propiedad nuestra o de nuestros licenciantes y están protegidos por la ley.']],
            ['Descargo de responsabilidad', ['La app se ofrece "tal cual" sin garantías de ningún tipo. No garantizamos un funcionamiento ininterrumpido o sin errores.']],
            ['Limitación de responsabilidad', ['En la máxima medida permitida por la ley, no somos responsables de daños indirectos o consecuentes derivados del uso de la app.']],
            ['Cambios', ['Podemos actualizar estos términos ocasionalmente. El uso continuado tras los cambios implica la aceptación de los términos revisados.']],
            ['Contacto', ['Las preguntas sobre estos términos pueden enviarse mediante el enlace de soporte en nuestro sitio.']],
        ],
    ];
    $pt = [
        'privacy_title' => 'Política de Privacidade',
        'terms_title'   => 'Termos de Uso',
        'updated'       => 'Última atualização',
        'home'          => 'Início',
        'privacy' => [
            ['Introdução', ['{app} ("nós") respeita sua privacidade. Esta política explica quais informações coletamos quando você usa nosso aplicativo e site, como as usamos e as opções que você tem.']],
            ['Informações que coletamos', ['Podemos coletar informações básicas do dispositivo, análises de uso e informações que você fornecer voluntariamente, como um e-mail de suporte. Não vendemos seus dados pessoais.']],
            ['Como usamos as informações', ['Usamos as informações para operar e melhorar o app, oferecer suporte, garantir segurança e cumprir obrigações legais.']],
            ['Cookies e análises', ['Nosso site pode usar cookies e análises de terceiros para entender o tráfego e melhorar o desempenho. Você pode desativar os cookies no navegador.']],
            ['Compartilhamento de dados', ['Compartilhamos dados apenas com prestadores que nos ajudam a operar o app ou quando exigido por lei. Esses prestadores têm obrigações de confidencialidade.']],
            ['Seus direitos', ['Dependendo da sua região, você pode solicitar acesso, correção ou exclusão dos seus dados pessoais entrando em contato conosco.']],
            ['Crianças', ['Nosso app é destinado ao público geral e não a crianças abaixo da idade exigida pela lei local.']],
            ['Contato', ['Para questões de privacidade, entre em contato pelo link de suporte em nosso site.']],
        ],
        'terms' => [
            ['Aceitação dos termos', ['Ao baixar ou usar {app}, você concorda com estes Termos de Uso. Se não concordar, não use o app.']],
            ['Uso do app', ['Você concorda em usar o app apenas para fins legais e em não fazer uso indevido, copiar ou interferir no seu funcionamento normal.']],
            ['Propriedade intelectual', ['Todo o conteúdo, marcas e software associados ao {app} pertencem a nós ou aos nossos licenciadores e são protegidos por lei.']],
            ['Isenção de responsabilidade', ['O app é fornecido "como está", sem garantias de qualquer tipo. Não garantimos operação ininterrupta ou livre de erros.']],
            ['Limitação de responsabilidade', ['Na máxima extensão permitida por lei, não somos responsáveis por danos indiretos ou consequentes decorrentes do uso do app.']],
            ['Alterações', ['Podemos atualizar estes termos periodicamente. O uso continuado após as alterações constitui aceitação dos termos revisados.']],
            ['Contato', ['Dúvidas sobre estes termos podem ser enviadas pelo link de suporte em nosso site.']],
        ],
    ];
    return ['en' => $en, 'es' => $es, 'pt' => $pt][$lang] ?? $en;
}
