#!/usr/bin/env bash
# deploy.sh — upload a generated landing folder to the DigitalOcean origin.
#
# Run from your Mac, inside the white-lp-factory folder:
#   ./deploy/deploy.sh <landing-folder> <droplet-ip> [remote-name]
#
# Examples:
#   ./deploy/deploy.sh output/lumina-notes-spotlight 203.0.113.10
#       -> served at  http://DROPLET_IP/        (the site root)
#   ./deploy/deploy.sh output/promo-x 203.0.113.10 promo
#       -> served at  http://DROPLET_IP/promo/  (a subfolder)
#
# Add --clean as a 4th arg to mirror (delete remote files missing locally).
set -euo pipefail

SRC="${1:?Usage: ./deploy/deploy.sh <landing-folder> <droplet-ip> [remote-name] [--clean]}"
IP="${2:?Need the droplet IP as the 2nd argument}"
NAME="${3:-}"
CLEAN="${4:-}"

SRC="${SRC%/}"
[ -d "$SRC" ] || { echo "Folder not found: $SRC"; exit 1; }
[ -f "$SRC/index.php" ] || [ -f "$SRC/index.html" ] || \
  echo "Warning: no index.php/index.html in $SRC — is this the right folder?"

USER_HOST="root@${IP}"
WEBROOT="/var/www/html"
DEST="${WEBROOT}${NAME:+/$NAME}"
DEL=""
[ "$CLEAN" = "--clean" ] && DEL="--delete"

echo "→ Deploying $SRC  →  ${USER_HOST}:${DEST}"
ssh "$USER_HOST" "mkdir -p '$DEST'"
rsync -az $DEL --exclude '.DS_Store' --exclude '*.zip' "$SRC"/ "${USER_HOST}:${DEST}/"

# readable by Apache/PHP; reminder if the Keitaro library is missing
ssh "$USER_HOST" "
  chown -R www-data:www-data '$DEST'
  find '$DEST' -type d -exec chmod 755 {} \;
  find '$DEST' -type f -exec chmod 644 {} \;
  if [ -f '$DEST/kclient.php' ] && [ ! -f '$DEST/kclient.lib.php' ]; then
    echo '  NOTE: kclient.php is present but kclient.lib.php (Keitaro library) is missing.'
  fi
"
echo "✓ Done.  Test: http://${IP}/${NAME}"
