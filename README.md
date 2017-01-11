To install:
```sh
git clone https://github.com/kubk/students.git
composer install
cat create-students-table.sql | mysql -u<user> -p<password> <db>
./scripts/student-table-seeder.php 30
php -S localhost:8001 -t public
```

To run tests:
```sh
phpunit --bootstrap vendor/autoload.php tests/
```
