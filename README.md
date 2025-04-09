## Init 순서

1. clone
2. composer install
3. .env 파일 생성
4. php artisan key:generate
5. 권한변경 
   5-1. 서버 루트 접속
   5-2. sudo chmod -R gu+w storage/
    sudo chmod -R guo+w storage/
    sudo chmod -R gu+w bootstrap/cache/
    sudo chmod -R guo+w bootstrap/cache/
   5-3. 서버 이름으로 다시 접근 exit;
   5-4. php artisan storage:link