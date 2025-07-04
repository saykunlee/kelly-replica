#!/bin/bash

# 기본값 설정
DEFAULT_DB_USER="kelly"
DEFAULT_DB_PASSWORD="uny2024"
DEFAULT_DB_NAME="kelly"

# 사용자로부터 입력 받기 (기본값 표시)
read -p "데이터베이스 계정을 입력하세요 [$DEFAULT_DB_USER]: " db_user
db_user=${db_user:-$DEFAULT_DB_USER}

read -sp "패스워드를 입력하세요 [$DEFAULT_DB_PASSWORD]: " db_password
db_password=${db_password:-$DEFAULT_DB_PASSWORD}
echo

read -p "백업할 데이터베이스 이름을 입력하세요 [$DEFAULT_DB_NAME]: " db_name
db_name=${db_name:-$DEFAULT_DB_NAME}

# 현재 날짜와 시간을 파일명에 추가
backup_time=$(date +"%Y%m%d%H%M%S")
backup_file="${db_name}-backup-${backup_time}.sql"

# mysqldump 명령어 실행 및 오류 처리 (UTF8MB4 강제 지정)
if mariadb-dump -u --default-character-set=utf8mb4 \
             --routines --triggers \
             -u "$db_user" -p"$db_password" "$db_name" > "./$backup_file"; then
    echo "백업이 완료되었습니다: $backup_file"
else
    echo "백업 중 오류가 발생했습니다. 입력 정보를 확인하고 다시 시도하세요."
    exit 1
fi