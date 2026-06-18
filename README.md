# White LP Factory

Paste an app link → get a clean, self-contained landing page (HTML or PHP) ready to drop
into a **Keitaro** flow with the **Preloading** method, optionally wired to **Kclient.php**.

These are neutral, white-safe app-promo pages (app icon, screenshots, ratings, reviews,
privacy + terms). The cloaking/decisioning itself lives in *your* Keitaro Kclient — this
tool only builds the landing and the integration hook.

---

## Requirements

- **PHP 7.4+** with `curl`, `mbstring`, `zip` extensions (all standard).
- Nothing else — no Composer, no database, no build step.

> This Mac has no PHP, Homebrew, or Docker installed. Easiest path: drop this folder on any
> PHP host (your Keitaro server already runs PHP) and open `index.php` there. To run locally
> instead, install Homebrew first (https://brew.sh), then `brew install php`.
>
> The code was verified end-to-end on PHP 8.3 via a WASM runtime — all 5 templates render and
> all output branches (zip, Kclient include, `.php`/`.html`, placeholders) pass.

## Run the generator

**Option A — one-click (macOS, no terminal).**
Just double-click one of these in the `white-lp-factory` folder:

- **`White LP Factory.app`** — Dock app (custom icon). Quit it from the Dock to stop.
- **`Start White LP Factory.command`** — opens a small status window; close it to stop.

Either one finds Node, installs deps on first run (~1 min, one time), starts the server,
and opens your browser at `http://localhost:8000` automatically. Requires Node.js
(https://nodejs.org) — it'll prompt you if it's missing.

**Option B — terminal (no PHP needed).**
Runs the real PHP app via WordPress Playground's WASM PHP under Node — auto-fetch works too.

```bash
cd white-lp-factory/local-runner
npm install      # first time only
npm start        # → http://localhost:8000
```

**Option C — real PHP host (production, or if you have PHP).**

```bash
cd white-lp-factory
php -S localhost:8000
# open http://localhost:8000
```

Either way, **deploying** a generated landing just means uploading its `output/<...>/`
folder to your white domain (a PHP host, since the page may include `kclient.php`).

## Workflow

1. **Drop the link** — Google Play / App Store / any URL. It auto-reads name, icon,
   screenshots and rating where possible.
2. **Edit** — fix any field, pick one of the 5 templates, choose language (EN/ES/PT),
   accent color, output format (`.php` or `.html`), and whether to include Kclient.
3. **Generate** — writes `output/<app>-<template>/` and a `.zip`. Preview in-browser,
   then upload the folder to your white domain.

## The 5 templates

| Key        | Name        | Look |
|------------|-------------|------|
| `spotlight`| Spotlight   | Gradient hero, phone mockup, feature grid, reviews |
| `minimal`  | Minimal     | Clean light, Apple-style, centered, single CTA |
| `storehero`| Store Hero  | Mimics an app-store listing: install bar, rating breakdown |
| `feature`  | Feature Flow| Dark, alternating feature blocks, stats, sticky CTA |
| `bold`     | Bold Dark   | Big type, glow, store badges, FAQ accordion |

Each generated page is **self-contained** (inline CSS, inline SVG icons, local assets)
and ships with `privacy.html`, `terms.html`, and `robots.txt` — the legal pages help with
ad-platform review.

## Keitaro + Kclient (preloading)

When you tick **“Add Keitaro Kclient.php”**, the generated `index.php` begins with:

```php
<?php if (file_exists(__DIR__ . '/kclient.php')) { include __DIR__ . '/kclient.php'; } ?>
```

A no-op placeholder `kclient.php` is created so the page renders immediately. To go live:

1. Keitaro → **Campaign → Integration → PHP (local / Kclient)** → download the generated file.
2. Rename it to **`kclient.php`** and drop it in the landing folder (replace the placeholder).
3. Upload the folder to your white domain; add it to the flow as the **landing** with the
   **Preloading** action.

See `kclient/kclient.example.php` for the full walkthrough. Enabling Kclient forces `.php`
output (HTML can't run the include).

## Project layout

```
white-lp-factory/
├─ index.php            # the web UI (paste → edit → generate)
├─ lib/
│  ├─ fetcher.php       # reads app metadata from a link
│  ├─ builder.php       # renders a template + packages the output
│  ├─ templates.php     # template registry + render helpers (icons, stars)
│  ├─ legal.php         # privacy / terms generator (EN/ES/PT)
│  └─ helpers.php       # http, zip, escaping, slugs
├─ templates/           # the 5 layouts
├─ kclient/             # Kclient reference + instructions
└─ output/             # generated landings land here (gitignored)
```

## Notes

- **Auto-fetch is best-effort.** Stores change their markup and sometimes block servers;
  whatever can't be read is left for you to fill in. App Store links parse most reliably.
- **Screenshots/icon** are downloaded into `/assets` by default so pages are portable.
  Untick “Download images locally” to hotlink instead.
- No screenshots found? Neutral SVG placeholders are inserted so layouts never break.
