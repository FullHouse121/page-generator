<?php
/** Template: Feature Flow — alternating feature blocks + stats + sticky CTA. */
/** @var array $L */
$S = $L['S'];
$accent = attr($L['accent']);
$shots = $L['screenshots'];
// build up to 3 alternating feature blocks from screenshots
// each: [icon, title, body] — localized
$blockCopy = feature_points($S);
if ($L['tagline']) { $blockCopy[0][2] = $L['tagline']; }
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
<?php if(!empty($shots[0])): ?><link rel="preload" as="image" href="<?= attr($shots[0]) ?>" fetchpriority="high"><?php endif; ?>
<style>
:root{--accent:<?= $accent ?>;--bg:#0f1117;--surface:#161925;--text:#eceefb;--muted:#9499ad;--line:rgba(255,255,255,.09)}
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.6;padding-bottom:74px}
.wrap{max-width:1060px;margin:0 auto;padding:0 22px}
a{color:inherit;text-decoration:none}
.btn{display:inline-flex;align-items:center;gap:9px;background:var(--accent);color:#fff;font-weight:700;padding:14px 30px;border-radius:14px;font-size:15px;transition:transform .15s}
.btn:hover{transform:translateY(-2px)}
nav{height:64px;display:flex;align-items:center;justify-content:space-between;max-width:1060px;margin:0 auto;padding:0 22px}
.logo{display:flex;gap:11px;align-items:center;font-weight:800;font-size:18px}
.logo img{width:32px;height:32px;border-radius:9px}
.hero{text-align:center;padding:56px 0 40px}
.pill{display:inline-block;background:var(--surface);border:1px solid var(--line);color:var(--accent);font-weight:700;font-size:12.5px;padding:7px 15px;border-radius:999px;letter-spacing:.05em;text-transform:uppercase}
.hero h1{font-size:clamp(34px,5.4vw,56px);letter-spacing:-.02em;line-height:1.05;margin:18px auto 16px;max-width:760px;font-weight:800}
.hero p{color:var(--muted);font-size:19px;max-width:560px;margin:0 auto 26px}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;max-width:680px;margin:46px auto 0}
.stat{background:var(--surface);border:1px solid var(--line);border-radius:16px;padding:22px 10px;text-align:center}
.stat b{font-size:26px;display:block}.stat span{color:var(--muted);font-size:13px}
.block{display:grid;grid-template-columns:1fr 1fr;gap:46px;align-items:center;padding:60px 0;border-top:1px solid var(--line)}
.block:nth-child(even) .txt{order:2}
.block .ic{width:50px;height:50px;border-radius:14px;background:rgba(255,255,255,.06);color:var(--accent);display:grid;place-items:center;margin-bottom:18px}
.block h2{font-size:clamp(24px,3vw,32px);letter-spacing:-.02em;margin-bottom:12px}
.block p{color:var(--muted);font-size:16.5px;margin-bottom:18px}
.checks{list-style:none;display:grid;gap:10px}
.checks li{display:flex;gap:10px;align-items:flex-start;color:var(--text);font-size:15px}
.checks .c{color:var(--accent);flex:0 0 auto}
.imgwrap{display:flex;justify-content:center}
.imgwrap img{width:260px;aspect-ratio:9/19;object-fit:cover;border-radius:26px;border:1px solid var(--line);box-shadow:0 40px 70px -34px #000}
.tcards{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;padding:24px 0 0}
.tcard{background:var(--surface);border:1px solid var(--line);border-radius:16px;padding:22px}
.tcard p{color:var(--muted);font-size:14.5px;margin-top:10px}
.tcard .who{display:flex;align-items:center;gap:10px;margin-top:14px;font-size:14px}
.av{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#2a2d3a);display:grid;place-items:center;font-weight:800;font-size:14px}
.sticky{position:fixed;left:0;right:0;bottom:0;z-index:40;background:rgba(15,17,23,.92);backdrop-filter:blur(12px);border-top:1px solid var(--line)}
.sticky .wrap{display:flex;align-items:center;justify-content:space-between;height:74px;gap:14px}
.sticky .l{display:flex;align-items:center;gap:12px}
.sticky img{width:42px;height:42px;border-radius:11px}
.sticky b{font-size:15px}.sticky span{color:var(--muted);font-size:12.5px;display:block}
footer{padding:38px 0;color:var(--muted);font-size:14px;border-top:1px solid var(--line)}
footer .wrap{display:flex;justify-content:space-between;flex-wrap:wrap;gap:14px}
footer a{margin-left:18px}footer a:hover{color:var(--text)}
@media(max-width:820px){.block{grid-template-columns:1fr;gap:26px}.block:nth-child(even) .txt{order:0}.stats,.tcards{grid-template-columns:1fr}.imgwrap img{width:220px}}
</style>
</head>
<body>
<nav>
  <a class="logo" href="#"><img src="<?= attr($L['icon']) ?>" alt=""><?= esc($L['name']) ?></a>
  <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= esc($L['cta_text']) ?></a>
</nav>

<header class="hero"><div class="wrap">
  <span class="pill"><?= esc($L['category']) ?></span>
  <h1><?= esc($L['name']) ?></h1>
  <p><?= esc($L['tagline'] ?: $L['description'] ?: $S['cta_sub']) ?></p>
  <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= svg_icon('down',18) ?> <?= esc($L['cta_text']) ?></a>
  <div class="stats">
    <div class="stat"><b><?= esc(number_format($L['rating'],1)) ?>★</b><span><?= esc($S['rating']) ?></span></div>
    <div class="stat"><b><?= esc($L['downloads'] ?: $L['rating_count']) ?></b><span><?= esc($L['downloads']?$S['downloads']:$S['reviews']) ?></span></div>
    <div class="stat"><b><?= esc($S['everyone']) ?></b><span><?= esc($S['age']) ?></span></div>
  </div>
</div></header>

<div class="wrap">
  <?php foreach($blockCopy as $i=>$b): $img=$shots[$i%count($shots)]; ?>
  <section class="block">
    <div class="txt">
      <div class="ic"><?= svg_icon($b[0],24) ?></div>
      <h2><?= esc($b[1]) ?></h2>
      <p><?= esc($b[2]) ?></p>
      <ul class="checks">
        <li><span class="c"><?= svg_icon('check',18) ?></span> <?= esc($S['features']) ?></li>
        <li><span class="c"><?= svg_icon('check',18) ?></span> <?= esc($S['privacy']) ?></li>
        <li><span class="c"><?= svg_icon('check',18) ?></span> <?= esc($S['updated']) ?> <?= esc($L['year']) ?></li>
      </ul>
    </div>
    <div class="imgwrap"><img src="<?= attr($img) ?>" alt="<?= attr($L['name']) ?>" width="260" height="540" <?= $i===0?'loading="eager" fetchpriority="high"':'loading="lazy"' ?> decoding="async"></div>
  </section>
  <?php endforeach; ?>

  <?php if(!empty($L['reviews'])): ?>
  <section style="padding:56px 0;border-top:1px solid var(--line)">
    <h2 style="font-size:clamp(24px,3vw,32px);letter-spacing:-.02em;margin-bottom:6px"><?= esc($S['reviews']) ?></h2>
    <div class="tcards">
      <?php foreach(array_slice($L['reviews'],0,3) as $r): ?>
      <div class="tcard"><?= stars_svg($r[1],'#f5b301',15) ?><p><?= esc($r[2]) ?></p>
        <div class="who"><span class="av"><?= esc(mb_substr($r[0],0,1)) ?></span><div><b><?= esc($r[0]) ?></b><br><span style="color:var(--muted);font-size:12px"><?= esc($S['verified']) ?></span></div></div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>
</div>

<footer><div class="wrap">
  <span>© <?= esc($L['year']) ?> <?= esc($L['name']) ?><?php if($L['developer']): ?> · <?= esc($L['developer']) ?><?php endif; ?></span>
  <span><a href="<?= attr($L['privacy_url']) ?>"><?= esc($S['privacy']) ?></a><a href="<?= attr($L['terms_url']) ?>"><?= esc($S['terms']) ?></a></span>
</div></footer>

<div class="sticky"><div class="wrap">
  <div class="l"><img src="<?= attr($L['icon']) ?>" alt=""><div><b><?= esc($L['name']) ?></b><span><?= esc(number_format($L['rating'],1)) ?>★ · <?= esc($L['category']) ?></span></div></div>
  <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= esc($L['cta_text']) ?></a>
</div></div>
</body>
</html>
