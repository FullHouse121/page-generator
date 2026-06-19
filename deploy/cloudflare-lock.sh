#!/usr/bin/env bash
# cloudflare-lock.sh — restrict the origin so ONLY Cloudflare can reach it.
#
# Run ON the droplet, AFTER Cloudflare is live and you've confirmed the domain
# works through it. This closes direct origin access so nobody can bypass the
# proxy to find/abuse your real server.
#   ssh root@DROPLET_IP 'bash -s' < deploy/cloudflare-lock.sh
#
# To undo (reopen to everyone):  ufw allow 80/tcp && ufw allow 443/tcp && ufw reload
set -euo pipefail

echo "==> Removing open HTTP/HTTPS rules"
ufw delete allow 80/tcp  >/dev/null 2>&1 || true
ufw delete allow 443/tcp >/dev/null 2>&1 || true

echo "==> Allowing 80/443 only from current Cloudflare ranges"
count=0
for ip in $(curl -s https://www.cloudflare.com/ips-v4); do
  ufw allow from "$ip" to any port 80,443 proto tcp >/dev/null && count=$((count+1))
done
for ip in $(curl -s https://www.cloudflare.com/ips-v6); do
  ufw allow from "$ip" to any port 80,443 proto tcp >/dev/null 2>&1 && count=$((count+1)) || true
done
ufw reload >/dev/null

echo "✓ Locked: ${count} Cloudflare ranges allowed on 80/443. SSH stays open."
echo "  Direct http://DROPLET_IP will now time out — that's expected."
ufw status numbered
