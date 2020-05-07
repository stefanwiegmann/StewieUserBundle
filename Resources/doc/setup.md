Setup
=====

## Bundle Configuration

### Automatic Configuration

Open a command console, enter your project directory and execute the
following command to copy over bundle configuration:

```console
$ bin/console stewie:user:configure
```

Pick which part to auto-configure. This will create `config/packages/stewie-user.yaml`, `config/routes/stewie-user.yaml`, add lines to `config/services.yaml` and add folders under `var/stewie/user-bundle/...` for Avatars. Make sure to review these files and locations. `config/packages/security.yaml` will need manual configuration as per below.

### Manual Configuration

#### Add minimum security configuration

```php
// config/security.yaml
security:
    # ...
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
    # ...
    firewalls:
        # ...
        main:
            anonymous: true
            guard:
                authenticators:
                    - Stewie\UserBundle\Security\LoginFormAuthenticator
            logout:
                path:   stewie_user_logout

            switch_user: true
            # ...
    # add some sane inheritance for logged in users without any groups assigned
    role_hierarchy:
        ROLE_USER: [ROLE_USER_USER_VIEW, ROLE_USER_ROLE_VIEW, ROLE_USER_GROUP_VIEW]
        # ...

    # make sure to leave login accessible for anybody (at least)
    # Easy way to control access for large sections of your site
    # Note: Only the *first* access control that matches will be used
    access_control:
        - { path: ^/login$, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/user, roles: ROLE_USER_USER_VIEW }
        # ...
# ...
```

#### Register services for the bundle in the `config/services.yaml` file of your project

```php
// config/services.yaml

// ...
services:

// ...

###> stewie/user-bundle ###
    Stewie\UserBundle\:
        resource: '@StewieUserBundle/*/*'
        exclude: '@StewieUserBundle/{Entity}'
        tags: ['controller.service_arguments']
        autowire: true
###< stewie/user-bundle ###

```

#### Register routes

```php
// config/routes/stewie-user.yaml
// ...
stewie_user:
    resource: "@StewieUserBundle/Controller/"
    type:     annotation
    prefix:   "{_locale}/"
    defaults:
      _locale: en
    requirements:
      _locale: en|de
```

### Populate data

#### fill minimal user data

```console
$ php bin/console stewie:user:fill-data
```

This would include a user `admin` with password `password` and an admin group with all roles assigned.

#### fill test user data

```console
$ php bin/console stewie:user:fill-data --all
```

This would add more dummy users and groups.
