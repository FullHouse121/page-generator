<?php
/** Template: Spotlight — gradient hero + phone mockups + features + reviews. */
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
<meta property="og:type" content="website">
<link rel="icon" href="<?= attr($L['icon']) ?>">
<?php if(!empty($shots[0])): ?><link rel="preload" as="image" href="<?= attr($shots[0]) ?>" fetchpriority="high"><?php endif; ?>
<style>
:root{--accent:<?= $accent ?>;--bg:#0a0b14;--surface:#12131f;--card:#181a29;--text:#eef0f7;--muted:#9aa0b4;--border:rgba(255,255,255,.08)}
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.6;overflow-x:hidden}
.wrap{max-width:1080px;margin:0 auto;padding:0 22px}
a{color:inherit;text-decoration:none}
.btn{display:inline-flex;align-items:center;gap:9px;background:var(--accent);color:#fff;font-weight:700;
     padding:15px 30px;border-radius:14px;font-size:16px;border:0;cursor:pointer;transition:transform .15s,box-shadow .15s;
     box-shadow:0 10px 30px -8px var(--accent)}
.btn:hover{transform:translateY(-2px);box-shadow:0 16px 40px -8px var(--accent)}
.btn-ghost{background:rgba(255,255,255,.06);box-shadow:none;border:1px solid var(--border)}
/* nav */
nav{position:sticky;top:0;z-index:30;backdrop-filter:blur(12px);background:rgba(10,11,20,.7);border-bottom:1px solid var(--border)}
nav .wrap{display:flex;align-items:center;justify-content:space-between;height:66px}
.logo{display:flex;align-items:center;gap:11px;font-weight:800;font-size:18px}
.logo img{width:34px;height:34px;border-radius:9px}
.nav-cta{padding:10px 20px;border-radius:11px;background:var(--accent);font-weight:700;font-size:14px}
/* hero */
.hero{position:relative;padding:70px 0 40px;overflow:hidden}
.hero::before{content:"";position:absolute;top:-200px;left:50%;transform:translateX(-50%);width:900px;height:600px;
  background:radial-gradient(closest-side,var(--accent),transparent);opacity:.22;filter:blur(20px);z-index:-1}
.hero-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:36px;align-items:center}
.eyebrow{display:inline-flex;align-items:center;gap:8px;color:var(--accent);font-weight:700;font-size:13px;
  text-transform:uppercase;letter-spacing:.08em;background:rgba(255,255,255,.05);padding:7px 14px;border-radius:999px;border:1px solid var(--border)}
h1{font-size:clamp(34px,5vw,52px);line-height:1.05;letter-spacing:-.02em;margin:20px 0 16px;font-weight:800}
.lead{font-size:18px;color:var(--muted);max-width:520px;margin-bottom:26px}
.cta-row{display:flex;gap:14px;flex-wrap:wrap;align-items:center}
.proof{display:flex;gap:26px;margin-top:30px;flex-wrap:wrap}
.proof div{display:flex;flex-direction:column}
.proof b{font-size:22px}.proof span{color:var(--muted);font-size:13px}
.lpf-stars svg{margin-right:1px}
/* phone */
.phone{position:relative;margin:0 auto;width:280px;height:570px;background:#05060c;border-radius:42px;
  border:10px solid #1d2030;box-shadow:0 40px 80px -30px rgba(0,0,0,.8);overflow:hidden}
.phone::before{content:"";position:absolute;top:14px;left:50%;transform:translateX(-50%);width:110px;height:24px;background:#05060c;border-radius:0 0 16px 16px;z-index:2}
.phone img{width:100%;height:100%;object-fit:cover}
/* sections */
section{padding:64px 0}
.shead{text-align:center;max-width:620px;margin:0 auto 44px}
.shead h2{font-size:clamp(26px,3.4vw,38px);letter-spacing:-.02em;margin-bottom:12px}
.shead p{color:var(--muted);font-size:17px}
.feat{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}
.fcard{background:var(--card);border:1px solid var(--border);border-radius:18px;padding:26px}
.fcard .ic{width:48px;height:48px;border-radius:13px;display:grid;place-items:center;background:rgba(255,255,255,.06);color:var(--accent);margin-bottom:16px}
.fcard h3{font-size:18px;margin-bottom:7px}.fcard p{color:var(--muted);font-size:14.5px}
.gallery{display:flex;gap:16px;overflow-x:auto;padding:6px 2px 16px;scroll-snap-type:x mandatory}
.gallery img{height:440px;aspect-ratio:9/19;object-fit:cover;border-radius:22px;border:1px solid var(--border);scroll-snap-align:center;flex:0 0 auto;background:var(--card)}
.reviews{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;max-width:860px;margin:0 auto}
.rcard{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:22px}
.rcard .top{display:flex;align-items:center;gap:12px;margin-bottom:10px}
.av{width:42px;height:42px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#222);display:grid;place-items:center;font-weight:800}
.rcard p{color:var(--muted);font-size:14.5px}
.final{text-align:center;background:linear-gradient(180deg,var(--card),var(--surface));border:1px solid var(--border);border-radius:28px;padding:56px 24px;margin:20px 0}
.final h2{font-size:clamp(26px,3.6vw,40px);margin-bottom:14px}
.final p{color:var(--muted);margin-bottom:26px}
footer{border-top:1px solid var(--border);padding:34px 0;color:var(--muted);font-size:14px}
footer .wrap{display:flex;justify-content:space-between;flex-wrap:wrap;gap:16px;align-items:center}
footer a{margin-left:18px}footer a:hover{color:var(--text)}
@media(max-width:860px){.hero-grid{grid-template-columns:1fr;text-align:center}.lead{margin:0 auto 26px}.cta-row,.proof{justify-content:center}.feat{grid-template-columns:1fr}.reviews{grid-template-columns:1fr}.phone{margin-top:20px}}
</style>
</head>
<body>
<nav><div class="wrap">
  <a class="logo" href="#"><img src="<?= attr($L['icon']) ?>" alt=""><?= esc($L['name']) ?></a>
  <a class="nav-cta" href="<?= attr($L['cta_url']) ?>"><?= esc($L['cta_text']) ?></a>
</div></nav>

<header class="hero"><div class="wrap hero-grid">
  <div>
    <span class="eyebrow"><?= svg_icon('sparkle',15) ?> <?= esc($L['category']) ?></span>
    <h1><?= esc($L['name']) ?></h1>
    <p class="lead"><?= esc($L['tagline'] ?: $L['description'] ?: $S['cta_sub']) ?></p>
    <div class="cta-row">
      <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= svg_icon('down',18) ?> <?= esc($L['cta_text']) ?></a>
      <a class="btn btn-ghost" href="#features"><?= esc($S['features']) ?></a>
    </div>
    <div class="proof">
      <div><b><?= esc(number_format($L['rating'],1)) ?> <?= stars_svg($L['rating'],'#f5b301',15) ?></b><span><?= esc($L['rating_count']) ?> <?= esc($S['reviews']) ?></span></div>
      <?php if($L['downloads']): ?><div><b><?= esc($L['downloads']) ?></b><span><?= esc($S['downloads']) ?></span></div><?php endif; ?>
      <?php if($L['developer']): ?><div><b><?= esc($L['developer']) ?></b><span><?= esc($S['verified']) ?></span></div><?php endif; ?>
    </div>
  </div>
  <div class="phone"><img src="<?= attr($shots[0]) ?>" alt="<?= attr($L['name']) ?>" width="280" height="570" loading="eager" fetchpriority="high" decoding="async"></div>
</div></header>

<section id="features"><div class="wrap">
  <div class="shead"><h2><?= esc($S['why']) ?></h2><p><?= esc($L['tagline'] ?: $S['join']) ?></p></div>
  <div class="feat">
    <?php foreach(feature_points($S) as $c): ?>
    <div class="fcard"><div class="ic"><?= svg_icon($c[0],24) ?></div><h3><?= esc($c[1]) ?></h3><p><?= esc($c[2]) ?></p></div>
    <?php endforeach; ?>
  </div>
</div></section>

<section style="background:var(--surface)"><div class="wrap">
  <div class="shead"><h2><?= esc($S['screens']) ?></h2></div>
  <div class="gallery">
    <?php foreach($shots as $s): ?><img src="<?= attr($s) ?>" alt="<?= attr($L['name']) ?>" loading="lazy" decoding="async"><?php endforeach; ?>
  </div>
</div></section>

<section><div class="wrap">
  <div class="shead"><h2><?= esc($S['reviews']) ?></h2><p><?= esc(number_format($L['rating'],1)) ?> / 5 · <?= esc($L['rating_count']) ?></p></div>
  <div class="reviews">
    <?php foreach($L['reviews'] as $r): ?>
    <div class="rcard"><div class="top"><div class="av"><?= esc(mb_substr($r[0],0,1)) ?></div>
      <div><strong><?= esc($r[0]) ?></strong><br><?= stars_svg($r[1],'#f5b301',14) ?></div></div>
      <p><?= esc($r[2]) ?></p></div>
    <?php endforeach; ?>
  </div>
</div></section>

<section><div class="wrap"><div class="final">
  <h2><?= esc($S['get']) ?> <?= esc($L['name']) ?></h2>
  <p><?= esc($S['cta_sub']) ?></p>
  <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= svg_icon('down',18) ?> <?= esc($L['cta_text']) ?></a>
</div></div></section>

<footer><div class="wrap">
  <div>© <?= esc($L['year']) ?> <?= esc($L['name']) ?><?php if($L['developer']): ?> · <?= esc($L['developer']) ?><?php endif; ?></div>
  <div>
    <a href="<?= attr($L['privacy_url']) ?>"><?= esc($S['privacy']) ?></a>
    <a href="<?= attr($L['terms_url']) ?>"><?= esc($S['terms']) ?></a>
  </div>
</div></footer>
</body>
</html>
