<?php
/** Template: Aurora — premium glassmorphism, aurora gradient hero, glass cards. */
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
<?php if(!empty($shots[0])): ?><link rel="preload" as="image" href="<?= attr($shots[0]) ?>" fetchpriority="high"><?php endif; ?>
<style>
:root{--accent:<?= $accent ?>;--bg:#07070d;--text:#f4f4fb;--muted:#a4a6c0;--glass:rgba(255,255,255,.05);--line:rgba(255,255,255,.1)}
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.6;overflow-x:hidden;position:relative}
body::before{content:"";position:fixed;inset:0;z-index:-2;pointer-events:none;
  background:radial-gradient(50% 40% at 18% 8%,var(--accent),transparent 60%),radial-gradient(45% 40% at 85% 12%,#49e0c4,transparent 60%),radial-gradient(55% 45% at 60% 100%,#64b8ff,transparent 60%);opacity:.30;filter:blur(10px)}
.wrap{max-width:1080px;margin:0 auto;padding:0 22px}
a{color:inherit;text-decoration:none}
.glass{background:var(--glass);border:1px solid var(--line);backdrop-filter:blur(16px);-webkit-backdrop-filter:blur(16px)}
.btn{display:inline-flex;align-items:center;gap:9px;background:linear-gradient(135deg,var(--accent),#49e0c4);color:#07070d;font-weight:800;padding:15px 30px;border-radius:14px;font-size:16px;transition:transform .15s,box-shadow .2s;box-shadow:0 10px 40px -10px var(--accent)}
.btn:hover{transform:translateY(-2px);box-shadow:0 18px 50px -10px var(--accent)}
.btn-ghost{background:var(--glass);color:var(--text);border:1px solid var(--line);box-shadow:none;backdrop-filter:blur(10px)}
nav{position:sticky;top:14px;z-index:30;margin:14px auto 0;max-width:1080px}
nav .inner{display:flex;align-items:center;justify-content:space-between;height:60px;padding:0 18px;border-radius:16px}
.logo{display:flex;align-items:center;gap:11px;font-weight:800;font-size:18px}
.logo img{width:34px;height:34px;border-radius:10px}
.nav-cta{padding:10px 20px;border-radius:11px;background:linear-gradient(135deg,var(--accent),#49e0c4);color:#07070d;font-weight:700;font-size:14px}
.hero{text-align:center;padding:66px 0 40px}
.kick{display:inline-flex;gap:8px;align-items:center;font-weight:600;font-size:13px;padding:8px 16px;border-radius:999px;color:var(--text)}
.hero h1{font-size:clamp(40px,7vw,72px);font-weight:800;letter-spacing:-.03em;line-height:1;margin:22px auto 18px;max-width:860px;
  background:linear-gradient(180deg,#fff,#c9c9e8 70%,var(--accent));-webkit-background-clip:text;background-clip:text;color:transparent}
.hero p{color:var(--muted);font-size:19px;max-width:560px;margin:0 auto 28px}
.cta-row{display:flex;gap:14px;justify-content:center;flex-wrap:wrap}
.proof{display:flex;gap:14px;justify-content:center;flex-wrap:wrap;margin-top:40px}
.proof .p{padding:16px 22px;border-radius:16px;text-align:center;min-width:120px}
.proof b{font-size:22px;display:block}.proof span{color:var(--muted);font-size:12.5px}
.phone{margin:54px auto 0;width:280px;height:570px;border-radius:42px;padding:10px;position:relative}
.phone::after{content:"";position:absolute;inset:-30px;border-radius:60px;background:radial-gradient(closest-side,var(--accent),transparent);opacity:.25;filter:blur(40px);z-index:-1}
.phone img{width:100%;height:100%;object-fit:cover;border-radius:32px}
section{padding:68px 0}
.shead{text-align:center;max-width:620px;margin:0 auto 44px}
.shead h2{font-size:clamp(28px,4vw,42px);font-weight:800;letter-spacing:-.02em}
.shead p{color:var(--muted);font-size:17px;margin-top:10px}
.grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.gcard{border-radius:20px;padding:28px}
.gcard .ic{width:52px;height:52px;border-radius:14px;display:grid;place-items:center;background:var(--glass);color:var(--accent);margin-bottom:16px}
.gcard h3{font-size:19px;margin-bottom:8px}.gcard p{color:var(--muted);font-size:14.5px}
.gallery{display:flex;gap:16px;overflow-x:auto;padding:6px 2px 16px;scroll-snap-type:x mandatory}
.gallery img{height:440px;aspect-ratio:9/19;object-fit:cover;border-radius:24px;border:1px solid var(--line);flex:0 0 auto;scroll-snap-align:center}
.rcards{display:grid;grid-template-columns:repeat(2,1fr);gap:16px;max-width:880px;margin:0 auto}
.rcard{border-radius:18px;padding:22px}
.rcard p{color:var(--muted);font-size:14.5px;margin-top:10px}
.rcard .who{display:flex;align-items:center;gap:10px;margin-top:14px}
.av{width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#49e0c4);display:grid;place-items:center;font-weight:800;color:#07070d}
.final{text-align:center;border-radius:28px;padding:60px 24px;margin:20px 0}
.final h2{font-size:clamp(30px,4.4vw,48px);font-weight:800;letter-spacing:-.02em;margin-bottom:14px}
.final p{color:var(--muted);margin-bottom:26px}
.lpf-badges{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;margin-top:8px}
.lpf-badge{display:inline-flex;align-items:center;gap:10px;background:var(--glass);border:1px solid var(--line);color:#fff;padding:10px 18px;border-radius:13px;backdrop-filter:blur(10px)}
.lpf-badge small{display:block;font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.05em}.lpf-badge strong{font-size:15px}
footer{padding:40px 0;color:var(--muted);font-size:14px}
footer .wrap{display:flex;justify-content:space-between;flex-wrap:wrap;gap:14px}
footer a{margin-left:18px}footer a:hover{color:var(--text)}
@media(max-width:820px){.grid{grid-template-columns:1fr}.rcards{grid-template-columns:1fr}}
</style>
</head>
<body>
<nav><div class="inner glass">
  <a class="logo" href="#"><img src="<?= attr($L['icon']) ?>" alt=""><?= esc($L['name']) ?></a>
  <a class="nav-cta" href="<?= attr($L['cta_url']) ?>"><?= esc($L['cta_text']) ?></a>
</div></nav>

<header class="hero"><div class="wrap">
  <span class="kick glass"><?= stars_svg($L['rating'],'#f7c625',14) ?> <?= esc(number_format($L['rating'],1)) ?> · <?= esc($L['rating_count']) ?> <?= esc($S['reviews']) ?></span>
  <h1><?= esc($L['tagline'] ?: $L['name']) ?></h1>
  <p><?= esc($L['description'] ?: $S['cta_sub']) ?></p>
  <div class="cta-row">
    <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= svg_icon('down',18) ?> <?= esc($L['cta_text']) ?></a>
    <a class="btn btn-ghost" href="#features"><?= esc($S['features']) ?></a>
  </div>
  <div class="proof">
    <div class="p glass"><b><?= esc(number_format($L['rating'],1)) ?>★</b><span><?= esc($S['rating']) ?></span></div>
    <?php if($L['downloads']): ?><div class="p glass"><b><?= esc($L['downloads']) ?></b><span><?= esc($S['downloads']) ?></span></div><?php endif; ?>
    <div class="p glass"><b><?= esc($L['category']) ?></b><span><?= esc($S['everyone']) ?></span></div>
  </div>
  <div class="phone glass"><img src="<?= attr($shots[0]) ?>" alt="<?= attr($L['name']) ?>" width="280" height="570" loading="eager" fetchpriority="high" decoding="async"></div>
</div></header>

<section id="features"><div class="wrap">
  <div class="shead"><h2><?= esc($S['why']) ?></h2><p><?= esc($S['join']) ?></p></div>
  <div class="grid">
    <?php foreach(feature_points($S) as $c): ?>
    <div class="gcard glass"><div class="ic"><?= svg_icon($c[0],24) ?></div><h3><?= esc($c[1]) ?></h3><p><?= esc($c[2]) ?></p></div>
    <?php endforeach; ?>
  </div>
</div></section>

<section><div class="wrap">
  <div class="shead"><h2><?= esc($S['screens']) ?></h2></div>
  <div class="gallery"><?php foreach($shots as $s): ?><img src="<?= attr($s) ?>" alt="<?= attr($L['name']) ?>" loading="lazy" decoding="async"><?php endforeach; ?></div>
</div></section>

<?php if(!empty($L['reviews'])): ?>
<section><div class="wrap">
  <div class="shead"><h2><?= esc($S['reviews']) ?></h2><p><?= esc(number_format($L['rating'],1)) ?> / 5 · <?= esc($L['rating_count']) ?></p></div>
  <div class="rcards">
    <?php foreach($L['reviews'] as $r): ?>
    <div class="rcard glass"><?= stars_svg($r[1],'#f7c625',15) ?><p><?= esc($r[2]) ?></p><div class="who"><span class="av"><?= esc(mb_substr($r[0],0,1)) ?></span><div><strong><?= esc($r[0]) ?></strong><br><span style="color:var(--muted);font-size:12px"><?= esc($S['verified']) ?></span></div></div></div>
    <?php endforeach; ?>
  </div>
</div></section>
<?php endif; ?>

<section><div class="wrap"><div class="final glass">
  <h2><?= esc($S['get']) ?> <?= esc($L['name']) ?></h2>
  <p><?= esc($S['cta_sub']) ?></p>
  <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= svg_icon('down',18) ?> <?= esc($L['cta_text']) ?></a>
  <?= store_badges($L['cta_url'], $L['lang']) ?>
</div></div></section>

<footer><div class="wrap">
  <span>© <?= esc($L['year']) ?> <?= esc($L['name']) ?><?php if($L['developer']): ?> · <?= esc($L['developer']) ?><?php endif; ?></span>
  <span><a href="<?= attr($L['privacy_url']) ?>"><?= esc($S['privacy']) ?></a><a href="<?= attr($L['terms_url']) ?>"><?= esc($S['terms']) ?></a></span>
</div></footer>
</body>
</html>
