Installation
============

User Bundle for Symfony 5
----------------------------------

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

### Step 2: Settings and configuration

Register services for the bundle in the `config/services.yaml` file of your project:

```php
// config/services.yaml

// ...
services:

// ...

  App\Stefanwiegmann\UserBundle\:
      resource: '@StefanwiegmannUserBundle/*/*'
      exclude: '@StefanwiegmannUserBundle/{Entity}'
      tags: ['controller.service_arguments']
      autowire: true

```

Register routes:

```php
// config/routes.yaml
// ...
stefanwiegmann_user:
    resource: "@StefanwiegmannUserBundle/Controller/"
    type:     annotation
    prefix:   "{_locale}/"
    defaults:
      _locale: en
    requirements:
      _locale: en|de
```

Add path to twig config:

```php
// config/packages/twig.yaml
twig:
  // ...
  debug: '%kernel.debug%'
  strict_variables: '%kernel.debug%'
  form_themes: ['bootstrap_4_layout.html.twig']
```

Add minimum security configuration

```php
// config/security.yaml
security:
    encoders:
        App\Stefanwiegmann\UserBundle\Entity\User:
            algorithm: argon2i
    # https://symfony.com/doc/current/security.html#where-do-users-come-from-user-providers
    providers:
        # used to reload user from session & other features (e.g. switch_user)
        app_user_provider:
            entity:
                class: App\Stefanwiegmann\UserBundle\Entity\User
                property: username
    // ...
    firewalls:
      // ...
      main:
        anonymous: true
        guard:
            authenticators:
                - App\Stefanwiegmann\UserBundle\Security\LoginFormAuthenticator
        logout:
            path:   sw_user_logout

        switch_user: true
        // ...
    // ...

    // add some sane inheritance for logged in users without any groups assigned
    role_hierarchy:
        ROLE_USER: [ROLE_USER_USER_VIEW, ROLE_USER_ROLE_VIEW, ROLE_USER_GROUP_VIEW]
        // ...

    // make sure to leave login accessible for anybody (at least)
    # Easy way to control access for large sections of your site
    # Note: Only the *first* access control that matches will be used
    access_control:
        - { path: ^/login$, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        // ...
```
Add these paramaters:

```php
// config/services.yaml
parameters:
    locale: 'en'
    max_rows: 10
    from_email: 'from@somedomain.org'
    // ...
// ...
```

Setup doctrine extensions and enable all defaults

### Step 3: Fill some data
`php bin/console make:migration`
`php bin/console doctrine:migrations:migrate`
`bin/console user:fill-roles`
`bin/console user:fill-groups --all`

### Step 4: Layout / Shortcuts

#### Includes

This bundle is tested with and requires

- jquery-3.4.1
- popper-1.16.0
- bootstrap-4.4.1
- fontawesome-free-5.13.0

Make sure to include in your project.

#### Layout

Templates extend `layout.html.twig`. Make sure to provide this and define the follwoing blocks:

- main (security forms, register - usually public)
- content (all other content, usually behind some firewall)

You can include a usermenu into your nav like this:

```php
  {% embed "@StefanwiegmannUser/default/usermenu.html.twig" %}{% endembed %}
```
