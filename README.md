1. Install symfony CLI:
https://symfony.com/download

2. php composer.phar install

3. Create Database:
a. php bin/console doctrine:database:create
b. php bin/console make:migration 
c. php bin/console doctrine:migrations:migrate

3. symfony server:start
