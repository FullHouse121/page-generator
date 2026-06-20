<?php
/** Template: Editorial — clean light, magazine big-type, large screenshots. */
/** @var array $L */
$S = $L['S'];
$accent = attr($L['accent']);
$shots = $L['screenshots'];
?><!DOCTYPE html>
<html lang="<?= attr($L['lang']) ?>" dir="<?= attr($L['dir'] ?? 'ltr') ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($L['name']) ?> — <?= esc($L['tagline'] ?: $S['get']) ?></title>
<meta name="description" content="<?= attr($L['tagline'] ?: $L['description']) ?>">
<meta property="og:title" content="<?= attr($L['name']) ?>">
<meta property="og:image" content="<?= attr($L['icon']) ?>">
<link rel="icon" href="<?= attr($L['icon']) ?>">
<link rel="preload" as="image" href="<?= attr($L['icon']) ?>" fetchpriority="high">
<style>
:root{--accent:<?= $accent ?>;--bg:#fbfbfa;--paper:#fff;--text:#16181d;--muted:#6b7280;--line:#e9e9e6}
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Helvetica,Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.6;-webkit-font-smoothing:antialiased}
.wrap{max-width:1000px;margin:0 auto;padding:0 28px}
a{color:inherit;text-decoration:none}
.btn{display:inline-flex;align-items:center;gap:9px;background:var(--text);color:var(--paper);font-weight:600;padding:15px 30px;border-radius:980px;font-size:15px;transition:transform .15s,opacity .15s}
.btn:hover{transform:translateY(-1px);opacity:.9}
.btn-line{background:transparent;color:var(--text);border:1px solid var(--text)}
nav{height:74px;display:flex;align-items:center;justify-content:space-between;max-width:1000px;margin:0 auto;padding:0 28px;border-bottom:1px solid var(--line)}
.logo{display:flex;gap:11px;align-items:center;font-weight:700;font-size:18px;letter-spacing:-.01em}
.logo img{width:30px;height:30px;border-radius:8px}
.nav-link{font-size:14px;color:var(--muted)}
.hero{padding:84px 0 30px;max-width:880px}
.eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--accent)}
h1{font-size:clamp(44px,8vw,84px);line-height:.98;letter-spacing:-.035em;font-weight:800;margin:20px 0 22px}
.lead{font-size:clamp(19px,2.4vw,24px);color:var(--muted);max-width:620px;margin-bottom:30px;line-height:1.45}
.cta-row{display:flex;gap:14px;flex-wrap:wrap;align-items:center}
.meta-row{display:flex;gap:40px;margin-top:46px;flex-wrap:wrap}
.meta-row div b{font-size:26px;font-weight:800;letter-spacing:-.02em;display:block}
.meta-row div span{color:var(--muted);font-size:13px}
.showcase{margin:70px 0;display:flex;gap:22px;overflow-x:auto;padding-bottom:10px;scroll-snap-type:x mandatory}
.showcase img{height:560px;aspect-ratio:9/19;object-fit:cover;border-radius:20px;border:1px solid var(--line);box-shadow:0 40px 70px -44px rgba(0,0,0,.4);flex:0 0 auto;scroll-snap-align:center;transition:transform .25s ease}
.showcase img:hover{transform:translateY(-4px)}
.feat{display:grid;grid-template-columns:repeat(3,1fr);gap:40px;padding:70px 0;border-top:1px solid var(--line)}
.feat .ic{color:var(--accent);margin-bottom:16px;transition:transform .2s ease}
.feat>div:hover .ic{transform:translateY(-3px)}
.feat h3{font-size:22px;letter-spacing:-.02em;margin-bottom:8px}
.feat p{color:var(--muted);font-size:15.5px}
.quote{padding:80px 0;border-top:1px solid var(--line);max-width:820px}
.quote p{font-size:clamp(26px,3.6vw,40px);font-weight:600;letter-spacing:-.02em;line-height:1.25;margin-bottom:20px}
.quote .who{color:var(--muted);font-size:15px;display:flex;align-items:center;gap:10px}
.cta{padding:90px 0;text-align:center;border-top:1px solid var(--line)}
.cta h2{font-size:clamp(34px,5vw,60px);font-weight:800;letter-spacing:-.03em;margin-bottom:24px}
footer{padding:36px 0;border-top:1px solid var(--line);color:var(--muted);font-size:13.5px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:12px}
footer a{margin-left:18px}footer a:hover{color:var(--text)}
@media(max-width:760px){.feat{grid-template-columns:1fr;gap:30px}.showcase img{height:440px}}
</style>
</head>
<body>
<nav>
  <a class="logo" href="#"><img src="<?= attr($L['icon']) ?>" alt=""><?= esc($L['name']) ?></a>
  <a class="nav-link" href="<?= attr($L['cta_url']) ?>"><?= esc($L['cta_text']) ?> →</a>
</nav>

<header class="hero wrap">
  <span class="eyebrow"><?= svg_icon('sparkle',14) ?> <?= esc($L['category']) ?></span>
  <h1><?= esc($L['tagline'] ?: $L['name']) ?></h1>
  <p class="lead"><?= esc($L['description'] ?: $S['cta_sub']) ?></p>
  <div class="cta-row">
    <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= svg_icon('down',18) ?> <?= esc($L['cta_text']) ?></a>
    <a class="btn btn-line" href="#features"><?= esc($S['features']) ?></a>
  </div>
  <div class="meta-row">
    <div><b><?= esc(number_format($L['rating'],1)) ?>★</b><span><?= esc($L['rating_count']) ?> <?= esc($S['reviews']) ?></span></div>
    <?php if($L['downloads']): ?><div><b><?= esc($L['downloads']) ?></b><span><?= esc($S['downloads']) ?></span></div><?php endif; ?>
    <?php if($L['developer']): ?><div><b style="font-size:18px;margin-top:5px"><?= esc($L['developer']) ?></b><span><?= esc($S['verified']) ?></span></div><?php endif; ?>
  </div>
</header>

<div class="wrap reveal"><div class="showcase">
  <?php foreach($shots as $i=>$s): ?><img src="<?= attr($s) ?>" alt="<?= attr($L['name']) ?>" <?= $i===0?'loading="eager" fetchpriority="high"':'loading="lazy"' ?> decoding="async"><?php endforeach; ?>
</div></div>

<section id="features" class="reveal"><div class="wrap"><div class="feat">
  <?php foreach(feature_points($S) as $c): ?>
  <div><div class="ic"><?= svg_icon($c[0],30) ?></div><h3><?= esc($c[1]) ?></h3><p><?= esc($c[2]) ?></p></div>
  <?php endforeach; ?>
</div></div></section>

<?php if(!empty($L['reviews'])): $q = $L['reviews'][1] ?? $L['reviews'][0]; ?>
<section class="reveal"><div class="wrap quote">
  <p>“<?= esc($q[2]) ?>”</p>
  <div class="who"><?= stars_svg($q[1],$accent,15) ?> &nbsp;<?= esc($q[0]) ?> · <?= esc($S['verified']) ?></div>
</div></section>
<?php endif; ?>

<section class="cta reveal"><div class="wrap">
  <h2><?= esc($S['get']) ?> <?= esc($L['name']) ?></h2>
  <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= svg_icon('down',18) ?> <?= esc($L['cta_text']) ?></a>
</div></section>

<footer class="wrap">
  <span>© <?= esc($L['year']) ?> <?= esc($L['name']) ?></span>
  <span><a href="<?= attr($L['privacy_url']) ?>"><?= esc($S['privacy']) ?></a><a href="<?= attr($L['terms_url']) ?>"><?= esc($S['terms']) ?></a></span>
</footer>
<?= reveal_script() ?>
</body>
</html>
