#!/bin/bash
# Shared launch logic for White LP Factory.
# Called by "Start White LP Factory.command" and the .app bundle.
# Finds Node, installs deps on first run, starts the server, opens the browser.
#
# Env:
#   PORT          server port (default 8000)
#   LPF_NO_OPEN   set to 1 to skip opening the browser (for testing)

RUNNER="$(cd "$(dirname "$0")" && pwd)"
PORT="${PORT:-8000}"
URL="http://localhost:${PORT}"

note()  { osascript -e "display notification \"$1\" with title \"White LP Factory\"" >/dev/null 2>&1; }
alert() { osascript -e "display alert \"White LP Factory\" message \"$1\"" >/dev/null 2>&1; }

# --- locate Node (Finder-launched apps have a minimal PATH) ---
NODE="$(command -v node 2>/dev/null)"
if [ -z "$NODE" ]; then
  for p in /opt/homebrew/bin/node /usr/local/bin/node \
           "$HOME"/.nvm/versions/node/*/bin/node \
           /usr/local/n/versions/node/*/bin/node \
           /opt/local/bin/node; do
    [ -x "$p" ] && NODE="$p" && break
  done
fi
if [ -z "$NODE" ]; then
  echo "Node.js not found. Install it from https://nodejs.org and try again."
  alert "Node.js was not found. Install it from https://nodejs.org and try again."
  sleep 4
  exit 1
fi
export PATH="$(dirname "$NODE"):$PATH"

cd "$RUNNER" || { echo "Cannot find runner folder"; exit 1; }

# --- install deps on first run ---
if [ ! -d node_modules ]; then
  echo "First run: installing dependencies (one-time, ~1 min)…"
  note "Installing dependencies (one-time)…"
  if ! npm install; then
    echo "npm install failed."
    alert "Dependency install failed. Open Terminal in local-runner and run: npm install"
    sleep 4
    exit 1
  fi
fi

open_when_ready() {
  [ "$LPF_NO_OPEN" = "1" ] && return 0
  for _ in $(seq 1 60); do
    if curl -s -o /dev/null --max-time 1 "$URL"; then open "$URL"; return 0; fi
    sleep 0.5
  done
}

# --- already running? just open it ---
if curl -s -o /dev/null --max-time 2 "$URL"; then
  echo "White LP Factory is already running → $URL"
  [ "$LPF_NO_OPEN" = "1" ] || open "$URL"
  # keep the window alive so closing it doesn't look like a crash
  [ -t 1 ] && { echo "(You can close this window.)"; }
  exit 0
fi

# --- start: open browser when the port responds, then run the server ---
( open_when_ready ) &

echo ""
echo "  White LP Factory  →  $URL"
echo "  Starting… your browser will open automatically."
echo "  Close this window (or press Ctrl+C) to stop."
echo ""
PORT="$PORT" exec "$NODE" serve.mjs
