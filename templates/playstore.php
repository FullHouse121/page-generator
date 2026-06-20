<?php
/** Template: Play Store — authentic Google Play listing clone (light). */
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
:root{--accent:<?= $accent ?>;--bg:#fff;--soft:#f1f3f4;--line:#e8eaed;--text:#202124;--muted:#5f6368}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Roboto,-apple-system,BlinkMacSystemFont,"Segoe UI",Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.5}
.wrap{max-width:920px;margin:0 auto;padding:0 24px}
a{color:inherit;text-decoration:none}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;background:var(--accent);color:#fff;font-weight:600;border:0;cursor:pointer;
     padding:11px 0;border-radius:8px;font-size:15px;width:100%;max-width:420px;transition:filter .15s,box-shadow .15s}
.btn:hover{filter:brightness(1.06);box-shadow:0 2px 10px rgba(0,0,0,.12)}
.topbar{border-bottom:1px solid var(--line);height:56px;display:flex;align-items:center}
.topbar .wrap{display:flex;align-items:center;gap:18px}
.gplay{display:flex;align-items:center;gap:8px;font-size:20px;font-weight:500;color:#5f6368}
.gplay svg{width:24px;height:24px}
.appcard{display:flex;gap:24px;align-items:flex-start;padding:32px 0 8px}
.appcard .icon{width:96px;height:96px;border-radius:22px;box-shadow:0 1px 4px rgba(0,0,0,.18);flex:0 0 auto}
.appcard h1{font-size:30px;font-weight:400;color:var(--text);letter-spacing:0}
.dev{color:var(--accent);font-size:14px;font-weight:500;margin:6px 0 2px}
.subtle{color:var(--muted);font-size:12.5px}
.statbar{display:flex;align-items:stretch;margin:18px 0;color:var(--text)}
.statbar div{padding:0 18px;border-right:1px solid var(--line);text-align:center;display:flex;flex-direction:column;justify-content:center;gap:3px}
.statbar div:first-child{padding-left:0}.statbar div:last-child{border-right:0}
.statbar b{font-size:14px;font-weight:600;display:flex;align-items:center;gap:4px;justify-content:center}
.statbar span{font-size:11.5px;color:var(--muted)}
.statbar .agebox{border:1.5px solid var(--muted);border-radius:4px;padding:1px 5px;font-size:13px;color:var(--text);font-weight:500}
.installrow{display:flex;align-items:center;gap:18px;margin:6px 0 4px;max-width:420px}
.iconbtn{color:var(--accent);display:flex;align-items:center;gap:6px;font-size:13px;font-weight:600}
.note{color:var(--muted);font-size:12.5px;margin-top:14px;display:flex;align-items:center;gap:7px}
.section{padding:26px 0;border-top:1px solid var(--line)}
.section h2{font-size:18px;font-weight:500;margin-bottom:14px;display:flex;justify-content:space-between;align-items:center}
.shots{display:flex;gap:12px;overflow-x:auto;padding-bottom:6px;scroll-snap-type:x mandatory}
.shots img{height:330px;aspect-ratio:9/19;object-fit:cover;border-radius:12px;border:1px solid var(--line);flex:0 0 auto;scroll-snap-align:start;transition:transform .2s ease}
.shots img:hover{transform:translateY(-3px)}
.about{color:#3c4043;font-size:14px;max-width:760px;white-space:pre-line}
.chips{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}
.chip{border:1px solid var(--line);border-radius:999px;padding:6px 14px;font-size:13px;color:var(--muted)}
.ds{display:flex;gap:14px;align-items:flex-start;background:var(--soft);border-radius:12px;padding:16px 18px}
.ds svg{width:22px;height:22px;color:var(--muted);flex:0 0 auto;margin-top:2px}
.ds p{font-size:13.5px;color:#3c4043}
.ratewrap{display:flex;gap:40px;align-items:center;flex-wrap:wrap}
.bignum .n{font-size:54px;font-weight:500;line-height:1}
.bignum .sub{color:var(--muted);font-size:12.5px;margin-top:4px}
.bars{flex:1;min-width:230px}
.bar{display:flex;align-items:center;gap:10px;margin:2px 0}.bar span{font-size:12px;color:var(--muted);width:8px}
.track{flex:1;height:9px;background:var(--soft);border-radius:6px;overflow:hidden}.fill{height:100%;background:var(--accent)}
.review{padding:18px 0;border-top:1px solid var(--line)}
.review .h{display:flex;align-items:center;gap:10px;margin-bottom:7px}
.av{width:34px;height:34px;border-radius:50%;background:var(--soft);display:grid;place-items:center;font-weight:600;color:var(--accent)}
.info div{display:flex;justify-content:space-between;border-bottom:1px solid var(--line);padding:10px 0;font-size:13.5px;max-width:620px}
.info span{color:var(--muted)}
footer{border-top:1px solid var(--line);padding:24px 0;color:var(--muted);font-size:13px;display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px}
footer a:hover{color:var(--text)}
@media(max-width:640px){.appcard{gap:16px}.appcard .icon{width:72px;height:72px}.appcard h1{font-size:24px}.shots img{height:300px}}
</style>
</head>
<body>
<div class="topbar"><div class="wrap"><span class="gplay"><svg viewBox="0 0 24 24"><path fill="#00c4ff" d="M3 3l11 9-11 9z" opacity=".0"/><path fill="#34a853" d="M3.5 2.6l10.6 9.4-2.7 2.4z"/><path fill="#fbbc04" d="M17.4 9.6l3.3 1.9c.9.5.9 1.5 0 2l-3.3 1.9-2.9-2.9z"/><path fill="#ea4335" d="M3.5 2.6l7.9 11.8L8.5 17z" opacity=".0"/><path fill="#4285f4" d="M3.5 2.6L14.1 12 3.5 21.4c-.5-.2-.9-.7-.9-1.5V4.1c0-.8.4-1.3.9-1.5z"/><path fill="#ea4335" d="M3.5 21.4L11.4 14l2.7 2.4-9 5.2c-.6.3-1.2.2-1.6-.2z"/><path fill="#fbbc04" d="M14.1 12l-2.7-2.4 2.7-2.4 2.9 2.9z" opacity="0"/></svg> Google Play</span></div></div>

<div class="wrap">
  <div class="appcard">
    <img class="icon" src="<?= attr($L['icon']) ?>" alt="<?= attr($L['name']) ?>" width="96" height="96" loading="eager" fetchpriority="high" decoding="async">
    <div style="flex:1">
      <h1><?= esc($L['name']) ?></h1>
      <div class="dev"><?= esc($L['developer'] ?: 'Apps') ?></div>
      <div class="subtle"><?= esc($L['category']) ?> · <?= esc($S['everyone']) ?></div>
    </div>
  </div>

  <div class="statbar">
    <div><b><?= esc(number_format($L['rating'],1)) ?> <?= svg_icon('star',12) ?></b><span><?= number_format($rc) ?> <?= esc($S['reviews']) ?></span></div>
    <?php if($L['downloads']): ?><div><b><?= esc($L['downloads']) ?></b><span><?= esc($S['downloads']) ?></span></div><?php endif; ?>
    <div><b class="agebox"><?= esc($S['everyone']) ?></b><span><?= esc($S['age']) ?></span></div>
  </div>

  <div class="installrow"><a class="btn" href="<?= attr($L['cta_url']) ?>"><?= esc($S['install']) ?></a></div>
  <div style="display:flex;gap:24px;margin-top:14px">
    <span class="iconbtn"><?= svg_icon('down',16) ?> <?= esc($S['get']) ?></span>
    <span class="iconbtn"><?= svg_icon('heart',16) ?> <?= esc($S['reviews']) ?></span>
  </div>
  <div class="note"><?= svg_icon('check',15) ?> <?= esc($S['cta_sub']) ?></div>

  <div class="section reveal">
    <div class="shots">
      <?php foreach($shots as $s): ?><img src="<?= attr($s) ?>" alt="<?= attr($L['name']) ?>" loading="lazy" decoding="async"><?php endforeach; ?>
    </div>
  </div>

  <div class="section reveal">
    <h2><?= esc($S['about']) ?> <?= svg_icon('down',18) ?></h2>
    <p class="about"><?= esc($L['description'] ?: $S['join']) ?></p>
    <div class="chips"><span class="chip"><?= esc($L['category']) ?></span><span class="chip"><?= esc($S['features']) ?></span><span class="chip"><?= esc($S['everyone']) ?></span></div>
  </div>

  <div class="section reveal">
    <h2><?= esc($S['privacy']) ?> &amp; <?= esc($S['verified']) ?></h2>
    <div class="ds"><?= svg_icon('shield',22) ?><div><p><?= esc($S['b_secure']) ?></p></div></div>
  </div>

  <div class="section reveal">
    <h2><?= esc($S['reviews']) ?></h2>
    <div class="ratewrap">
      <div class="bignum"><div class="n"><?= esc(number_format($L['rating'],1)) ?></div><?= stars_svg($L['rating'],$accent,15) ?><div class="sub"><?= number_format($rc) ?></div></div>
    </div>
    <?php if(!empty($L['reviews'])): foreach($L['reviews'] as $r): ?>
    <div class="review"><div class="h"><div class="av"><?= esc(mb_substr($r[0],0,1)) ?></div><div><strong><?= esc($r[0]) ?></strong> &nbsp;<?= stars_svg($r[1],$accent,12) ?></div></div><p class="about"><?= esc($r[2]) ?></p></div>
    <?php endforeach; endif; ?>
  </div>

  <div class="section reveal">
    <h2><?= esc($S['what_new']) ?></h2>
    <div class="info">
      <div><span><?= esc($S['updated']) ?></span><b><?= esc($L['year']) ?></b></div>
      <div><span><?= esc($S['rating']) ?></span><b><?= esc(number_format($L['rating'],1)) ?> ★</b></div>
      <div><span><?= esc($S['age']) ?></span><b><?= esc($S['everyone']) ?></b></div>
    </div>
  </div>
</div>

<footer class="wrap">
  <span>© <?= esc($L['year']) ?> <?= esc($L['name']) ?></span>
  <span><a href="<?= attr($L['privacy_url']) ?>"><?= esc($S['privacy']) ?></a> &nbsp; <a href="<?= attr($L['terms_url']) ?>"><?= esc($S['terms']) ?></a></span>
</footer>
<?= reveal_script() ?>
</body>
</html>
