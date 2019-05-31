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