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
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>White LP Factory</title>
<style>
:root{--bg:#070a0f;--panel:#0e1016;--panel2:#141821;--line:rgba(255,255,255,.09);--text:#e9edf5;--muted:#8b93a7;--accent:#36d07c;--accent2:#2bb56a}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;background:var(--bg);color:var(--text);line-height:1.55;
  background-image:radial-gradient(rgba(54,208,124,.05) 1px,transparent 1px);background-size:26px 26px}
.wrap{max-width:1000px;margin:0 auto;padding:0 22px}
a{color:var(--accent)}
header.top{padding:30px 0 10px}
.brand{display:flex;align-items:center;gap:12px;font-weight:800;font-size:22px;letter-spacing:-.01em}
.brand .dot{width:30px;height:30px;border-radius:9px;background:linear-gradient(135deg,var(--accent),#0a6b3b);display:grid;place-items:center;font-size:16px}
.brand small{color:var(--muted);font-weight:500;font-size:13px;display:block}
.steps{display:flex;gap:8px;margin:18px 0 26px;color:var(--muted);font-size:13px;flex-wrap:wrap}
.steps b{color:var(--text)}
.card{background:var(--panel);border:1px solid var(--line);border-radius:18px;padding:26px;margin-bottom:20px}
.card h2{font-size:18px;margin-bottom:4px}
.card .hint{color:var(--muted);font-size:13.5px;margin-bottom:18px}
label.f{display:block;margin-bottom:16px}
label.f span{display:block;font-size:13px;color:var(--muted);margin-bottom:6px;font-weight:600}
input[type=text],input[type=url],input[type=number],textarea,select{width:100%;background:var(--panel2);border:1px solid var(--line);
  border-radius:11px;color:var(--text);padding:12px 14px;font-size:14.5px;font-family:inherit;outline:none}
input:focus,textarea:focus,select:focus{border-color:var(--accent)}
textarea{min-height:90px;resize:vertical}
.row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px}
.btn{display:inline-flex;align-items:center;gap:9px;background:var(--accent);color:#04130b;font-weight:800;border:0;cursor:pointer;
  padding:14px 28px;border-radius:12px;font-size:15px;transition:transform .12s,filter .12s}
.btn:hover{transform:translateY(-1px);filter:brightness(1.06)}
.btn.sec{background:var(--panel2);color:var(--text);border:1px solid var(--line)}
.btn.lg{padding:16px 34px;font-size:16px}
.tpls{display:grid;grid-template-columns:repeat(5,1fr);gap:12px}
label.tpl{background:var(--panel2);border:1px solid var(--line);border-radius:14px;padding:10px;cursor:pointer;transition:border-color .12s,transform .12s;display:block}
label.tpl:hover{transform:translateY(-2px)}
label.tpl.sel{border-color:var(--accent);box-shadow:0 0 0 1px var(--accent)}
label.tpl input{display:none}
.tpl .wf{margin-bottom:8px}
.tpl .tname{font-weight:700;font-size:13.5px}
.tpl .tdesc{color:var(--muted);font-size:11px;line-height:1.35;margin-top:2px}
.opts{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.opt{display:flex;gap:10px;align-items:flex-start;background:var(--panel2);border:1px solid var(--line);border-radius:11px;padding:12px 14px}
.opt input{margin-top:3px}
.opt b{font-size:14px}.opt small{color:var(--muted);font-size:12px;display:block}
.seg{display:inline-flex;background:var(--panel2);border:1px solid var(--line);border-radius:10px;overflow:hidden}
.seg label{padding:9px 16px;cursor:pointer;font-size:13.5px}
.seg input{display:none}
.seg input:checked + span{background:var(--accent);color:#04130b;font-weight:700}
.seg span{display:block;padding:9px 16px;margin:-9px -16px;border-radius:8px}
.warn{background:rgba(245,179,1,.08);border:1px solid rgba(245,179,1,.3);color:#f5d27a;border-radius:11px;padding:12px 16px;font-size:13.5px;margin-bottom:10px}
.ok{background:rgba(54,208,124,.08);border:1px solid rgba(54,208,124,.35);border-radius:14px;padding:18px 20px;margin-bottom:18px}
.ok h2{color:var(--accent)}
.filelist{font-family:ui-monospace,Menlo,Consolas,monospace;font-size:12.5px;color:var(--muted);background:var(--bg);border:1px solid var(--line);border-radius:10px;padding:14px;margin-top:12px}
.preview-thumb{display:flex;gap:10px;align-items:center;margin-bottom:14px}
.preview-thumb img{width:54px;height:54px;border-radius:12px;border:1px solid var(--line);object-fit:cover}
.muted{color:var(--muted)}
footer{color:var(--muted);font-size:12.5px;text-align:center;padding:30px 0}
@media(max-width:780px){.tpls{grid-template-columns:repeat(2,1fr)}.row,.row3,.opts{grid-template-columns:1fr}}
</style>
</head>
<body>
<header class="top"><div class="wrap">
  <div class="brand"><span class="dot">⬡</span><div>White LP Factory<small>Clean app landing pages for Keitaro · HTML / PHP + Kclient</small></div></div>
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
    ];
    $res = build_landing($form);
    $tname = $reg[$form['template']]['name'] ?? $form['template'];
?>
  <div class="steps">1. Link → 2. Edit → <b>3. Done ✓</b></div>
  <div class="ok">
    <h2>Landing generated</h2>
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

  <form method="post" action="index.php">
  <input type="hidden" name="action" value="generate">

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
          <option value="en" <?= $lang==='en'?'selected':'' ?>>English</option>
          <option value="es" <?= $lang==='es'?'selected':'' ?>>Español (MX)</option>
          <option value="pt" <?= $lang==='pt'?'selected':'' ?>>Português (BR)</option>
        </select>
      </label>
      <label class="f"><span>Accent color</span><input type="text" name="accent" value="<?= attr($accentDefault) ?>" placeholder="#5b8cff"></label>
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
    <p class="hint">5 clean, white-safe app-promo layouts. Choose now or on the next step.</p>
    <div class="tpls"><?php render_cards('spotlight'); ?></div>
  </div>
  </form>
<?php endif; ?>
</main>

<footer class="wrap">White LP Factory · generates <code>output/&lt;app&gt;-&lt;template&gt;/</code> · self-contained HTML/PHP for Keitaro preloading</footer>
</body>
</html>
