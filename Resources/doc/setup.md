Setup
=====

## Bundle Configuration

Open a command console, enter your project directory and execute the
following command to copy over bundle configuration:

```console
$ bin/console stewie:user:configuration
```

This will create `config/packages/stewie-user.yaml`, add folders under `uploads/.../...` for Avatars and add them to the projects `.gitignore`. Make sure to review these files and locations.

### Manual Configuration

Register services for the bundle in the `config/services.yaml` file of your project:

```php
// config/services.yaml

// ...
services:

// ...

  Stewie\UserBundle\:
      resource: '@StewieUserBundle/*/*'
      exclude: '@StewieUserBundle/{Entity}'
      tags: ['controller.service_arguments']
      autowire: true

```

Register routes:

```php
// config/routes.yaml
// ...
stefanwiegmann_user:
    resource: "@StewieUserBundle/Controller/"
    type:     annotation
    prefix:   "{_locale}/"
    defaults:
      _locale: en
    requirements:
      _locale: en|de
```

Add minimum security configuration

```php
// config/security.yaml
security:
    encoders:
        Stewie\UserBundle\Entity\User:
            algorithm: argon2i
    # https://symfony.com/doc/current/security.html#where-do-users-come-from-user-providers
    providers:
        # used to reload user from session & other features (e.g. switch_user)
        app_user_provider:
            entity:
                class: Stewie\UserBundle\Entity\User
                property: username
    // ...
    firewalls:
      // ...
      main:
        anonymous: true
        guard:
            authenticators:
                - Stewie\UserBundle\Security\LoginFormAuthenticator
        logout:
            path:   stewie_user_logout

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

### Setup

Open a command console, enter your project directory and execute the
following commands to

#### fill minimal user data

```console
$ bin/console stewie:user:fill-data
```

This would include a user `admin` with password `password` and an admin group with all roles assigned.

#### fill test user data

```console
$ bin/console stewie:user:fill-data --all
```

This would add more dummy users and groups.
