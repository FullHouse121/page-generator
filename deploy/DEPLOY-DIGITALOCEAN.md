# Hosting generated landings — DigitalOcean origin + Cloudflare

The generated landings are **PHP** (they run `kclient.php`). They need a PHP host.
This sets up a cheap DigitalOcean droplet as the origin, with Cloudflare in front for
DNS, SSL, CDN, and to hide the origin IP.

```
Visitor → Cloudflare (white domain, proxied)  → DigitalOcean droplet (Apache+PHP)
                                                 index.php + kclient.php + kclient.lib.php
                                                 → Keitaro decides white vs offer
```

## One-time: prepare the droplet

Create a droplet (Marketplace → **LAMP on Ubuntu**, cheapest 1 GB plan, SSH key auth),
grab its **public IP**, then from inside this folder on your Mac:

```bash
ssh root@DROPLET_IP 'bash -s' < deploy/origin-setup.sh
```

This installs/verifies Apache + PHP (mbstring, curl, zip), enables HTTPS with a
self-signed cert (works with Cloudflare SSL = Full), sets web-root permissions, and
turns on a firewall (SSH + HTTP/HTTPS open for now).

## Deploy a landing

Generate one in the tool, then push the folder:

```bash
./deploy/deploy.sh output/<your-landing> DROPLET_IP            # → site root
./deploy/deploy.sh output/<your-landing> DROPLET_IP promo      # → /promo/
```

Then drop in your real Keitaro library next to it (see the main README):
`kclient.lib.php` (the KClient class) alongside `kclient.php` (the snippet).

Test directly first: `http://DROPLET_IP/` should render the white page.

## Put Cloudflare in front

1. Cloudflare → add the white domain (Free plan) → set its nameservers at your registrar.
2. **DNS:** `A` `@` → `DROPLET_IP` → **Proxied** (orange cloud). Add `www` too.
3. **SSL/TLS:** mode **Full**; turn on **Always Use HTTPS**.
   - For **Full (strict)**: create a Cloudflare **Origin Certificate**, paste the cert +
     key onto the droplet (`/etc/ssl/certs/` + `/etc/ssl/private/`), point
     `default-ssl.conf` at them, `systemctl reload apache2`.
4. **Caching:** leave default (PHP isn't cached). Do **not** add a "Cache Everything" rule
   on the landing path.

## Lock the origin to Cloudflare only

Once the domain works through Cloudflare, close direct access:

```bash
ssh root@DROPLET_IP 'bash -s' < deploy/cloudflare-lock.sh
```

Now `http://DROPLET_IP` times out and all traffic must go through Cloudflare — the origin
IP stays hidden. (Undo: `ufw allow 80/tcp && ufw allow 443/tcp && ufw reload`.)

## Notes
- **Don't commit real `kclient.php`** (it holds your tracker token) — keep it on the origin only.
- Re-deploying is just re-running `deploy.sh`; add `--clean` as a 4th arg to mirror/delete.
- One droplet can host many landings — use subfolders (`promo`, `promo2`, …) or separate
  domains all pointing at the same IP via Cloudflare.
