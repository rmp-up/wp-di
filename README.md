![](https://img.shields.io/badge/PHP-7.0%20--%207.4-blue?style=for-the-badge&logo=php)
![](https://img.shields.io/badge/WordPress-4.8%20--%205.5-blue?style=for-the-badge&logo=wordpress)

[![Build Status](https://travis-ci.org/rmp-up/wp-di.svg?branch=release/0.7)](https://travis-ci.org/rmp-up/wp-di)
[![Coverage Status](https://coveralls.io/repos/github/rmp-up/wp-di/badge.svg?branch=release/0.7)](https://coveralls.io/github/rmp-up/wp-di?branch=release/0.7)

# WP DI

> PHP Dependency Injection for WordPress (based on Pimple)

This is nothing new but we added some magic:

* Compatible with projects using Pimple already
* Configuration via plain arrays, Yaml or other
* "Less WordPress more OOP"

and still searching for other magic to apply.


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

Friends of YAML can add `composer require symfony/yaml`
and use

```php
$container->register(
  new \RmpUp\WpDi\Provider(
    \RmpUp\WpDi\Yaml::parseFile( 'services.yaml' )
  )
);
```


## Features

A full documentation can be found in the
[documentation of the latest releases](https://github.com/rmp-up/wp-di/releases).
The following is just a sneak peek into some of the possibilities.

### Services and parameters

Define services as known from classical DI but also ...

* Primitive parameters as usual
* Default values for options
* Path to templates
* Inject all of them into services

```yaml
# Primitive parameters as usual
parameters:
  some: "primitives"
  like: 42

# Default values for options
options:
  _my_plugin_rating: 5/7
  _this_is: cool

# Path to templates
templates:
  admin-view: my-own-plugin/template-parts/fester.php
  frontend-view: my-own-plugin/public/coogan.jpg
  # looks up the file in theme, theme-compat and plugin directory

# Inject all of them into services
services:
  SimpleOne:
    arguments:
      - "Hello there!" 
      - 1337
 
  SomeThing:
    arguments:
      - "%like%" # the parameter
      - "%_this_is%" # the option
      - "%frontend-view%" # path to the template
      - "@SimpleOne" # the other service
```


### Register services in WordPress

Services can also be used to ...

* Add actions / filters
* Add Meta-Boxes
* Register Post-Types
* Register Shortcodes
* Register Widgets
* Add WP-CLI commands

```yaml
services:
  StrrevEverything:
    filter: the_content
    # calling `::__invoke` for the "the_content"-filter

  BackendAdminListThing:
    meta_box:
      title: Greatest box in the World!
      screen: post

  MyOwnPostType:
    post_type: animals
    # cast service to array and forward to register_post_type

  BestShortcodeEver:
    shortcode: shortcode_wont_die
    widget: ~
    # Shortcode and widget at once. Wow!

  DoItCommand:
    wp_cli:
      do-it: __invoke
      doit: __invoke
      seriously do-it do-it do-it: seriously
      # cli commands mapped to methods 
```


### Use tags to enhance YAML

Within YAML you can:

* Access PHP-Constants
* Concatenate text
* Translate text

Mostly lazy to get the best performance.

```yaml
services:
  # Access PHP-Constants
  InjectingConstants:
    arguments:
      - !php/const ABSPATH

  # Concatenate text
  ThisIsSomeTemplate:
    arguments:
      - !join [ !php/const WP_CONTENT_DIR, "/plugins/grey-matter/walter.jpg" ]

  # Translations within YAML
  ThisThingNeedsTranslations:
    arguments:
      - !__ [ Who is Adam?, dark ]
      - !esc_attr__ [ white ]
      # ... many more translation functions available ...
```

All of this is only possible when using `\RmpUp\WpDi\Yaml::parseFile(...)`
or `::parse(...)`.


## Contributing

We used this in some projects
and still maintain/enhance it,
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
