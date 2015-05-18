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

```
$ ./example
Console Tool

Usage:
  [options] command [arguments]

Options:
  --help           -h Display this help message
  --quiet          -q Do not output any message
  --verbose        -v|vv|vvv Increase the verbosity of [snip]
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

You must be familiar with some of the third-party libraries that are used by
Console in order to be able to make sense of anything. These libraries come from
[Symfony][], an open source web application framework. For your convenience, the
documentation for the most relevant libraries are linked below.

- [Console][] - Manages all aspects of the console (input and output). When you
  author your commands you will be targeting this library.
- [DependencyInjection][] - Responsible for wiring all of the dependencies
  together.
- [EventDispatcher][] - A simple implementation of the [mediator][] pattern.
  Enables events in the **Console** library, and makes it possible to add a
  plugin system to your console application.

### Creating an Application

To create a new application, you will first need to create a new container.

```php
use Symfony\Component\DependencyInjection\ContainerBuilder;

$container = new ContainerBuilder();
```

Next, you need to create a new application using the container.

```php
use Box\Component\Console\Application;

$app = new Application($container);
```

When an instance of `ContainerBuilder` is provided to `Application`, it will
automatically set parameters and register services needed to run the console
application. `Application` will not set parameters or register services that
already exist.

### Running an Application

Once you have created your application, you will need to compile the container
before you run the application. This gives the container a chance to perform
some last minute processing.

```php
$container->compile();

$app->run();
```

Putting it all together in a script called **example**, it should look something
like the following example.

```php
use Box\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;

$container = new ContainerBuilder();
$app = new Application($container);

$container->compile();
$app->run();
```

When you run **example**, you should see the following output.

```
Console Tool

Usage:
  [options] command [arguments]

Options:
  --help           -h Display this help message
  --quiet          -q Do not output any message
  --verbose        -v|vv|vvv Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
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

[Console]: http://symfony.com/doc/current/components/console/index.html
[DependencyInjection]: http://symfony.com/doc/current/components/dependency_injection/index.html
[EventDispatcher]: http://symfony.com/doc/current/components/event_dispatcher/index.html
[Symfony]: https://symfony.com/
