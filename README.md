# book_cart
Symfony framework 4.4 sample book cart application

```php
composer update 
```
Database name
```php
 book_store_app
```
Creating the Database Tables
```php
 php bin/console make:migration
```
```php
 php bin/console doctrine:migrations:migrate
```

```php
php bin/console make:entity --regenerate App
```
```php
 php bin/console doctrine:schema:update --force 
```

Add sample data to the database

```php
php bin/console doctrine:fixtures:load --append
```
 # Database Structure
 
![alt text](https://github.com/gayanramyakumara/book_cart/blob/master/public/database_structure.png)
