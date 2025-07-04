#!/bin/bash

# PHP 개발 서버 관리 스크립트 (Cursor PHP Server 익스텐션과 동일한 기능)
# z_info 폴더에서 실행하여 로컬 PHP 서버를 관리

# 기본 설정
DEFAULT_PORT=3000
DEFAULT_HOST="localhost"
DEFAULT_DOCROOT="$(cd "$(dirname "$0")/.." && pwd)/public"
PID_FILE="$(pwd)/z_info/.php_server.pid"
LOG_FILE="$(pwd)/z_info/.php_server.log"

# 색상 정의
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 함수: 로그 출력
log() {
    echo -e "${GREEN}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
}

# 함수: 에러 출력
error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 함수: 경고 출력
warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# 함수: 정보 출력
info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

# 함수: 사용법 출력
show_usage() {
    echo "PHP 개발 서버 관리 스크립트 (z_info 폴더용)"
    echo ""
    echo "사용법: $0 [명령] [옵션]"
    echo ""
    echo "명령:"
    echo "  start     - PHP 개발 서버 시작"
    echo "  stop      - PHP 개발 서버 중지"
    echo "  restart   - PHP 개발 서버 재시작"
    echo "  status    - 서버 상태 확인"
    echo "  logs      - 로그 확인"
    echo "  help      - 도움말 표시"
    echo ""
    echo "옵션:"
    echo "  -p, --port PORT    - 포트 번호 (기본값: $DEFAULT_PORT)"
    echo "  -h, --host HOST    - 호스트 주소 (기본값: $DEFAULT_HOST)"
    echo "  -d, --docroot DIR  - 문서 루트 디렉토리 (기본값: $DEFAULT_DOCROOT)"
    echo "  -v, --verbose      - 상세 로그 출력"
    echo ""
    echo "예시:"
    echo "  $0 start                    # 기본 포트로 서버 시작 (z_info 폴더)"
    echo "  $0 start -p 8080            # 8080 포트로 서버 시작"
    echo "  $0 start -p 8080 -d public  # public 폴더를 문서 루트로 설정"
    echo "  $0 stop                     # 서버 중지"
    echo "  $0 restart                  # 서버 재시작"
    echo "  $0 status                   # 서버 상태 확인"
}

# 함수: 포트 사용 확인
check_port() {
    local port=$1
    if lsof -ti:$port > /dev/null 2>&1; then
        return 0  # 포트 사용 중
    else
        return 1  # 포트 비어있음
    fi
}

# 함수: PID 파일에서 프로세스 ID 읽기
get_pid_from_file() {
    if [ -f "$PID_FILE" ]; then
        cat "$PID_FILE" 2>/dev/null
    fi
}

# 함수: 프로세스가 실행 중인지 확인
is_process_running() {
    local pid=$1
    if [ -n "$pid" ] && kill -0 "$pid" 2>/dev/null; then
        return 0  # 실행 중
    else
        return 1  # 실행 중이 아님
    fi
}

# 함수: 서버 시작
start_server() {
    local port=$1
    local host=$2
    local docroot=$3
    local verbose=$4
    
    log "PHP 개발 서버 시작 중..."
    
    # 포트가 이미 사용 중인지 확인
    if check_port "$port"; then
        error "포트 $port가 이미 사용 중입니다."
        info "다른 포트를 사용하거나 기존 서버를 중지하세요."
        return 1
    fi
    
    # 문서 루트 디렉토리 확인
    if [ ! -d "$docroot" ]; then
        error "문서 루트 디렉토리 '$docroot'가 존재하지 않습니다. (public 폴더가 필요합니다)"
        return 1
    fi
    
    # PHP 명령어 확인
    if ! command -v php &> /dev/null; then
        error "PHP가 설치되어 있지 않습니다."
        return 1
    fi
    
    # z_info 폴더가 존재하는지 확인 (현재 디렉토리가 z_info인 경우)
    if [ ! -d "z_info" ] && [ "$(basename $(pwd))" != "z_info" ]; then
        error "z_info 폴더가 존재하지 않습니다."
        return 1
    fi
    
    # 서버 시작
    local log_output=""
    if [ "$verbose" = "true" ]; then
        log_output="$LOG_FILE"
    else
        log_output="/dev/null"
    fi
    
    cd "$docroot"
    php -S "$host:$port" -t . > "$log_output" 2>&1 &
    local server_pid=$!
    
    # PID 파일에 저장 (z_info 폴더에)
    echo "$server_pid" > "$PID_FILE"
    
    # 서버가 정상적으로 시작되었는지 확인
    sleep 2
    if is_process_running "$server_pid"; then
        log "PHP 개발 서버가 성공적으로 시작되었습니다!"
        info "서버 URL: http://$host:$port"
        info "문서 루트: $docroot"
        info "프로세스 ID: $server_pid"
        info "로그 파일: $LOG_FILE"
        info "중지하려면: $0 stop"
    else
        error "서버 시작에 실패했습니다."
        rm -f "$PID_FILE"
        return 1
    fi
}

# 함수: 서버 중지
stop_server() {
    log "PHP 개발 서버 중지 중..."
    
    local pid=$(get_pid_from_file)
    
    if [ -z "$pid" ]; then
        warning "PID 파일이 없습니다. 포트에서 프로세스를 찾아 중지합니다."
        local port=${PORT:-$DEFAULT_PORT}
        pid=$(lsof -ti:$port 2>/dev/null)
    fi
    
    if [ -n "$pid" ] && is_process_running "$pid"; then
        kill -TERM "$pid" 2>/dev/null
        sleep 1
        
        # 강제 종료가 필요한 경우
        if is_process_running "$pid"; then
            warning "프로세스가 정상 종료되지 않았습니다. 강제 종료합니다."
            kill -KILL "$pid" 2>/dev/null
        fi
        
        log "서버가 중지되었습니다."
    else
        warning "실행 중인 서버가 없습니다."
    fi
    
    # PID 파일 정리
    rm -f "$PID_FILE"
}

# 함수: 서버 상태 확인
check_status() {
    local pid=$(get_pid_from_file)
    local port=${PORT:-$DEFAULT_PORT}
    local host=${HOST:-$DEFAULT_HOST}
    local docroot=${DOCROOT:-$DEFAULT_DOCROOT}
    
    echo "=== PHP 개발 서버 상태 (z_info 폴더용) ==="
    echo "포트: $port"
    echo "호스트: $host"
    echo "문서 루트: $docroot"
    echo ""
    
    if [ -n "$pid" ] && is_process_running "$pid"; then
        echo -e "${GREEN}상태: 실행 중${NC}"
        echo "프로세스 ID: $pid"
        echo "서버 URL: http://$host:$port"
        
        # 포트 상태 확인
        if check_port "$port"; then
            echo -e "${GREEN}포트 상태: 사용 중${NC}"
        else
            echo -e "${RED}포트 상태: 비어있음 (프로세스는 실행 중)${NC}"
        fi
    else
        echo -e "${RED}상태: 중지됨${NC}"
        
        # 포트에서 다른 프로세스 확인
        local other_pid=$(lsof -ti:$port 2>/dev/null)
        if [ -n "$other_pid" ]; then
            echo -e "${YELLOW}경고: 포트 $port에서 다른 프로세스($other_pid)가 실행 중입니다.${NC}"
        fi
    fi
}

# 함수: 로그 확인
show_logs() {
    if [ -f "$LOG_FILE" ]; then
        echo "=== PHP 개발 서버 로그 ==="
        tail -f "$LOG_FILE"
    else
        warning "로그 파일이 없습니다."
    fi
}

# 메인 스크립트
main() {
    # 기본값 설정
    local command=""
    local port=$DEFAULT_PORT
    local host=$DEFAULT_HOST
    local docroot=$DEFAULT_DOCROOT
    local verbose="false"
    
    # 인수 파싱
    while [[ $# -gt 0 ]]; do
        case $1 in
            start|stop|restart|status|logs|help)
                command="$1"
                shift
                ;;
            -p|--port)
                port="$2"
                shift 2
                ;;
            -h|--host)
                host="$2"
                shift 2
                ;;
            -d|--docroot)
                docroot="$2"
                shift 2
                ;;
            -v|--verbose)
                verbose="true"
                shift
                ;;
            *)
                error "알 수 없는 옵션: $1"
                show_usage
                exit 1
                ;;
        esac
    done
    
    # 명령이 없으면 도움말 표시
    if [ -z "$command" ]; then
        show_usage
        exit 1
    fi
    
    # 명령 실행
    case $command in
        start)
            start_server "$port" "$host" "$docroot" "$verbose"
            ;;
        stop)
            stop_server
            ;;
        restart)
            stop_server
            sleep 2
            start_server "$port" "$host" "$docroot" "$verbose"
            ;;
        status)
            check_status
            ;;
        logs)
            show_logs
            ;;
        help)
            show_usage
            ;;
        *)
            error "알 수 없는 명령: $command"
            show_usage
            exit 1
            ;;
    esac
}

# 스크립트 실행
main "$@" 