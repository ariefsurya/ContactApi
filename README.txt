## run api:
php artisan serve







## download  mysql:
https://dev.mysql.com/downloads/installer/

## mysql Create Database:
mysql -u root -p
CREATE DATABASE cnaindo_test;

## mysql Database add new user:
mysql -u root -p
CREATE USER 'userx'@'localhost' IDENTIFIED BY '$3cr3T@dmin123!!!';
GRANT ALL PRIVILEGES ON cnaindo_test.* TO 'userx'@'localhost';
FLUSH PRIVILEGES;


## add model database:
php artisan make:model Group -m

## migrate database:
php artisan migrate

## laravel Add API Resouce:
php artisan make:resource PostResource

## laravel Add API Controller:
php artisan make:controller Api/ContactController

## laravel add route for api
php artisan install:api

##packages
# laravel prevent XSS using ezyang/htmlpurifier
composer require ezyang/htmlpurifier --ignore-platform-req=ext-curl
composer require ezyang/htmlpurifier

# laravel read excel using maatwebsite/excel
composer require maatwebsite/excel


root: $3cr3T@dmin123#@!


