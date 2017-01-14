To install:
```sh
git clone https://github.com/kubk/students.git
composer install
mysql -uuser -ppassword db < create-students-table.sql
./scripts/student-table-seeder.php 30
php -S localhost:8001 -t public
```

To run tests:
```sh
phpunit
```
