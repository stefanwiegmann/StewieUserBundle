Installation
============

User Bundle for Symfony Flex
----------------------------------

### Step 1: Install and enable the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require <package-name>
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Then, enable the bundle by adding it to the list of registered bundles
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
