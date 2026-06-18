<?php
/** Template: Minimal — clean light, centered, single CTA. */
/** @var array $L */
$S = $L['S'];
$accent = attr($L['accent']);
$shots = $L['screenshots'];
?><!DOCTYPE html>
<html lang="<?= attr($L['lang']) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($L['name']) ?> — <?= esc($L['tagline'] ?: $S['get']) ?></title>
<meta name="description" content="<?= attr($L['tagline'] ?: $L['description']) ?>">
<meta property="og:title" content="<?= attr($L['name']) ?>">
<meta property="og:description" content="<?= attr($L['tagline'] ?: $L['description']) ?>">
<meta property="og:image" content="<?= attr($L['icon']) ?>">
<link rel="icon" href="<?= attr($L['icon']) ?>">
<style>
:root{--accent:<?= $accent ?>;--text:#0c0d10;--muted:#6b7280;--line:#e8eaef;--soft:#f6f7f9}
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;color:var(--text);background:#fff;line-height:1.6;-webkit-font-smoothing:antialiased}
.wrap{max-width:980px;margin:0 auto;padding:0 22px}
a{color:inherit;text-decoration:none}
.btn{display:inline-flex;align-items:center;gap:9px;background:var(--accent);color:#fff;font-weight:600;padding:15px 34px;border-radius:980px;font-size:16px;transition:opacity .15s,transform .15s}
.btn:hover{transform:translateY(-1px);opacity:.92}
nav{height:60px;display:flex;align-items:center;justify-content:space-between;max-width:980px;margin:0 auto;padding:0 22px;position:sticky;top:0;background:rgba(255,255,255,.8);backdrop-filter:blur(14px);z-index:20}
.logo{display:flex;gap:10px;align-items:center;font-weight:700;font-size:17px}
.logo img{width:30px;height:30px;border-radius:8px}
.nav-link{font-size:15px;color:var(--accent);font-weight:600}
.hero{text-align:center;padding:84px 0 30px}
.icon-xl{width:104px;height:104px;border-radius:24px;margin:0 auto 26px;box-shadow:0 18px 40px -16px rgba(0,0,0,.35);display:block}
h1{font-size:clamp(36px,6vw,60px);letter-spacing:-.03em;line-height:1.04;font-weight:700;margin-bottom:18px}
.sub{font-size:clamp(18px,2.4vw,22px);color:var(--muted);max-width:600px;margin:0 auto 30px}
.meta{display:flex;gap:0;justify-content:center;margin:34px auto 0;max-width:560px;border:1px solid var(--line);border-radius:18px;overflow:hidden}
.meta div{flex:1;padding:18px 10px;border-right:1px solid var(--line)}
.meta div:last-child{border-right:0}
.meta b{display:block;font-size:18px}.meta span{font-size:12.5px;color:var(--muted)}
.shots{display:flex;gap:18px;justify-content:center;padding:60px 0 20px;overflow-x:auto}
.shots img{height:480px;border-radius:30px;border:1px solid var(--line);box-shadow:0 30px 60px -34px rgba(0,0,0,.4);flex:0 0 auto}
.section{padding:70px 0;border-top:1px solid var(--line)}
.section h2{font-size:clamp(26px,3.4vw,34px);letter-spacing:-.02em;text-align:center;margin-bottom:14px}
.section p.lead{text-align:center;color:var(--muted);max-width:620px;margin:0 auto;font-size:17px}
.points{display:grid;grid-template-columns:repeat(3,1fr);gap:26px;margin-top:46px}
.point{text-align:center}
.point .ic{width:54px;height:54px;border-radius:50%;background:var(--soft);color:var(--accent);display:grid;place-items:center;margin:0 auto 14px}
.point h3{font-size:17px;margin-bottom:6px}.point p{color:var(--muted);font-size:14.5px}
.quote{max-width:680px;margin:0 auto;text-align:center}
.quote p{font-size:clamp(20px,2.6vw,26px);letter-spacing:-.01em;line-height:1.4;margin-bottom:18px}
.quote .who{color:var(--muted);font-size:15px}
.cta{text-align:center;padding:80px 0}
.cta h2{font-size:clamp(28px,3.6vw,40px);margin-bottom:22px;letter-spacing:-.02em}
footer{padding:36px 0;border-top:1px solid var(--line);color:var(--muted);font-size:14px;text-align:center}
footer a{margin:0 10px;color:var(--muted)}footer a:hover{color:var(--text)}
@media(max-width:760px){.points{grid-template-columns:1fr}.shots img{height:400px}}
</style>
</head>
<body>
<nav>
  <a class="logo" href="#"><img src="<?= attr($L['icon']) ?>" alt=""><?= esc($L['name']) ?></a>
  <a class="nav-link" href="<?= attr($L['cta_url']) ?>"><?= esc($L['cta_text']) ?></a>
</nav>

<header class="hero"><div class="wrap">
  <img class="icon-xl" src="<?= attr($L['icon']) ?>" alt="<?= attr($L['name']) ?>">
  <h1><?= esc($L['name']) ?></h1>
  <p class="sub"><?= esc($L['tagline'] ?: $L['description'] ?: $S['cta_sub']) ?></p>
  <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= svg_icon('down',18) ?> <?= esc($L['cta_text']) ?></a>
  <div class="meta">
    <div><b><?= esc(number_format($L['rating'],1)) ?>★</b><span><?= esc($S['rating']) ?></span></div>
    <?php if($L['downloads']): ?><div><b><?= esc($L['downloads']) ?></b><span><?= esc($S['downloads']) ?></span></div><?php endif; ?>
    <div><b><?= esc($L['category']) ?></b><span><?= esc($S['everyone']) ?></span></div>
  </div>
</div></header>

<div class="wrap"><div class="shots">
  <?php foreach($shots as $s): ?><img src="<?= attr($s) ?>" alt="<?= attr($L['name']) ?>"><?php endforeach; ?>
</div></div>

<section class="section"><div class="wrap">
  <h2><?= esc($S['why']) ?></h2>
  <p class="lead"><?= esc($L['description'] ?: $S['join']) ?></p>
  <div class="points">
    <div class="point"><div class="ic"><?= svg_icon('bolt',24) ?></div><h3><?= esc($S['features']) ?></h3><p>Effortless, fast and a joy to use every day.</p></div>
    <div class="point"><div class="ic"><?= svg_icon('lock',22) ?></div><h3><?= esc($S['privacy']) ?></h3><p>Private by design — your data stays yours.</p></div>
    <div class="point"><div class="ic"><?= svg_icon('heart',22) ?></div><h3><?= esc($S['reviews']) ?></h3><p><?= esc($L['rating_count']) ?> people rate it <?= esc(number_format($L['rating'],1)) ?>★.</p></div>
  </div>
</div></section>

<section class="section"><div class="wrap quote">
  <?php $q=$L['reviews'][1]; ?>
  <p>“<?= esc($q[2]) ?>”</p>
  <div class="who"><?= stars_svg($q[1],$accent,15) ?> &nbsp;<?= esc($q[0]) ?> · <?= esc($S['verified']) ?></div>
</div></section>

<section class="cta"><div class="wrap">
  <h2><?= esc($S['get']) ?> <?= esc($L['name']) ?></h2>
  <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= svg_icon('down',18) ?> <?= esc($L['cta_text']) ?></a>
</div></section>

<footer>
  © <?= esc($L['year']) ?> <?= esc($L['name']) ?>
  <br>
  <a href="<?= attr($L['privacy_url']) ?>"><?= esc($S['privacy']) ?></a> ·
  <a href="<?= attr($L['terms_url']) ?>"><?= esc($S['terms']) ?></a>
</footer>
</body>
</html>
