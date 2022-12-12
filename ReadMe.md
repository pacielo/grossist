ReadMe
===

## Requirements

- php7.4
- symfony5.2
- bootstrap4

## Install

Les commandes pour l'installation:
	$ composer update
	$ composer install
	$ php bin/console doctrine:database:create
	$ php bin/console doctrine:schema:update --force
	$ php bin/console doctrine:fixtures:load

## Configuration

Modifier les parametres suivants pour votre serveur local, voir .env.exemple

.env

    DATABASE_URL=mysql://user:passwrod@localhost:3306/atu?serverVersion=5.7
    BASE_URLS=http://localhost/
    GOOGLE_RECAPTCHA_SITE_KEY=6Ley_k0UAAAAALX1xyCuNLx0-KgH_u8Gt1RSJDo8
    GOOGLE_RECAPTCHA_SECRET=6Ley_k0UAAAAAJ-_BKipd-j-YI_H_Dm-qU_9Y9Re

## Compte root 

login: info@relooke.com
pwd: admin_20201!	

## Compte PDS

login: pds@relooke.com
pwd: azerty	

## crontab

    php bin/console user:mail_omission

Sur la machine dans /var/www/projet/
> crontab -e
____________________________________________________________________________________________

# mails omission
0 0 * * * php /var/www/relooke-atu.relooke.com/bin/console user:mail_omission >> /var/www/relooke-atu.relooke.com/var/log/mail_omission.log
0 0 * * * php /var/www/relooke-atu.relooke.com/bin/console user:mail_omission2 >> /var/www/relooke-atu.relooke.com/var/log/mail_omission2.log
___________________________________________________________________________________________________

## site en maintenance

mise en maintenance cli

    php bin/console app:maintenance:lock on

mise en production cli 

    php bin/console app:maintenance:lock off

et maj public/.htccess comme 

```
<IfModule mod_rewrite.c>
  RewriteEngine on
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule (.+) index.php?p=$1 [QSA,L]

  RewriteCond %{DOCUMENT_ROOT}/maintenance.html -f
  RewriteCond %{SCRIPT_FILENAME} !maintenance.html
  RewriteRule ^.*$ /maintenance.html [R=503,L]

  RewriteCond %{DOCUMENT_ROOT}/maintenance.html -f
  RewriteRule ^(.*)$ - [env=MAINTENANCE:1]

  <IfModule mod_headers.c>
    Header set cache-control "max-age=0,must-revalidate,post-check=0,pre-check=0" env=MAINTENANCE
    Header set Expires -1 env=MAINTENANCE
  </IfModule>
</IfModule>

ErrorDocument 503 /maintenance.html
Options -Indexs
```    

et la template maintenance se trouve templates/maintenance.html

## Les commands pour mise à jour le site en production

> php bin/console app:maintenance:lock on
> svn up config/ src/ templates/ translations/ public/
> php bin/console cache:clear --no-warmup --env=prod
> php bin/console doctrine:schema:update --force
> chown www-data:www-data -R var data
> php bin/console app:maintenance:lock off

OR 

    make up

## mise à jour svn prod par svn test

1. supprimer le svn prod des répertoires: assets/ config/ public/ src/ templates/ translations/ composer.json package.json webpack.config.js ReadMe.md
2. copier et coller le svn test ces répertoires: assets/ config/ public/ src/ templates/ translations/ composer.json package.json webpack.config.js ReadMe.md, à svn prod
3. php bin/console doctrine:schema:update --force

## PHP-CS-Fixer

  $ mkdir --parents tools/php-cs-fixer
  $ composer require --working-dir=tools/php-cs-fixer friendsofphp/php-cs-fixer
  $ tools/php-cs-fixer/vendor/bin/php-cs-fixer fix src