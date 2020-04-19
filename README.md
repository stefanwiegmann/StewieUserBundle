Installation
============

User Bundle for Symfony 5
----------------------------------

### Prerequisites

#### Bootstrap / Fontawesome / JS

This bundle is tested with and requires

jquery-3.4.1
popper-1.16.0
bootstrap-4.4.1
fontawesome-free-5.13.0

Make sure to include in your project.

#### Layout

Templates extend `layout.html.twig`. Make sure to provide this and define the follwoing blocks:

main
content

The command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 1: Install and enable the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require stefanwiegmann/userbundle
```
The command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Then, if not using Flex, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.yaml` file of your project:

```php
// config/bundles.yaml

// ...
return [
            // ...
            App\Stefanwiegmann\UserBundle\StefanwiegmannUserBundle::class => ['all' => true],
];
```

### Step 2: Settings

### Step 3: Setup
`php bin/console make:migration`
`php bin/console doctrine:migrations:migrate`
`bin/console user:fill-roles`
`bin/console user:fill-groups --all`
