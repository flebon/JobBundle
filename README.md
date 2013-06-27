FilterLitstBundle
=================

Symfony2 bundle generating dynamic filtered (client-side) lists using jQuery.

It is using VVGFilterListBundle : https://github.com/vvanghelue/FilterListBundle


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
