<?php
/**
 * White LP Factory — paste an app link, generate a clean white landing page.
 * Run locally:  php -S localhost:8000   (from this folder), then open /
 */
require_once __DIR__ . '/lib/auth.php';   // optional Basic Auth (active only if LPF_USER set)
require_once __DIR__ . '/lib/fetcher.php';
require_once __DIR__ . '/lib/templates.php';
require_once __DIR__ . '/lib/builder.php';

$action = $_POST['action'] ?? 'home';
$reg    = template_registry();

/* repopulation helper */
function v($k, $d = '') { return $_POST[$k] ?? $d; }

/* tiny wireframe per template for the picker */
function wireframe($key) {
    $a = '#36d07c';
    $base = '<rect width="120" height="150" rx="8" fill="#0e1016"/>';
    $w = [
        'spotlight' => '<rect x="12" y="14" width="46" height="9" rx="3" fill="#2a2f3a"/><rect x="12" y="30" width="34" height="6" rx="3" fill="'.$a.'"/><rect x="72" y="16" width="34" height="60" rx="7" fill="#1b2030"/><rect x="12" y="92" width="28" height="28" rx="5" fill="#1b2030"/><rect x="46" y="92" width="28" height="28" rx="5" fill="#1b2030"/><rect x="80" y="92" width="28" height="28" rx="5" fill="#1b2030"/>',
        'minimal'   => '<rect x="50" y="16" width="20" height="20" rx="6" fill="'.$a.'"/><rect x="34" y="44" width="52" height="8" rx="4" fill="#2a2f3a"/><rect x="42" y="58" width="36" height="5" rx="2" fill="#1b2030"/><rect x="30" y="78" width="24" height="46" rx="6" fill="#1b2030"/><rect x="66" y="78" width="24" height="46" rx="6" fill="#1b2030"/>',
        'storehero' => '<rect x="12" y="14" width="22" height="22" rx="6" fill="'.$a.'"/><rect x="40" y="16" width="40" height="7" rx="3" fill="#2a2f3a"/><rect x="40" y="28" width="24" height="5" rx="2" fill="#1b2030"/><rect x="12" y="48" width="30" height="40" rx="5" fill="#1b2030"/><rect x="46" y="48" width="30" height="40" rx="5" fill="#1b2030"/><rect x="12" y="98" width="96" height="6" rx="3" fill="#1b2030"/><rect x="12" y="110" width="70" height="6" rx="3" fill="#1b2030"/>',
        'feature'   => '<rect x="12" y="16" width="40" height="8" rx="4" fill="#2a2f3a"/><rect x="12" y="40" width="44" height="6" rx="3" fill="#1b2030"/><rect x="12" y="52" width="34" height="6" rx="3" fill="'.$a.'"/><rect x="74" y="36" width="34" height="44" rx="7" fill="#1b2030"/><rect x="12" y="96" width="96" height="20" rx="6" fill="#11151c"/>',
        'bold'      => '<rect x="20" y="20" width="80" height="12" rx="4" fill="'.$a.'" opacity=".85"/><rect x="34" y="40" width="52" height="6" rx="3" fill="#2a2f3a"/><rect x="30" y="60" width="24" height="48" rx="7" fill="#1b2030"/><rect x="58" y="54" width="24" height="54" rx="7" fill="#242a3a"/><rect x="86" y="60" width="18" height="48" rx="7" fill="#1b2030"/>',
        'playstore' => '<rect x="12" y="14" width="24" height="24" rx="6" fill="'.$a.'"/><rect x="42" y="16" width="44" height="7" rx="3" fill="#2a2f3a"/><rect x="42" y="28" width="28" height="5" rx="2" fill="#1b2030"/><rect x="12" y="46" width="96" height="12" rx="6" fill="'.$a.'"/><rect x="12" y="66" width="30" height="42" rx="5" fill="#1b2030"/><rect x="46" y="66" width="30" height="42" rx="5" fill="#1b2030"/><rect x="12" y="116" width="64" height="6" rx="3" fill="#1b2030"/>',
        'aurora'    => '<circle cx="30" cy="22" r="26" fill="#a15bff" opacity=".35"/><circle cx="94" cy="22" r="22" fill="'.$a.'" opacity=".30"/><rect x="26" y="30" width="68" height="10" rx="4" fill="#2a2f3a"/><rect x="40" y="50" width="40" height="40" rx="9" fill="#1b2030"/><rect x="16" y="102" width="28" height="22" rx="6" fill="#1b2030"/><rect x="48" y="102" width="24" height="22" rx="6" fill="#1b2030"/><rect x="80" y="102" width="24" height="22" rx="6" fill="#1b2030"/>',
        'editorial' => '<rect x="12" y="18" width="20" height="5" rx="2" fill="'.$a.'"/><rect x="12" y="30" width="92" height="14" rx="4" fill="#2a2f3a"/><rect x="12" y="52" width="60" height="6" rx="3" fill="#1b2030"/><rect x="12" y="70" width="96" height="38" rx="6" fill="#1b2030"/><rect x="12" y="118" width="26" height="6" rx="3" fill="#1b2030"/><rect x="47" y="118" width="26" height="6" rx="3" fill="#1b2030"/><rect x="82" y="118" width="26" height="6" rx="3" fill="#1b2030"/>',
    ];
    return '<svg viewBox="0 0 120 150" width="100%" style="display:block;border-radius:10px">'.$base.($w[$key] ?? '').'</svg>';
}

function render_cards($selected) {
    foreach (template_registry() as $key => $t) {
        $chk = $key === $selected ? 'checked' : '';
        $cl  = $key === $selected ? 'tpl sel' : 'tpl';
        echo '<label class="'.$cl.'"><input type="radio" name="template" value="'.attr($key).'" '.$chk.'>'
           . '<div class="wf">'.wireframe($key).'</div>'
           . '<div class="tname">'.esc($t['name']).'</div>'
           . '<div class="tdesc">'.esc($t['desc']).'</div></label>';
    }
}
/* ============================================================ LIVE PREVIEW (AJAX) */
if ($action === 'preview') {
    header('Content-Type: text/html; charset=UTF-8');
    echo render_preview([
        'name'=>v('name'),'tagline'=>v('tagline'),'description'=>v('description'),'icon'=>v('icon'),
        'developer'=>v('developer'),'category'=>v('category','App'),'rating'=>v('rating','4.8'),
        'rating_count'=>v('rating_count'),'downloads'=>v('downloads'),
        'screenshots'=>array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', v('screenshots')))),
        'cta_url'=>v('cta_url','#'),'cta_text'=>v('cta_text'),'template'=>v('template','spotlight'),
        'lang'=>v('lang','en'),'accent'=>v('accent'),
        'seo_title'=>v('seo_title'),'seo_desc'=>v('seo_desc'),'og_image'=>v('og_image'),
        'trust_badges'=>!empty($_POST['trust_badges']),'cookie_banner'=>!empty($_POST['cookie_banner']),
        'competitor_urls'=>v('competitor_urls'),'source_url'=>v('source_url'),
        'age18'=>!empty($_POST['age18']),'support_email'=>v('support_email'),'company'=>v('company'),
    ]);
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>DEUS · LP Factory</title>
<link rel="icon" href="assets/brand/favicon.ico" sizes="any">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{
  --bg:#121315;--bg-deep:#0b0c0e;--panel:#1b1d21;--panel2:#14161a;--panel-soft:#202228;--panel-deep:#14161a;
  --line:#2b2e35;--stroke:#2b2e35;--stroke-soft:rgba(255,255,255,.06);--text:#f2f2f4;--muted:#8b8f98;--muted-strong:#a9adb7;
  --accent:#36d07c;--green:#36d07c;--green-soft:rgba(54,208,124,.14);--blue:#64b8ff;--purple:#a15bff;
  --yellow:#f7c625;--pink:#ff7da3;--orange:#ff9357;--teal:#49e0c4;--red:#ff7d88;
  --radius:18px;--radius-md:14px;--radius-sm:10px;
}
*{box-sizing:border-box;margin:0;padding:0}
html,body{background:var(--bg);color:var(--text);font-family:"Sora",system-ui,-apple-system,"Segoe UI",sans-serif;letter-spacing:-.005em;-webkit-font-smoothing:antialiased;line-height:1.55}
body::before{content:"";position:fixed;inset:0;z-index:0;pointer-events:none;
  background-image:linear-gradient(rgba(54,208,124,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(54,208,124,.04) 1px,transparent 1px);background-size:42px 42px}
body::after{content:"";position:fixed;inset:0;z-index:0;pointer-events:none;
  background:radial-gradient(1200px 600px at 8% -10%,rgba(54,208,124,.06),transparent 60%),radial-gradient(900px 500px at 92% 110%,rgba(100,184,255,.04),transparent 60%)}
.wrap{max-width:1000px;margin:0 auto;padding:0 22px;position:relative;z-index:1}
a{color:var(--accent);text-decoration:none}
header.top{padding:34px 0 8px}
.brand{display:flex;align-items:center;gap:14px}
.brand img{height:40px;width:auto;display:block;filter:drop-shadow(0 0 14px rgba(54,208,124,.25))}
.brand .tool{font-size:13px;color:var(--muted-strong);font-weight:600;letter-spacing:-.005em;padding-left:14px;border-left:1px solid var(--line)}
.brand small{color:var(--muted);font-weight:400;font-size:13px;display:block;letter-spacing:0;margin-top:1px}
.steps{display:flex;gap:8px;margin:20px 0 26px;color:var(--muted);font-size:13px;flex-wrap:wrap}
.steps b{color:var(--accent)}
.card{position:relative;z-index:1;overflow:hidden;
  background:radial-gradient(circle at top left,rgba(255,255,255,.04),transparent 36%),linear-gradient(180deg,#212329fa,#191b20fb),#1b1d21;
  border:1px solid var(--line);border-radius:var(--radius);padding:24px;margin-bottom:18px;
  box-shadow:0 22px 40px rgba(0,0,0,.28),inset 0 1px rgba(255,255,255,.03)}
.card::before{content:"";position:absolute;inset:0;border-radius:inherit;pointer-events:none;background:linear-gradient(180deg,rgba(255,255,255,.03),transparent 24%)}
.card>*{position:relative;z-index:1}
.card h2{font-size:17px;font-weight:700;letter-spacing:-.02em;margin-bottom:4px}
.card .hint{color:var(--muted);font-size:13px;margin-bottom:18px}
label.f{display:block;margin-bottom:16px}
label.f span{display:block;font-size:12.5px;color:var(--muted-strong);margin-bottom:6px;font-weight:500}
input[type=text],input[type=url],input[type=number],textarea,select{width:100%;background:var(--panel-deep);border:1px solid var(--line);
  border-radius:var(--radius-sm);color:var(--text);padding:11px 13px;font-size:14px;font-family:inherit;transition:border-color .16s,box-shadow .16s}
input::placeholder,textarea::placeholder{color:var(--muted)}
input:focus,textarea:focus,select:focus{outline:none;border-color:rgba(54,208,124,.5);box-shadow:0 0 12px rgba(54,208,124,.15)}
textarea{min-height:90px;resize:vertical}
.row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
.btn{display:inline-flex;align-items:center;gap:8px;cursor:pointer;font-family:inherit;font-weight:600;font-size:14px;
  padding:11px 20px;border-radius:var(--radius-sm);border:1px solid rgba(54,208,124,.4);background:rgba(54,208,124,.12);color:var(--accent);
  transition:background .16s,box-shadow .16s,border-color .16s}
.btn:hover{background:rgba(54,208,124,.2);box-shadow:0 0 14px rgba(54,208,124,.2)}
.btn.sec{background:linear-gradient(180deg,#15171cfa,#111317fa);border-color:var(--line);color:var(--muted-strong)}
.btn.sec:hover{color:var(--text);border-color:#3a3e47;box-shadow:none}
.btn.lg{padding:14px 26px;font-size:15px}
.tpls{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
label.tpl{position:relative;background:linear-gradient(180deg,#202228,#191b20);border:1px solid var(--line);border-radius:var(--radius-md);padding:10px;cursor:pointer;transition:border-color .14s,transform .14s,box-shadow .14s;display:block}
label.tpl:hover{transform:translateY(-2px);border-color:#3a3e47}
label.tpl.sel,label.tpl:has(input:checked){border-color:rgba(54,208,124,.6);box-shadow:0 0 0 1px rgba(54,208,124,.5),0 0 16px rgba(54,208,124,.12)}
label.tpl input{display:none}
.tpl .wf{margin-bottom:8px}
.tpl .tname{font-weight:600;font-size:13px}
.tpl .tdesc{color:var(--muted);font-size:11px;line-height:1.35;margin-top:2px}
.opts{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.opt{display:flex;gap:10px;align-items:flex-start;background:var(--panel-deep);border:1px solid var(--line);border-radius:var(--radius-sm);padding:12px 14px}
.opt input{margin-top:3px;accent-color:var(--accent)}
.opt b{font-size:13.5px;font-weight:600}.opt small{color:var(--muted);font-size:12px;display:block;margin-top:2px}
.seg{display:inline-flex;background:var(--panel-deep);border:1px solid var(--line);border-radius:var(--radius-sm);overflow:hidden}
.seg label{cursor:pointer}
.seg input{display:none}
.seg span{display:block;padding:9px 16px;font-size:13.5px;color:var(--muted-strong)}
.seg input:checked + span{background:var(--green-soft);color:var(--accent);font-weight:600}
.warn{background:rgba(247,198,37,.08);border:1px solid rgba(247,198,37,.3);color:#f7d878;border-radius:var(--radius-sm);padding:12px 16px;font-size:13px;margin-bottom:10px}
.ok{position:relative;overflow:hidden;background:rgba(54,208,124,.07);border:1px solid rgba(54,208,124,.35);border-radius:var(--radius-md);padding:18px 20px;margin-bottom:18px}
.ok h2{color:var(--accent);font-size:18px;font-weight:700;display:flex;align-items:center;gap:10px}
.badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:999px;font-size:.72rem;font-weight:600}
.badge-green{background:rgba(54,208,124,.14);color:#36d07c}
.badge-yellow{background:rgba(247,198,37,.14);color:#f7c625}
.badge-red{background:rgba(255,125,136,.14);color:#ff7d88}
.badge-blue{background:rgba(100,184,255,.14);color:#64b8ff}
.badge-muted{background:rgba(255,255,255,.06);color:#a9adb7}
.filelist{font-family:ui-monospace,Menlo,Consolas,monospace;font-size:12.5px;color:var(--muted);background:var(--bg-deep);border:1px solid var(--line);border-radius:var(--radius-sm);padding:14px;margin-top:12px;line-height:1.7}
.muted{color:var(--muted)}
code{background:var(--panel-deep);border:1px solid var(--line);border-radius:6px;padding:1px 6px;font-size:.9em;color:var(--muted-strong)}
footer{color:var(--muted);font-size:12.5px;text-align:center;padding:30px 0;position:relative;z-index:1}
@media(max-width:780px){.tpls{grid-template-columns:repeat(2,1fr)}.row,.row3,.opts{grid-template-columns:1fr}}
</style>
</head>
<body>
<header class="top"><div class="wrap">
  <div class="brand">
    <img src="assets/brand/deus-logo.png" width="172" height="50" alt="DEUS">
    <div class="tool">LP Factory<small>White-safe app landing pages · Keitaro-ready</small></div>
  </div>
</div></header>

<main class="wrap">
<?php
/* ============================================================ STATE 3: GENERATE */
if ($action === 'generate'):
    $form = [
        'name'=>v('name'),'tagline'=>v('tagline'),'description'=>v('description'),'icon'=>v('icon'),
        'developer'=>v('developer'),'category'=>v('category','App'),'rating'=>v('rating','4.8'),
        'rating_count'=>v('rating_count'),'downloads'=>v('downloads'),
        'screenshots'=>array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', v('screenshots')))),
        'cta_url'=>v('cta_url','#download'),'cta_text'=>v('cta_text'),'template'=>v('template','spotlight'),
        'lang'=>v('lang','en'),'accent'=>v('accent'),'kclient'=>!empty($_POST['kclient']),
        'format'=>v('format','php'),'localize'=>!empty($_POST['localize']),
        'tracker_url'=>v('tracker_url'),'tracker_token'=>v('tracker_token'),
        'seo_title'=>v('seo_title'),'seo_desc'=>v('seo_desc'),'og_image'=>v('og_image'),
        'trust_badges'=>!empty($_POST['trust_badges']),'cookie_banner'=>!empty($_POST['cookie_banner']),
        'competitor_urls'=>v('competitor_urls'),'source_url'=>v('source_url'),
        'age18'=>!empty($_POST['age18']),'support_email'=>v('support_email'),'company'=>v('company'),
    ];
    $res = build_landing($form);
    $tname = $reg[$form['template']]['name'] ?? $form['template'];
?>
  <div class="steps">1. Link → 2. Edit → <b>3. Done ✓</b></div>
  <div class="ok">
    <h2>Landing generated <span class="badge badge-green">✓ Ready</span></h2>
    <div class="muted">Template: <b style="color:var(--text)"><?= esc($tname) ?></b> · Slug: <code><?= esc($res['slug']) ?></code></div>
  </div>

  <?php foreach($res['warnings'] as $w): ?><div class="warn"><?= esc($w) ?></div><?php endforeach; ?>

  <div class="card">
    <h2>Your files</h2>
    <p class="hint">Generated into <code>output/<?= esc($res['slug']) ?>/</code> — upload that whole folder to your landing host / Keitaro.</p>
    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:6px">
      <?php if($res['index']): ?><a class="btn" target="_blank" href="output/<?= rawurlencode($res['slug']) ?>/<?= esc($res['index']) ?>">↗ Open preview</a><?php endif; ?>
      <?php if($res['zip']): ?><a class="btn sec" href="output/<?= rawurlencode($res['slug']) ?>.zip" download>⬇ Download ZIP</a><?php endif; ?>
      <a class="btn sec" href="index.php">＋ Make another</a>
    </div>
    <div class="filelist">
      output/<?= esc($res['slug']) ?>/<br>
      ├─ <?= esc($res['index']) ?><br>
      ├─ privacy.html<br>
      ├─ terms.html<br>
      ├─ robots.txt<br>
      <?php if($form['kclient']): ?>├─ kclient.php  <span style="color:var(--accent)">← replace with Keitaro's Kclient</span><br><?php endif; ?>
      └─ assets/ (icon + screenshots)
    </div>
  </div>

  <?php if($form['kclient']): ?>
  <div class="card">
    <h2>Keitaro hookup (preloading method)</h2>
    <p class="hint">The page already includes <code>kclient.php</code> at the very top. To go live:</p>
    <ol class="muted" style="padding-left:18px;font-size:14px;line-height:1.9">
      <li>In Keitaro: <b>Campaign → Integration → PHP (local / Kclient)</b>, download the generated <code>Kclient.php</code>.</li>
      <li>Rename it to <code>kclient.php</code> and drop it in this landing folder, replacing the placeholder.</li>
      <li>Upload the folder to your white domain. Add it to the flow as the <b>landing</b> with the <b>Preloading</b> action.</li>
    </ol>
  </div>
  <?php endif; ?>

<?php
/* ============================================================ STATE 2: EDIT */
elseif ($action === 'fetch'):
    $url = trim(v('url'));
    $tpl = v('template','spotlight');
    $lang = v('lang','en');
    $meta = $url !== '' ? fetch_app_meta($url) : default_meta();
    if ($meta['source'] && $url!=='') { /* detected */ }
    $accentDefault = $reg[$tpl]['accent'] ?? '#5b8cff';
?>
  <div class="steps">1. Link → <b>2. Edit & generate</b> → 3. Done</div>
  <?php if($url!=='' && $meta['name']==='My App'): ?>
    <div class="warn">Couldn't auto-read that link (store may be blocking the server). Fill the fields in manually below — everything still works.</div>
  <?php elseif($url!==''): ?>
    <div class="ok" style="padding:12px 18px"><span class="muted">Auto-filled from <b style="color:var(--text)"><?= esc($meta['source']?:'link') ?></b>. Edit anything before generating.</span></div>
  <?php endif; ?>

  <form id="genForm" method="post" action="index.php">
  <input type="hidden" name="action" value="generate">
  <input type="hidden" name="source_url" value="<?= attr($url) ?>">

  <div class="card">
    <h2 style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap">Live preview
      <span style="display:flex;gap:8px">
        <button type="button" class="btn sec" style="padding:7px 13px;font-size:12.5px" onclick="lpDevice('mobile')">Mobile</button>
        <button type="button" class="btn sec" style="padding:7px 13px;font-size:12.5px" onclick="lpDevice('desktop')">Desktop</button>
        <button type="button" class="btn" style="padding:7px 13px;font-size:12.5px" onclick="lpPreview()">↻ Refresh</button>
      </span>
    </h2>
    <p class="hint">Updates live as you edit below — generate when it looks right.</p>
    <div style="text-align:center;background:#0b0c0e;border:1px solid var(--line);border-radius:12px;padding:16px;overflow:hidden">
      <iframe id="lpFrame" style="width:390px;max-width:100%;height:620px;border:0;border-radius:14px;background:#fff;transition:width .2s" title="Live preview"></iframe>
    </div>
  </div>

  <div class="card">
    <h2>1 · Template</h2>
    <p class="hint">Pick the layout. You can regenerate with another anytime.</p>
    <div class="tpls"><?php render_cards($tpl); ?></div>
  </div>

  <div class="card">
    <h2>2 · App details</h2>
    <p class="hint">Pre-filled from the link where possible — override freely.</p>
    <div class="row">
      <label class="f"><span>App name</span><input type="text" name="name" value="<?= attr($meta['name']) ?>" required></label>
      <label class="f"><span>Developer / brand (optional)</span><input type="text" name="developer" value="<?= attr($meta['developer']) ?>"></label>
    </div>
    <label class="f"><span>Tagline (one line)</span><input type="text" name="tagline" value="<?= attr($meta['tagline']) ?>" placeholder="Everything you need, one tap away"></label>
    <label class="f"><span>Description</span><textarea name="description" placeholder="Short paragraph about the app"><?= esc($meta['description']) ?></textarea></label>
    <div class="row3">
      <label class="f"><span>Category</span><input type="text" name="category" value="<?= attr($meta['category']) ?>"></label>
      <label class="f"><span>Rating (0–5)</span><input type="number" step="0.1" min="0" max="5" name="rating" value="<?= attr($meta['rating']) ?>"></label>
      <label class="f"><span>Rating count</span><input type="text" name="rating_count" value="<?= attr($meta['rating_count']) ?>"></label>
    </div>
    <div class="row">
      <label class="f"><span>Downloads (optional)</span><input type="text" name="downloads" value="<?= attr($meta['downloads']) ?>" placeholder="1M+"></label>
      <label class="f"><span>App icon URL</span><input type="url" name="icon" value="<?= attr($meta['icon']) ?>" placeholder="https://.../icon.png"></label>
    </div>
    <label class="f"><span>Screenshot URLs — one per line (leave blank for placeholders)</span><textarea name="screenshots" placeholder="https://.../1.jpg&#10;https://.../2.jpg"><?= esc(implode("\n", $meta['screenshots'])) ?></textarea></label>
  </div>

  <div class="card">
    <h2>3 · Call to action</h2>
    <p class="hint">Where the buttons point. For Keitaro you can use a macro like <code>{offer}</code> or your click URL.</p>
    <div class="row">
      <label class="f"><span>CTA link</span><input type="text" name="cta_url" value="<?= attr($url ?: '#download') ?>" placeholder="{offer} or https://..."></label>
      <label class="f"><span>CTA button text (optional)</span><input type="text" name="cta_text" value="" placeholder="auto (Get the App / Descargar…)"></label>
    </div>
  </div>

  <div class="card">
    <h2>4 · Output options</h2>
    <div class="row3" style="margin-bottom:16px">
      <label class="f"><span>Language</span>
        <select name="lang">
          <?php foreach(supported_langs() as $lc=>$lname): ?><option value="<?= attr($lc) ?>" <?= $lang===$lc?'selected':'' ?>><?= esc($lname) ?></option><?php endforeach; ?>
        </select>
      </label>
      <label class="f"><span>Accent color</span>
        <div style="display:flex;gap:8px;align-items:center">
          <input type="color" id="accentColor" value="<?= attr($accentDefault) ?>" style="width:44px;height:40px;border:1px solid var(--line);border-radius:8px;background:var(--panel-deep);padding:2px;cursor:pointer" oninput="document.getElementsByName('accent')[0].value=this.value;window.lpPreview&&lpPreview()">
          <input type="text" name="accent" value="<?= attr($accentDefault) ?>" placeholder="#5b8cff" style="flex:1" oninput="var v=this.value;if(/^#[0-9a-fA-F]{6}$/.test(v))document.getElementById('accentColor').value=v">
        </div>
        <div style="display:flex;gap:7px;margin-top:9px;flex-wrap:wrap">
          <?php foreach(['#36d07c','#64b8ff','#a15bff','#49e0c4','#f7c625','#ff9357','#ff7da3','#16181d'] as $sw): ?><button type="button" title="<?= $sw ?>" style="width:24px;height:24px;border-radius:6px;border:1px solid var(--line);background:<?= $sw ?>;cursor:pointer" onclick="document.getElementsByName('accent')[0].value='<?= $sw ?>';document.getElementById('accentColor').value='<?= $sw ?>';window.lpPreview&&lpPreview()"></button><?php endforeach; ?>
        </div>
      </label>
      <label class="f"><span>File format</span><br>
        <span class="seg">
          <label><input type="radio" name="format" value="php" checked><span>index.php</span></label>
          <label><input type="radio" name="format" value="html"><span>index.html</span></label>
        </span>
      </label>
    </div>
    <div class="opts">
      <label class="opt"><input type="checkbox" name="kclient" value="1"><div><b>Add Keitaro Kclient (fail-open)</b><small>Prepends a fail-open preloading hook (forces .php). Fill the tracker URL + token below for a ready-to-run snippet.</small></div></label>
      <label class="opt"><input type="checkbox" name="localize" value="1" checked><div><b>Download images locally</b><small>Saves icon + screenshots into /assets so the page is self-contained.</small></div></label>
    </div>
    <div class="row" style="margin-top:14px">
      <label class="f"><span>Keitaro tracker URL <span class="muted">(only used if Kclient is on)</span></span><input type="text" name="tracker_url" value="" placeholder="https://go.yourtracker.com/"></label>
      <label class="f"><span>Campaign token <span class="muted">(only used if Kclient is on)</span></span><input type="text" name="tracker_token" value="" placeholder="xxxxxxxxxxxxxxxx"></label>
    </div>
  </div>

  <div class="card">
    <h2>5 · Trust &amp; SEO</h2>
    <p class="hint">Adds JSON-LD structured data (always), plus optional trust badges, cookie notice and a richer footer. Override the SEO tags if you want.</p>
    <div class="opts">
      <label class="opt"><input type="checkbox" name="trust_badges" value="1" checked><div><b>Trust badges + footer</b><small>Secure · Verified developer · Free, plus a copyright / support line above the footer.</small></div></label>
      <label class="opt"><input type="checkbox" name="cookie_banner" value="1" checked><div><b>Cookie consent banner</b><small>Small dismissible notice that links your privacy policy.</small></div></label>
      <label class="opt"><input type="checkbox" name="age18" value="1"><div><b>18+ / responsible-use note</b><small>Adds an age + responsible-use line (recommended for casino / betting).</small></div></label>
    </div>
    <div class="row" style="margin-top:14px">
      <label class="f"><span>Support email <span class="muted">(footer)</span></span><input type="text" name="support_email" value="" placeholder="support@yourdomain.com"></label>
      <label class="f"><span>Company / footer name</span><input type="text" name="company" value="" placeholder="defaults to the app name"></label>
    </div>
    <label class="f"><span>Custom page title <span class="muted">(SEO — optional)</span></span><input type="text" name="seo_title" value="" placeholder="leave blank to auto-generate"></label>
    <label class="f"><span>Meta description <span class="muted">(SEO — optional)</span></span><input type="text" name="seo_desc" value="" placeholder="leave blank to use the tagline"></label>
    <label class="f"><span>Social share image URL <span class="muted">(og:image — optional)</span></span><input type="url" name="og_image" value="" placeholder="https://.../share.jpg"></label>
  </div>

  <div class="card">
    <h2>6 · Compare to real apps <span class="muted" style="font-weight:400;font-size:13px">— optional</span></h2>
    <p class="hint">Paste links to other real apps on Google Play / the App Store (one per line). The generator fetches
      each one's <b>real</b> name, icon and rating from its own listing and shows them next to yours, sorted by actual
      rating — nothing here is invented. Leave blank to skip this section entirely.</p>
    <label class="f"><span>Competitor app links <span class="muted">(up to 6, one per line)</span></span>
      <textarea name="competitor_urls" placeholder="https://play.google.com/store/apps/details?id=...&#10;https://apps.apple.com/app/..."></textarea>
    </label>
  </div>

  <div style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:30px">
    <button class="btn lg" type="submit">⚙ Generate landing</button>
    <a class="btn sec lg" href="index.php">← Start over</a>
  </div>
  </form>

<?php
/* ============================================================ STATE 1: HOME */
else: ?>
  <div class="steps"><b>1. Drop the link</b> → 2. Edit → 3. Generate</div>
  <form method="post" action="index.php">
  <input type="hidden" name="action" value="fetch">
  <div class="card">
    <h2>Drop the app link</h2>
    <p class="hint">Google Play, App Store, or any URL. We'll try to auto-read the name, icon, screenshots and rating — then you edit before generating. Leave blank to fill everything manually.</p>
    <label class="f"><input type="url" name="url" placeholder="https://play.google.com/store/apps/details?id=..." autofocus></label>
    <div style="display:flex;gap:12px;align-items:center;flex-wrap:wrap">
      <button class="btn lg" type="submit">Fetch & continue →</button>
      <span class="muted" style="font-size:13px">or skip and fill in manually on the next screen</span>
    </div>
  </div>
  <div class="card">
    <h2>Templates</h2>
    <p class="hint">8 clean, white-safe app-promo layouts. Choose now or on the next step.</p>
    <div class="tpls"><?php render_cards('spotlight'); ?></div>
  </div>
  </form>
<?php endif; ?>
</main>

<script>
(function(){
  document.querySelectorAll('label.tpl input[name="template"]').forEach(function(r){
    r.addEventListener('change', function(){
      document.querySelectorAll('label.tpl').forEach(function(l){ l.classList.remove('sel'); });
      var lbl = r.closest('label.tpl'); if (lbl) lbl.classList.add('sel');
    });
  });
})();

/* live preview (edit screen only) */
(function(){
  var form=document.getElementById('genForm'), fr=document.getElementById('lpFrame');
  if(!form||!fr){return;}
  var t;
  window.lpPreview=function(){
    try{var fd=new FormData(form);fd.set('action','preview');
      fetch('index.php',{method:'POST',body:fd}).then(function(r){return r.text();}).then(function(h){fr.srcdoc=h;}).catch(function(){});}catch(e){}
  };
  window.lpDevice=function(d){fr.style.width=(d==='mobile')?'390px':'100%';};
  form.addEventListener('input',function(){clearTimeout(t);t=setTimeout(window.lpPreview,500);});
  form.addEventListener('change',function(){clearTimeout(t);t=setTimeout(window.lpPreview,250);});
  window.lpPreview();
})();
</script>

<footer class="wrap">White LP Factory · generates <code>output/&lt;app&gt;-&lt;template&gt;/</code> · self-contained HTML/PHP for Keitaro preloading</footer>
</body>
</html>
