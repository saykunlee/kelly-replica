#!/usr/bin/env bash

# 뷰 스키마 생성 스크립트
DB_HOST="localhost"
DB_PORT="3306"
DB_USER="root"
DB_PASS="uny2023"
DB_NAME="kelly"

# 뷰 파일 초기화
VIEW_FILE="schema_views.sql"
echo "-- Views for $DB_NAME" > $VIEW_FILE

# 뷰 목록 가져오기
VIEWS=$(mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -p$DB_PASS -e "SELECT TABLE_NAME FROM information_schema.views WHERE table_schema = '$DB_NAME';" -s -N)

for view in $VIEWS; do
    echo "Processing view: $view"
    
    # DROP VIEW 문 추가
    echo "DROP VIEW IF EXISTS \`$view\`;" >> $VIEW_FILE
    
    # CREATE VIEW 문 가져오기
    CREATE_STMT=$(mysql -h $DB_HOST -P $DB_PORT -u $DB_USER -p$DB_PASS -e "SHOW CREATE VIEW $DB_NAME.$view\G" | grep "Create View:" | sed 's/Create View: //')
    
    # DEFINER를 현재 사용자로 변경
    CREATE_STMT=$(echo "$CREATE_STMT" | sed "s/DEFINER=\`[^`]*\`@\`[^`]*\`/DEFINER=\`$DB_USER\`@\`$DB_HOST\`/g")
    
    echo "$CREATE_STMT;" >> $VIEW_FILE
    echo "" >> $VIEW_FILE
done

echo "Views schema generated: $VIEW_FILE"
