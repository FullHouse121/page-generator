<?php
/** Template: Bold Dark — large type, glow, store badges, FAQ accordion. */
/** @var array $L */
$S = $L['S'];
$accent = attr($L['accent']);
$shots = $L['screenshots'];
$faq = [
  [$S['get'].' '.$L['name'].'?', $S['cta_sub']],
  [$S['privacy'].'?', $S['b_secure']],
  [$S['rating'].'?', number_format($L['rating'],1).' / 5 · '.$L['rating_count'].' '.$S['reviews']],
];
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
:root{--accent:<?= $accent ?>;--bg:#070710;--surface:#0e0e1c;--card:#13131f;--text:#f3f3fb;--muted:#8b8da6;--line:rgba(255,255,255,.08)}
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.6;overflow-x:hidden}
.wrap{max-width:1080px;margin:0 auto;padding:0 22px}
a{color:inherit;text-decoration:none}
.btn{display:inline-flex;align-items:center;gap:9px;background:var(--accent);color:#fff;font-weight:800;padding:16px 34px;border-radius:16px;font-size:16px;
     box-shadow:0 0 0 0 var(--accent);transition:transform .15s,box-shadow .25s}
.btn:hover{transform:translateY(-2px);box-shadow:0 0 40px -6px var(--accent)}
nav{height:70px;display:flex;align-items:center;justify-content:space-between;max-width:1080px;margin:0 auto;padding:0 22px}
.logo{display:flex;gap:11px;align-items:center;font-weight:900;font-size:19px;letter-spacing:-.01em}
.logo img{width:34px;height:34px;border-radius:10px}
.hero{position:relative;text-align:center;padding:74px 0 56px;overflow:hidden}
.glow{position:absolute;inset:0;z-index:-1;pointer-events:none}
.glow::before,.glow::after{content:"";position:absolute;border-radius:50%;filter:blur(90px);opacity:.5}
.glow::before{width:520px;height:520px;background:var(--accent);top:-160px;left:-80px}
.glow::after{width:460px;height:460px;background:#6d28d9;bottom:-200px;right:-60px;opacity:.4}
.kick{display:inline-flex;gap:8px;align-items:center;font-weight:700;font-size:13px;color:var(--text);background:rgba(255,255,255,.06);
      border:1px solid var(--line);padding:8px 16px;border-radius:999px}
.hero h1{font-size:clamp(40px,7vw,76px);font-weight:900;letter-spacing:-.03em;line-height:.98;margin:22px auto 18px;max-width:880px;
  background:linear-gradient(180deg,#fff,#b9bce0);-webkit-background-clip:text;background-clip:text;color:transparent}
.hero p{color:var(--muted);font-size:20px;max-width:600px;margin:0 auto 30px}
.lpf-badges{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:8px}
.lpf-badge{display:inline-flex;align-items:center;gap:10px;background:#000;border:1px solid var(--line);color:#fff;padding:10px 18px;border-radius:13px}
.lpf-badge small{display:block;font-size:10px;color:#bdbfd6;text-transform:uppercase;letter-spacing:.05em}
.lpf-badge strong{font-size:16px}
.heroimg{margin:54px auto 0;max-width:920px;display:flex;gap:18px;justify-content:center;perspective:1200px}
.heroimg img{width:230px;aspect-ratio:9/19;object-fit:cover;border-radius:30px;border:1px solid var(--line);box-shadow:0 50px 90px -40px #000}
.heroimg img:nth-child(2){transform:translateY(-26px) scale(1.04)}
section{padding:70px 0}
.shead{text-align:center;max-width:640px;margin:0 auto 44px}
.shead h2{font-size:clamp(28px,4vw,44px);font-weight:800;letter-spacing:-.02em}
.shead p{color:var(--muted);font-size:17px;margin-top:10px}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.card{background:var(--surface);border:1px solid var(--line);border-radius:20px;padding:28px}
.card .ic{width:52px;height:52px;border-radius:14px;display:grid;place-items:center;background:rgba(255,255,255,.05);color:var(--accent);margin-bottom:16px}
.card h3{font-size:19px;margin-bottom:8px}.card p{color:var(--muted);font-size:14.5px}
.faq{max-width:760px;margin:0 auto}
.q{border:1px solid var(--line);background:var(--surface);border-radius:14px;margin-bottom:12px;overflow:hidden}
.q summary{list-style:none;cursor:pointer;padding:20px 22px;font-weight:700;display:flex;justify-content:space-between;align-items:center}
.q summary::-webkit-details-marker{display:none}
.q summary::after{content:"+";font-size:22px;color:var(--accent)}
.q[open] summary::after{content:"–"}
.q .a{padding:0 22px 20px;color:var(--muted);font-size:15px}
.final{text-align:center;background:radial-gradient(closest-side,rgba(125,92,255,.18),transparent),var(--surface);border:1px solid var(--line);border-radius:30px;padding:64px 24px}
.final h2{font-size:clamp(30px,4.4vw,52px);font-weight:900;letter-spacing:-.02em;margin-bottom:16px}
.final p{color:var(--muted);margin-bottom:28px}
footer{padding:40px 0;border-top:1px solid var(--line);color:var(--muted);font-size:14px}
footer .wrap{display:flex;justify-content:space-between;flex-wrap:wrap;gap:14px}
footer a{margin-left:18px}footer a:hover{color:var(--text)}
@media(max-width:820px){.grid{grid-template-columns:1fr}.heroimg img{width:42vw}.heroimg img:nth-child(3){display:none}}
</style>
</head>
<body>
<nav>
  <a class="logo" href="#"><img src="<?= attr($L['icon']) ?>" alt=""><?= esc($L['name']) ?></a>
  <a class="btn" style="padding:11px 22px;font-size:14px" href="<?= attr($L['cta_url']) ?>"><?= esc($L['cta_text']) ?></a>
</nav>

<header class="hero">
  <div class="glow"></div>
  <div class="wrap">
    <span class="kick"><?= stars_svg($L['rating'],'#f5b301',14) ?> <?= esc(number_format($L['rating'],1)) ?> · <?= esc($L['rating_count']) ?> <?= esc($S['reviews']) ?></span>
    <h1><?= esc($L['tagline'] ?: $L['name']) ?></h1>
    <p><?= esc($L['description'] ?: $S['cta_sub']) ?></p>
    <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= svg_icon('down',18) ?> <?= esc($L['cta_text']) ?></a>
    <?= store_badges($L['cta_url'], $L['lang']) ?>
    <div class="heroimg">
      <?php for($i=0;$i<min(3,count($shots));$i++): ?><img src="<?= attr($shots[$i]) ?>" alt="<?= attr($L['name']) ?>" width="230" height="480" <?= $i===0?'loading="eager" fetchpriority="high"':'loading="lazy"' ?> decoding="async"><?php endfor; ?>
    </div>
  </div>
</header>

<section><div class="wrap">
  <div class="shead"><h2><?= esc($S['why']) ?></h2><p><?= esc($S['join']) ?></p></div>
  <div class="grid">
    <div class="card"><div class="ic"><?= svg_icon('bolt',24) ?></div><h3><?= esc($S['features']) ?></h3><p><?= esc($S['b_fast']) ?></p></div>
    <div class="card"><div class="ic"><?= svg_icon('shield',24) ?></div><h3><?= esc($S['privacy']) ?></h3><p><?= esc($S['b_secure']) ?></p></div>
    <div class="card"><div class="ic"><?= svg_icon('sparkle',24) ?></div><h3><?= esc($S['what_new']) ?></h3><p><?= esc($S['b_updates']) ?></p></div>
  </div>
</div></section>

<section style="background:var(--surface)"><div class="wrap">
  <div class="shead"><h2><?= esc($S['faq']) ?></h2></div>
  <div class="faq">
    <?php foreach($faq as $f): ?>
    <details class="q"><summary><?= esc($f[0]) ?></summary><div class="a"><?= esc($f[1]) ?></div></details>
    <?php endforeach; ?>
  </div>
</div></section>

<section><div class="wrap"><div class="final">
  <h2><?= esc($S['get']) ?> <?= esc($L['name']) ?></h2>
  <p><?= esc($S['cta_sub']) ?></p>
  <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= svg_icon('down',18) ?> <?= esc($L['cta_text']) ?></a>
</div></div></section>

<footer><div class="wrap">
  <span>© <?= esc($L['year']) ?> <?= esc($L['name']) ?><?php if($L['developer']): ?> · <?= esc($L['developer']) ?><?php endif; ?></span>
  <span><a href="<?= attr($L['privacy_url']) ?>"><?= esc($S['privacy']) ?></a><a href="<?= attr($L['terms_url']) ?>"><?= esc($S['terms']) ?></a></span>
</div></footer>
</body>
</html>
