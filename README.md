FilterLitstBundle
=================

Symfony2 bundle generating dynamic filtered (client-side) lists using jQuery.


ONLY WORKS WITH MYSQL

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

Edit your doctrine connection in your config.yml:

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

