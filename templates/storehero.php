<?php
/** Template: Store Hero — mimics an app-store listing (install bar, rating bars, about). */
/** @var array $L */
$S = $L['S'];
$accent = attr($L['accent']);
$shots = $L['screenshots'];
$rc = (int)preg_replace('/\D/','',$L['rating_count']) ?: 12480;
?><!DOCTYPE html>
<html lang="<?= attr($L['lang']) ?>" dir="<?= attr($L['dir'] ?? 'ltr') ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= esc($L['name']) ?> — <?= esc($S['get']) ?></title>
<meta name="description" content="<?= attr($L['tagline'] ?: $L['description']) ?>">
<meta property="og:title" content="<?= attr($L['name']) ?>">
<meta property="og:image" content="<?= attr($L['icon']) ?>">
<link rel="icon" href="<?= attr($L['icon']) ?>">
<link rel="preload" as="image" href="<?= attr($L['icon']) ?>" fetchpriority="high">
<style>
:root{--accent:<?= $accent ?>;--bg:#fff;--soft:#f1f3f6;--text:#1c1f26;--muted:#6a7280;--line:#e6e9ef}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Roboto,-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.55}
.wrap{max-width:1000px;margin:0 auto;padding:0 22px}
a{color:inherit;text-decoration:none}
.btn{display:inline-flex;align-items:center;gap:8px;background:var(--accent);color:#fff;font-weight:600;border:0;cursor:pointer;
     padding:12px 40px;border-radius:10px;font-size:15px;transition:filter .15s}
.btn:hover{filter:brightness(1.07)}
.topbar{border-bottom:1px solid var(--line);height:58px;display:flex;align-items:center}
.topbar .wrap{display:flex;align-items:center;gap:10px;font-weight:600}
.topbar img{width:26px;height:26px;border-radius:6px}
/* head card */
.appcard{display:flex;gap:24px;align-items:flex-start;padding:34px 0 26px}
.appcard .icon{width:108px;height:108px;border-radius:24px;box-shadow:0 6px 18px -8px rgba(0,0,0,.3);flex:0 0 auto}
.appcard h1{font-size:30px;font-weight:500;letter-spacing:-.01em}
.dev{color:var(--accent);font-size:14px;font-weight:600;margin:4px 0 14px}
.statbar{display:flex;gap:0;margin:8px 0 18px}
.statbar div{padding-right:26px;margin-right:26px;border-right:1px solid var(--line);text-align:center}
.statbar div:last-child{border-right:0}
.statbar b{font-size:16px;display:flex;align-items:center;gap:5px;justify-content:center}
.statbar span{font-size:12px;color:var(--muted)}
.install-row{display:flex;gap:12px;align-items:center}
.muted{color:var(--muted);font-size:13px}
/* screenshots */
.section{padding:26px 0;border-top:1px solid var(--line)}
.section h2{font-size:20px;font-weight:500;margin-bottom:16px}
.shots{display:flex;gap:14px;overflow-x:auto;padding-bottom:8px}
.shots img{height:340px;aspect-ratio:9/19;object-fit:cover;border-radius:14px;border:1px solid var(--line);flex:0 0 auto}
.about{color:#39414f;font-size:15px;max-width:760px;white-space:pre-line}
.tags{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}
.tag{background:var(--soft);border-radius:999px;padding:6px 14px;font-size:13px;color:var(--muted)}
/* ratings */
.ratewrap{display:flex;gap:40px;align-items:center;flex-wrap:wrap}
.bignum{text-align:center}
.bignum .n{font-size:52px;font-weight:500;line-height:1}
.bars{flex:1;min-width:240px}
.bar{display:flex;align-items:center;gap:10px;margin:3px 0}
.bar span{font-size:12px;color:var(--muted);width:10px}
.track{flex:1;height:8px;background:var(--soft);border-radius:5px;overflow:hidden}
.fill{height:100%;background:var(--accent)}
.review{padding:18px 0;border-top:1px solid var(--line)}
.review .h{display:flex;align-items:center;gap:10px;margin-bottom:6px}
.av{width:36px;height:36px;border-radius:50%;background:var(--soft);display:grid;place-items:center;font-weight:700;color:var(--accent)}
.info{display:grid;grid-template-columns:repeat(2,1fr);gap:12px 30px;max-width:680px}
.info div{display:flex;justify-content:space-between;border-bottom:1px solid var(--line);padding:9px 0;font-size:14px}
.info span{color:var(--muted)}
footer{border-top:1px solid var(--line);padding:26px 0;color:var(--muted);font-size:13px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px}
footer a{margin-left:16px}footer a:hover{color:var(--text)}
@media(max-width:640px){.appcard{flex-direction:column}.statbar{flex-wrap:wrap;gap:10px}.shots img{height:280px}}
</style>
</head>
<body>
<div class="topbar"><div class="wrap"><img src="<?= attr($L['icon']) ?>" alt=""> <?= esc($L['name']) ?></div></div>

<div class="wrap">
  <div class="appcard">
    <img class="icon" src="<?= attr($L['icon']) ?>" alt="<?= attr($L['name']) ?>" width="108" height="108" loading="eager" fetchpriority="high" decoding="async">
    <div style="flex:1">
      <h1><?= esc($L['name']) ?></h1>
      <?php if($L['developer']): ?><div class="dev"><?= esc($L['developer']) ?></div><?php endif; ?>
      <div class="statbar">
        <div><b><?= esc(number_format($L['rating'],1)) ?> <?= svg_icon('star',13) ?></b><span><?= number_format($rc) ?> <?= esc($S['reviews']) ?></span></div>
        <?php if($L['downloads']): ?><div><b><?= esc($L['downloads']) ?></b><span><?= esc($S['downloads']) ?></span></div><?php endif; ?>
        <div><b><?= esc($S['everyone']) ?></b><span><?= esc($S['age']) ?></span></div>
      </div>
      <div class="install-row">
        <a class="btn" href="<?= attr($L['cta_url']) ?>"><?= esc($S['install']) ?></a>
        <span class="muted"><?= esc($S['cta_sub']) ?></span>
      </div>
    </div>
  </div>

  <div class="section">
    <div class="shots">
      <?php foreach($shots as $s): ?><img src="<?= attr($s) ?>" alt="<?= attr($L['name']) ?>" loading="lazy" decoding="async"><?php endforeach; ?>
    </div>
  </div>

  <div class="section">
    <h2><?= esc($S['about']) ?></h2>
    <p class="about"><?= esc($L['description'] ?: $S['join']) ?></p>
    <div class="tags">
      <span class="tag"><?= esc($L['category']) ?></span>
      <span class="tag"><?= esc($S['everyone']) ?></span>
      <span class="tag"><?= esc($S['features']) ?></span>
    </div>
  </div>

  <div class="section">
    <h2><?= esc($S['reviews']) ?></h2>
    <div class="ratewrap">
      <div class="bignum"><div class="n"><?= esc(number_format($L['rating'],1)) ?></div><?= stars_svg($L['rating'],$accent,15) ?><div class="muted"><?= number_format($rc) ?></div></div>
      <div class="bars">
        <?php $dist=[78,15,4,2,1]; for($i=0;$i<5;$i++): ?>
        <div class="bar"><span><?= 5-$i ?></span><div class="track"><div class="fill" style="width:<?= $dist[$i] ?>%"></div></div></div>
        <?php endfor; ?>
      </div>
    </div>
    <?php foreach($L['reviews'] as $r): ?>
    <div class="review"><div class="h"><div class="av"><?= esc(mb_substr($r[0],0,1)) ?></div>
      <div><strong><?= esc($r[0]) ?></strong> &nbsp;<?= stars_svg($r[1],$accent,13) ?></div></div>
      <p class="muted"><?= esc($r[2]) ?></p></div>
    <?php endforeach; ?>
  </div>

  <div class="section">
    <h2><?= esc($S['what_new']) ?></h2>
    <div class="info">
      <div><span><?= esc($S['updated']) ?></span><b><?= esc($L['year']) ?></b></div>
      <div><span><?= esc($S['category']) ?: 'Category' ?></span><b><?= esc($L['category']) ?></b></div>
      <div><span><?= esc($S['rating']) ?></span><b><?= esc(number_format($L['rating'],1)) ?> ★</b></div>
      <div><span><?= esc($S['age']) ?></span><b><?= esc($S['everyone']) ?></b></div>
    </div>
  </div>
</div>

<footer class="wrap">
  <span>© <?= esc($L['year']) ?> <?= esc($L['name']) ?></span>
  <span><a href="<?= attr($L['privacy_url']) ?>"><?= esc($S['privacy']) ?></a><a href="<?= attr($L['terms_url']) ?>"><?= esc($S['terms']) ?></a></span>
</footer>
</body>
</html>
