#!/usr/bin/env bash
# origin-setup.sh — prepare a DigitalOcean LAMP droplet to serve white landings.
#
# Run ON the droplet, as root. Easiest from your Mac:
#   ssh root@DROPLET_IP 'bash -s' < deploy/origin-setup.sh
# or paste it into the droplet's web console.
#
# Idempotent: safe to run more than once.
set -euo pipefail
export DEBIAN_FRONTEND=noninteractive

echo "==> Updating packages"
apt-get update -y
apt-get install -y rsync ufw ssl-cert curl

echo "==> Ensuring Apache + PHP (and the extensions the pages use)"
if ! command -v apache2 >/dev/null 2>&1; then
  apt-get install -y apache2 php libapache2-mod-php
fi
apt-get install -y php-mbstring php-curl php-zip || true

echo "==> Enabling HTTPS + perf modules (self-signed cert works with Cloudflare SSL = Full)"
a2enmod ssl headers rewrite deflate expires >/dev/null 2>&1 || true
a2ensite default-ssl >/dev/null 2>&1 || true
# allow the landing's .htaccess (gzip + browser caching) to take effect
printf '<Directory /var/www/html>\n  AllowOverride All\n</Directory>\n' > /etc/apache2/conf-available/lpf-htaccess.conf
a2enconf lpf-htaccess >/dev/null 2>&1 || true
systemctl restart apache2

echo "==> Web root permissions (and removing the LAMP default placeholder page)"
mkdir -p /var/www/html
rm -f /var/www/html/index.html /var/www/html/index.htm
chown -R www-data:www-data /var/www/html

echo "==> Firewall: SSH + HTTP/HTTPS open (lock to Cloudflare later with cloudflare-lock.sh)"
ufw allow OpenSSH >/dev/null
ufw allow 80/tcp  >/dev/null
ufw allow 443/tcp >/dev/null
yes | ufw enable  >/dev/null
ufw status verbose

IP="$(curl -s --max-time 5 ifconfig.me || echo DROPLET_IP)"
echo ""
echo "Origin ready (IP: ${IP})."
echo "  1) From your Mac:   ./deploy/deploy.sh <landing-folder> ${IP}"
echo "  2) Test directly:   http://${IP}/"
echo "  3) Cloudflare: A record (proxied) -> ${IP}, SSL mode = Full, Always Use HTTPS on."
echo "  4) Then lock down:  ssh root@${IP} 'bash -s' < deploy/cloudflare-lock.sh"
