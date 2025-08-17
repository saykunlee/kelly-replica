#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "$0")/../.." && pwd)"
INIT_DIR="$ROOT_DIR/z_info/init"
LOG_FILE="$INIT_DIR/init.log"

mkdir -p "$INIT_DIR"
touch "$LOG_FILE"

log() {
  printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$1" | tee -a "$LOG_FILE"
}

if ! command -v php >/dev/null 2>&1; then
  log "PHP가 설치되어 있지 않습니다. PHP CLI를 설치한 후 다시 실행하세요."
  exit 1
fi

log "초기화 스크립트를 시작합니다."
php "$INIT_DIR/init.php" | tee -a "$LOG_FILE"

exit ${PIPESTATUS[0]}


