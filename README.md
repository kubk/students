# kubk/students [![Build Status](https://travis-ci.org/kubk/students.svg?branch=master)](https://travis-ci.org/kubk/students)

Junior PHP test task: https://github.com/codedokode/pasta/blob/master/student-list.md

Simple CRUD/Auth app to get familiar with Symfony components and Unit/Functional testing.

## To install
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

## Demo
Heroku demo: https://boiling-brook-29265.herokuapp.com/
> The app is hosted on a free Heroku dyno, so it need some time to wake up.

## Testing
1. Create a test database and edit connection params in config/config_test.php
2. Run tests via ```phpunit```
