# Paleidimas

### Prisijungimas į docker konteinerį
docker exec -it -u www-data app_pay_task bash

### Composer install
composer install

### Komnados paleidimas, kad paskaičiuotų komisinius
bin/console app:commissions-calculate public/input.csv

'public/input.csv', vieta iš kur imamas failas

### Testų paleidimas
./vendor/bin/phpunit --bootstrap vendor/autoload.php src/Tests
### Testų paleidimas su padengimu į tests direktoriją
./vendor/bin/phpunit --bootstrap vendor/autoload.php src/Tests --coverage-html /var/www/html/tests
