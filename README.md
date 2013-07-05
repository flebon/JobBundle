Symfony2 bundle generating dynamic filtered (client-side) lists using jQuery.


WORKS ONLY WITH MYSQL

The database could be independant from your application.


Dependencies
============

VVGFilterListBundle : https://github.com/vvanghelue/FilterListBundle,

Doctrine

Installation
===========

To install this bundle please follow the next steps:

First add the dependencies to your `composer.json` file:

```json
"require": {
    ...
    "Tessi-Tms/JobBundle": "dev-master",
    "vvg/filterlist-bundle": "dev-master",
},
```

Then install the bundle with the command:

```sh
php composer update
```

Enable the bundle in your application kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new VVG\Bundle\FilterListBundle\VVGFilterListBundle(),
        new Tessi\JobBundle\JobBundle(),
    );
}
```

Edit your doctrine connection in your `app/config/config.yml`:

```yaml
doctrine:
    dbal:
        default_connection:   default
        connections:
            default:
                driver:   "%database_driver%"
                host:     "%database_host%"
                port:     "%database_port%"
                dbname:   "%database_name%"
                user:     "%database_user%"
                password: "%database_password%"
                charset:  UTF8
            
            ...
            
            #Put your job database config here
            job:
                driver:   "%database_driver%"
                host:     "%database_host%"
                port:     "%database_port%"
                dbname:   "%database_name%"
                user:     "%database_user%"
                password: "%database_password%"
                charset:  UTF8
```

And then for your orm mappings :

```yaml
    orm:
        default_entity_manager:   default
        entity_managers:
            default:
                connection:       default
                mappings: ~
                
            ...
            
            #Declare your entity manager
            job:
                connection:       job
                mappings:
                    JobBundle: ~
```

Update your schema
```sh
php app/console doctrine:schema:update --em=job --force
```

Crontab
=======
```sh
* * * * * echo "cron 0"; /home/dev/workspace/YourProject/app/console job:run >> /var/log/cron_your_project.log
* * * * * sleep 5; echo "cron 5"; /home/dev/workspace/YourProject/app/console job:run >> /var/log/cron_your_project.log
* * * * * sleep 10; echo "cron 10"; /home/dev/workspace/YourProject/app/console job:run >> /var/log/cron_your_project.log
* * * * * sleep 15; echo "cron 15"; /home/dev/workspace/YourProject/app/console job:run >> /var/log/cron_your_project.log
* * * * * sleep 20; echo "cron 20"; /home/dev/workspace/YourProject/app/console job:run >> /var/log/cron_your_project.log
* * * * * sleep 25; echo "cron 25"; /home/dev/workspace/YourProject/app/console job:run >> /var/log/cron_your_project.log
* * * * * sleep 30; echo "cron 30"; /home/dev/workspace/YourProject/app/console job:run >> /var/log/cron_your_project.log
* * * * * sleep 35; echo "cron 35"; /home/dev/workspace/YourProject/app/console job:run >> /var/log/cron_your_project.log
* * * * * sleep 40; echo "cron 40"; /home/dev/workspace/YourProject/app/console job:run >> /var/log/cron_your_project.log
* * * * * sleep 45; echo "cron 45"; /home/dev/workspace/YourProject/app/console job:run >> /var/log/cron_your_project.log
* * * * * sleep 50; echo "cron 50"; /home/dev/workspace/YourProject/app/console job:run >> /var/log/cron_your_project.log
* * * * * sleep 55; echo "cron 55"; /home/dev/workspace/YourProject/app/console job:run >> /var/log/cron_your_project.log
```

Usage
=====
@TODO

Security
========
@TODO
