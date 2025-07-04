#!/bin/bash

# 기본값 설정
DEFAULT_DB_USER="root"
DEFAULT_DB_PASSWORD="uny2023"
DEFAULT_DB_NAME="kelly"

# 사용자로부터 입력 받기 (기본값 표시)
read -p "데이터베이스 계정을 입력하세요 [$DEFAULT_DB_USER]: " db_user
db_user=${db_user:-$DEFAULT_DB_USER}

read -sp "패스워드를 입력하세요 [$DEFAULT_DB_PASSWORD]: " db_password
db_password=${db_password:-$DEFAULT_DB_PASSWORD}
echo

read -p "복구할 데이터베이스 이름을 입력하세요 [$DEFAULT_DB_NAME]: " db_name
db_name=${db_name:-$DEFAULT_DB_NAME}

# 현재 폴더의 SQL 파일 목록 가져오기
echo "사용 가능한 백업 파일 목록:"
sql_files=(*.sql)
if [ ${#sql_files[@]} -eq 0 ]; then
    echo "현재 폴더에 SQL 파일이 없습니다."
    exit 1
fi

# 파일 목록 출력
for i in "${!sql_files[@]}"; do
    echo "$((i+1)). ${sql_files[$i]}"
done

# 파일 선택
while true; do
    read -p "복구할 파일 번호를 선택하세요 (1-${#sql_files[@]}): " file_num
    if [[ "$file_num" =~ ^[0-9]+$ ]] && [ "$file_num" -ge 1 ] && [ "$file_num" -le ${#sql_files[@]} ]; then
        backup_file="${sql_files[$((file_num-1))]}"
        break
    else
        echo "올바른 번호를 입력하세요 (1-${#sql_files[@]})"
    fi
done

# 파일 존재 여부 확인
if [ ! -f "$backup_file" ]; then
    echo "지정한 파일이 존재하지 않습니다: $backup_file"
    exit 1
fi

# mariadb 복구 명령어 실행 및 오류 처리
if mariadb -u "$db_user" -p"$db_password" "$db_name" < "$backup_file"; then
    echo "복구가 완료되었습니다: $backup_file"
else
    echo "복구 중 오류가 발생했습니다. 입력 정보를 확인하고 다시 시도하세요."
    exit 1
fi
