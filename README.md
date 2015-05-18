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

Using the Container
-------------------

`Application` is designed around the use of the container. All functionality
that is provided by **Console** can be found as a parameter or service within
the container. As a result, all changes to the console (adding commands, adding
helpers, changing defaults, etc) must also occur through the container.

### Loading Resources

In order to make changes to the container, the loaders provided by the
**DependencyInjection** library must be used. More information about how to
use the [DI loaders][] can be found on Symfony's website. While you may use
any compatible loader, **Console** will only officially support XML and YAML
for file-based loading. PHP is also supported, but not in conjunction with
the bundled commands or loaders.

#### Loading `.dist` Files

In addition to the standard loaders, **Console** provides its own loader for
special cases. Many applications make use of files that end with `.dist`. This
file extension is used to indicate that the file is part of the distribution.
A user may then make a copy of the file, drop the `.dist` extension, and use
their version of the file with their software.

The following example will support the loading of XML and YAML files, with or
without the `.dist` file extension.

```php
use Box\Component\Console\Loader\Resource;
use Box\Component\Console\Loader\ResourceCollection;
use Box\Component\Console\Loader\ResourceCollectionLoader;
use Box\Component\Console\Loader\ResourceSupport;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

// load files from the current directory
$locator = new FileLocator('.');

// create a loader for xml and yaml files
$loader = new ResourceCollectionLoader(
    new LoaderResolver(
        array(
            new XmlFileLoader($container, $locator),
            new YamlFileLoader($container, $locator)
        )
    )
);

// load the first available file from a collection of possible resources
$loader->load(
    new ResourceCollection(
        array(
            new Resource('example.xml'),
            new ResourceSupport('example.xml.dist', 'example.xml'),
            new Resource('example.yml'),
            new ResourceSupport('example.yml.dist', 'example.yml')
        )
    )
);
```

As the name implies, the `ResourceCollection` class manages a collection of
`Resource` instances. Because of how the standard file loaders behave when
determining support for files, `ResourceSupport` is used to map an unsupported
file extension (e.g. `.yml.dist`) a supported one (e.g. `.yml`). The loader,
`ResourceCollectionLoader`, will then iterate through the collection attempting
load each resource until one is successfully loaded. If the first resource in
the collection fails to load due to it not existing, the next will be attempted.
This iteration will continue until the list is exhausted, or an error is found
while processing an available resource. 

In the example above, an exception is thrown if none of the resources in the
collection exist. To optionally load a resource, without having an exception
thrown, the `loadOptional()` method should be used.

```php
$loader->loadOptional(
    new ResourceCollection(
        array(
            new Resource('example.xml'),
            new ResourceSupport('example.xml.dist', 'example.xml'),
            new Resource('example.yml'),
            new ResourceSupport('example.yml.dist', 'example.yml')
        )
    )
);
```

### Registering Commands

To register a command, you must tag its service with "box.console.command".

#### In PHP

```php
$definition = new Definition('My\Command');
$definition->addTag('box.console.command');

$container->setDefinition('my_command', $definition);
```

#### In XML

```xml
<container>
  <services>
    <service class="My\Command" id="my_command">
      <tag name="box.console.command"/>
    </service>
  </services>
</container>
```

#### In YAML

```yaml
services:

    my_command:
        class: My\Command
        tags:
            - { name: box.console.command }
```

### Registering Helpers

To register a command, you must tag its service with "box.console.helper".

#### In PHP

```php
$definition = new Definition('My\Helper');
$definition->addTag('box.console.helper');

$container->setDefinition('my_helper', $definition);
```

#### In XML

```xml
<container>
  <services>
    <service class="My\Helper" id="my_helper">
      <tag name="box.console.helper"/>
    </service>
  </services>
</container>
```

#### In YAML

```yaml
services:

    my_helper:
        class: My\Helper
        tags:
            - { name: box.console.helper }
```

### Registering Event Listeners and Subscribers

The **EventDispatcher** library supports the registration of listeners, or a
collection of listeners through what is known as a "subscriber". A listener
is a single callable, while a subscriber is a class that returns a list of
which methods must be called when specific events a dispatched.

#### Listeners

##### In PHP

```php
$definition = new Definition('My\Listener');
$definition->addTag(
    'box.console.event.listener',
    array(
        'event' => 'the.event',
        'method' => 'onEvent'
    )
);

$container->setDefinition('my_listener', $definition);
```

##### In XML

```xml
<container>
  <services>
    <service class="My\Listener" id="my_listener">
      <tag name="box.console.event.listener" event="the.event" method="onEvent"/>
    </service>
  </services>
</container>
```

##### In YAML

```yaml
services:

    my_listener:
        class: My\Listener
        tags:
            - name: box.console.event.listener
              event: the.event
              method: onEvent
```

#### Subscribers

##### In PHP

```php
$definition = new Definition('My\Subscriber');
$definition->addTag('box.console.event.subscriber');

$container->setDefinition('my_subscriber', $definition);
```

##### In XML

```xml
<container>
  <services>
    <service class="My\Subscriber" id="my_subscriber">
      <tag name="box.console.event.subscriber"/>
    </service>
  </services>
</container>
```

##### In YAML

```yaml
services:

    my_subscriber:
        class: My\Subscriber
        tags:
            - { name: box.console.event.subscriber }
```

The Defaults
------------

As mentioned early in **Getting Started**, when a `ContainerBuilder` instance
is passed to `Application`, a set of default parameters and services are set
within the container. The following is a list of those parameters and services.

### Parameters

<table>
  <thead>
    <tr>
      <th>Name (Default Value)</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        <code>box.console.auto_exit</code>
        <br/>
        (<code>true</code>)
      </td>
      <td>If <code>true</code>, <code>exit()</code> is called once a command finishes.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.class</code>
        <br/>
        (<code>Symfony\Component\Console\Application</code>)
      </td>
      <td>The class for the console application.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.command.*.class</code>
        <br/>
        (Instances of <code>Symfony\Component\Console\Command\Command</code>)
      </td>
      <td>The class for each default command.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.event_dispatcher.class</code>
        <br/>
        (<code>Symfony\Component\EventDispatcher\ContainerAwareDispatcher</code>)
      </td>
      <td>The class for the event dispatcher.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.helper.*.class</code>
        <br/>
        (Instances of <code>Symfony\Component\Console\Helper\Helper</code>)
      </td>
      <td>The class for each default helper.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.helper.container.class</code>
        <br/>
        (<code>Box\Component\Console\Helper\ContainerHelper</code>)
      </td>
      <td>The class a helper that provides access to the container.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.helper_set.class</code>
        <br/>
        (<code>Symfony\Component\Console\Helper\HelperSet</code>)
      </td>
      <td>The class for the helper set.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.input.class</code>
        <br/>
        (<code>Symfony\Component\Console\Input\ArgvInput</code>)
      </td>
      <td>The class for the default input manager.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.name</code>
        <br/>
        (<code>UNKNOWN</code>)
      </td>
      <td>The name of the console application.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.output.class</code>
        <br/>
        (<code>Symfony\Component\Console\Output\ConsoleOutput</code>)
      </td>
      <td>The class for the default output manager.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.version</code>
        <br/>
        (<code>UNKNOWN</code>)
      </td>
      <td>The version of the console application.</td>
    </tr>
  </tbody>
</table>

### Services

<table>
  <thead>
    <tr>
      <th>Identifier (Class)</th>
      <th>Description</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>
        <code>box.console</code>
        <br/>
        (<code>%box.console.class%</code>)
      </td>
      <td>The console application which contains all commands.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.helper.container</code>
        <br/>
        (<code>%box.console.helper.container.class%</code>)
      </td>
      <td>A helper that provides access to the container.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.command.*</code>
        <br/>
        (<code>%box.console.command.*.class%</code>)
      </td>
      <td>A command.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.helper.*</code>
        <br/>
        (<code>%box.console.helper.*.class%</code>)
      </td>
      <td>A helper.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.event_dispatcher</code>
        <br/>
        (<code>%box.console.event_dispatcher.class%</code>)
      </td>
      <td>The event dispatcher.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.helper_set</code>
        <br/>
        (<code>%box.console.helper_set.class%</code>)
      </td>
      <td>The helper set which contains all helpers.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.input</code>
        <br/>
        (<code>%box.console.input.class%</code>)
      </td>
      <td>The input manager.</td>
    </tr>
    <tr>
      <td>
        <code>box.console.output</code>
        <br/>
        (<code>%box.console.output.class%</code>)
      </td>
      <td>The output manager.</td>
    </tr>
  </tbody>
</table>

Performance
-----------

The processing of building a container can potentially be time consuming and
costly in terms of performance. **Console** provides a way to cache the results
of the container building process so that subsequent uses of the application can
be faster.

```php
use Box\Component\Console\ApplicationCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;

ApplicationCache::bootstrap(
    '/path/to/cache/example.php',
    function (ContainerBuilder $container) {
        // first-run container building
    },
    'MyCachedContainer', // name of cached container class
    true                 // toggle debugging
);
```

The `ApplicationCache::bootstrap()` method manages the process of creating,
loading, and saving the container. When the application is first run using this
method, the following files are created. It is important to note that the name
of the generated files will vary depending on what you provided as the first
argument to `bootstrap()`.

| File               | Description                                                               |
|:-------------------|:--------------------------------------------------------------------------|
| `example.php`      | The cached container.                                                     |
| `example.php.meta` | The cache metadata, used to determine if the cache needs to be refreshed. |
| `example.xml`      | The container configuration used for debugging.                           |

By default, the name of the cached container class is `ConsoleContainer` and
resides in the root namespace. Also by default, "debugging" is enabled. The
debugging option will cause the cache to be refreshed if a resource is updated.
By disabling debugging, the cache files must be manually deleted before any of
the changes to the resources take effect.

[Build Status]: https://travis-ci.org/box-project/console.png?branch=master
[Latest Stable Version]: https://poser.pugx.org/box-project/console/v/stable.png
[Latest Unstable Version]: https://poser.pugx.org/box-project/console/v/unstable.png
[Total Downloads]: https://poser.pugx.org/box-project/console/downloads.png

[dependency injection]: http://en.wikipedia.org/wiki/Dependency_injection
[mediator]: http://en.wikipedia.org/wiki/Mediator_pattern
[Compiling]: http://symfony.com/doc/current/components/dependency_injection/compilation.html
[DI loaders]: http://symfony.com/doc/current/components/dependency_injection/introduction.html#setting-up-the-container-with-configuration-files

[Console]: http://symfony.com/doc/current/components/console/index.html
[DependencyInjection]: http://symfony.com/doc/current/components/dependency_injection/index.html
[EventDispatcher]: http://symfony.com/doc/current/components/event_dispatcher/index.html
[Symfony]: https://symfony.com/
