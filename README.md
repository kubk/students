### To install:
```sh
git clone https://github.com/kubk/students.git
composer install
mysql -uuser -ppassword db < create-students-table.sql
./scripts/student-table-seeder.php 30
```

Run app using built-in web server:
```sh
php -S localhost:8001 -t public
```

### Testing:
1. Create a test database and edit connection params in config/config_test.php
2. Run tests via ```phpunit```
