[![Build Status](https://travis-ci.org/kubk/students.svg?branch=master)](https://travis-ci.org/kubk/students)

### To install:
```sh
git clone https://github.com/kubk/students.git
composer install
psql -U user -d db < create-students-table.sql
./scripts/student-table-seeder.php 30
```

Run app using PHP's built-in web server:
```sh
php -S localhost:8001 -t public
```

### Demo:
Heroku demo: https://boiling-brook-29265.herokuapp.com/

### Testing:
1. Create a test database and edit connection params in config/config_test.php
2. Run tests via ```phpunit```
