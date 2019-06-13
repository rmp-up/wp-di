[![Build Status](https://travis-ci.org/rmp-up/wp-di.svg?branch=master)](https://travis-ci.org/rmp-up/wp-di)
[![Coverage Status](https://coveralls.io/repos/github/rmp-up/wp-di/badge.svg?branch=master)](https://coveralls.io/github/rmp-up/wp-di?branch=master)

# WP DI

> PHP Dependency Injection for WordPress (based on Pimple)

This is nothing new but we added some magic:

* Compatible with every project using Pimple already
* Configuration via plain arrays (or other)
* "Less WordPress more OOP"

And still searching for other magic to apply.


## Getting started

Add the package to your project

```bash
composer require rmp-up/wp-di
```

and set up the container provider

```php
$container = \Pimple\Container();
$container->register(
    new \RmpUp\WpDi\Provider( require 'services.php' )
);
```

with as much config files as you like.
Those shall return an array like the following.


## Features

wp-di does not only support registering services but also:

* Register post-types
* Add action/filter handler (as service)
* Register wp-cli commands

See how simple it is with the following examples.


## Examples

### Services and parameters

Each thing is nested using the class-name of its provider:

```php
use \RmpUp\WpDi\Provider\Parameters;
use \RmpUp\WpDi\Provider\Services;

return [
    Parameters::class => [
        'some' => 'primitives',
        'like' => 42,
    ]
    
    Services::class => [
        Something::class => [
            'like' // injecting the parameter here
        ]
    ]
]
```


### Post-Types

Just needs the post-type name and the class that defines it:

```php
return [
    WpPostTypes::class => [
        'company' => \My\PostType\Company::class,
        'wolves' => \My\PostType\Wolves::class,
    ]
]
```

Such classes will be cast to array
and used for `register_post_type()`.


### Actions

Registering actions can be kept that simple too
and gets tricky if you need argument count and priorities:

```php
return [
    'init' => [
        InitPlugin::class,
    ],
]
```

Add a service "InitPlugin" which is invoked when the init-action occurs.


### And more

* Options
* wp-cli commands

Read how this works in the official documentation of every release.


## Contributing

We used this in older projects but still maintain this use this for my own projects,
so please [open an issue](https://github.com/rmp-up/wp-di/issues/new)
if there is anything we can help with.

If you'd like to contribute,
please fork the repository and make changes as you'd like.
Pull requests are warmly welcome.


## Related projects

Please also note the following projects
about dependency injection container in WordPress:

* [Pimple Dependency Injection Container](https://packagist.org/packages/pimple/pimple)
* [Plugin-Boilerplate by Gary Jones](https://github.com/GaryJones/plugin-boilerplate)

## Licensing

See the [LICENSE.txt](./LICENSE.txt) for details.
