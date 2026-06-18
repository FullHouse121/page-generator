<?php
/**
 * kclient.example.php — REFERENCE ONLY.
 *
 * This is NOT a working Kclient. Keitaro generates a campaign-specific Kclient.php
 * for you. This file just documents how it plugs into the generated landings.
 *
 * ── How the "preloading" method works in these landings ─────────────────────
 *
 * When you tick "Add Keitaro Kclient.php" in the generator, every generated
 * index.php starts with:
 *
 *     <?php if (file_exists(__DIR__ . '/kclient.php')) { include __DIR__ . '/kclient.php'; } ?>
 *     ...the white landing HTML follows...
 *
 * So the white page is what lives on disk. The included kclient.php is what
 * Keitaro uses to decide, per request, what the visitor actually receives.
 *
 * ── Getting the real file ───────────────────────────────────────────────────
 *
 * 1. Keitaro admin → Campaigns → (your campaign) → click the "..." menu →
 *    "Integration" → choose "PHP" (a.k.a. local / Kclient integration).
 * 2. Set your campaign token / tracker domain when prompted and download the
 *    generated PHP file (often named click.php or kclient.php).
 * 3. Rename it to  kclient.php  and place it NEXT TO index.php in the landing
 *    folder, replacing the placeholder the generator created.
 * 4. Make sure the landing folder is served by PHP (not a static-only host).
 *
 * ── Typical shape of a real Keitaro Kclient (do not copy — yours differs) ────
 *
 *     <?php
 *     define('KEITARO_URL', 'https://your-tracker-domain.com');
 *     define('KEITARO_CAMPAIGN_TOKEN', 'xxxxxxxxxxxxxxxx');
 *     // ... Keitaro's client library code ...
 *     // It calls the tracker, applies your campaign's filters/flow, and either
 *     // lets this white page render or routes the visitor onward.
 *
 * Keep the filename exactly  kclient.php  so the landing picks it up.
 */
