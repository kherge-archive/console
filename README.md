[![Build Status][]](https://travis-ci.org/box-project/console)
[![Latest Stable Version][]](https://packagist.org/packages/box-project/console)
[![Latest Unstable Version][]](https://packagist.org/packages/box-project/console)
[![Total Downloads][]](https://packagist.org/packages/box-project/console)

Console
=======

    composer require box-project/console

Console simplifies the process of building a command line application using the
[dependency injection][] design pattern. Input and output management is already
handled for you. You simply need to create your commands and register each as a
service.

```php
use Box\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;

$container = new ContainerBuilder();
$application = new Application($container);

$container->compile()
$application->run();
```

Requirements
------------

- `kherge/file` ~1.3
- `herrera-io/object-storage` ~1.0
- `symfony/config` ~2.5
- `symfony/console` ~2.5
- `symfony/dependency-injection` ~2.5
- `symfony/yaml` ~2.5

### Suggested

- `symfony/event-dispatcher` ~2.5
- `symfony/expression-language` ~2.5
- `symfony/framework-bundle` ~2.5

Getting Started
---------------

You need to be familiar with some of the third-party libraries that are used by
Console in order to be able to make sense of anything. These libraries come from
[Symfony][], an open source web application framework. For your convenience, the
documentation for the most relevant libraries are linked below.

- [Console][] - Manages all aspects of the console (input and output). When you
  author your commands you will be targeting this library.
- [DependencyInjection][] - Responsible for wiring all of the dependencies
  together. Also makes it possible to alter the defaults provided by the
  library to better suit your needs.
- [EventDispatcher][] - A simple implementation of the [mediator][] pattern.
  Enables events in the **Console** library and makes it possible to add a
  plugin system to your console application.

### Creating an Application

```php
use Box\Component\Console\Application;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
```

Before a new application can be created, a dependency injection container will
be needed. While any instance of `ContainerInterface` may be used, we will be
using an instance of `ContainerBuilder` (more detail on why later).

```php
$container = new ContainerBuilder();
```

With the container, a new `Application` instance can now be created.

```php
$app = new Application($container);
```

When an instance of `ContainerBuilder` is provided to `Application`, it will
automatically set parameters and register services needed to run the console
application. `Application` will not set parameters or register services that
already exist. It is important to note that only instances of `ContainerBuilder`
will cause `Application` to set the default parameters and register the default
services.

### Running an Application

Before we can begin process of running the console, the container must first be
compiled. [Compiling][] the container allows for some last minute processes to
occur.

```php
$container->compile();
```

With the compiled container, the application is ready to run.

```php
$app->run();
```

When the code in the examples above are run from a script in the command line,
the following output will be shown. It may be important to note that the output
may vary slightly depending on the age of the documentation and what libraries
were installed in addition to **Console**.

```
Console Tool

Usage:
  [options] command [arguments]

Options:
  --help           -h Display this help message
  --quiet          -q Do not output any message
  --verbose        -v|vv|vvv Increase the verbosity of messages: [...snip...]
  --version        -V Display this application version
  --ansi              Force ANSI output
  --no-ansi           Disable ANSI output
  --no-interaction -n Do not ask any interactive question

Available commands:
  help               Displays help for a command
  list               Lists commands
config
  config:current     Displays the current configuration
  config:list        Lists the registered extensions
  config:reference   Displays a configuration reference
container
  container:debug    Displays current services for an application
debug
  debug:container    Displays current services for an application
```

[Build Status]: https://travis-ci.org/box-project/console.png?branch=master
[Latest Stable Version]: https://poser.pugx.org/box-project/console/v/stable.png
[Latest Unstable Version]: https://poser.pugx.org/box-project/console/v/unstable.png
[Total Downloads]: https://poser.pugx.org/box-project/console/downloads.png

[dependency injection]: http://en.wikipedia.org/wiki/Dependency_injection
[mediator]: http://en.wikipedia.org/wiki/Mediator_pattern
[Compiling]: http://symfony.com/doc/current/components/dependency_injection/compilation.html

[Console]: http://symfony.com/doc/current/components/console/index.html
[DependencyInjection]: http://symfony.com/doc/current/components/dependency_injection/index.html
[EventDispatcher]: http://symfony.com/doc/current/components/event_dispatcher/index.html
[Symfony]: https://symfony.com/
