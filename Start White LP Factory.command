#!/bin/bash
# Double-click this to start White LP Factory.
# Opens a small status window and your browser at http://localhost:8000
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
exec /bin/bash "$HERE/local-runner/launch.sh"
