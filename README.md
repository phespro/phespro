<img src="./logo.svg" alt="logo" width="250" />
 
Another PHP framework? Yes and here is why:

- Caching causes complexity -> No built-in caches.
- Minimalistic -> Most frameworks do too much. You have the freedom to choose you own tooling.
- Stable -> Because of its minimalism, updating to new versions should be easy
- Simple -> Easy to set up, easy to understand
- Modern -> Built with modern PHP (>=8.2)
- Extensible -> Very simple to extend

## Setup

You will need composer for setting up Phespro (Composer installation under: https://getcomposer.org).
If you have composer installed, you can run the following command:

```
composer create-project phespro/project your_project_name
```

Alternatively, you can include phespro into your project and do the setup manually:

```
composer require phespro/phespro
```

## Kernel, Container and Extension

Phespro is built around a dependency injection container. Nearly everything that happens in Phespro is done through the
container. Because of that, the class `\Phespro\Phespro\Kernel` is a subclass of `\Phespro\Container\Container`.

Phespro is built around extensions. When you write your application, you need to build at minimum one extension.

Extensions are registered when creating the kernel.

```
$kernel = new Kernel([
    MyExtension::class,
]);
```

Please note, that your extensions are executed in the order, that they are contained in the extension array passed on kernel creation.

Your extension class needs to implement the interface `Phespro\Phespro\Extensibility\ExtensionInterface`.

```
use Phespro\Phespro\Kernel;
use League\Route\Router;

class MyExtension implements Phespro\Phespro\Extensibility\ExtensionInterface
{
    static function preBoot(Kernel $kernel): void
    {
        // primarily used for registering your extension service in the dependency injection container
        $kernel->add(static::class, fn() => new static, ['extension']);
    }

    function boot(Kernel $kernel): void
    {
        // here you can register all your services. This includes actions (for web request handling).
        $kernel->add(IndexGet::class, fn() => new IndexGet);
    }

    function bootHttp(Router $router): void
    {
        // register your routes and middlewares
        $router->get('/', IndexGet::class));
    }
}
```

You may extend the class `Phespro\Phespro\Extensibility\AbstractExtension` for simplicity reasons. But you don't have to.

When using the <a href="https://packagist.org/packages/phespro/project">project template</a> you already have the first
extension registered. When including phespro without the project template, you might consider taking a look into the
<a href="https://github.com/phespro/project">project template code</a> for understanding, how to set up Phespro correctly.


## Migrations

Phespro comes with support for migrations out-of-the-box. Migrations can be used to migrate anything.
And because Phespro does not care for what type of database you use, you can adapt the migrations to
any database or storage.

Before using the migration system, you need to provide a implementation for the interface `Phespro\Phespro\Migration\MigrationStateStorageInterface`.

Phespro ships with an implementation for SQLite. You can use this implementation by registering the needed service:

```
// assumes, that the service 'db' is registered and provides a pdo connection to an sqlite db
$kernel->add(MigrationStateStorageInterface::class, fn(Container $c) => new SQLiteMigrationStateStorage(
    $c->get('db'))
);
```

You can simply generate a migration by executing the following command:

```
bin/console migration:create --directory the/directory/path/of/your/migrations --namespace App
```

Now you can register the migration by adding it in the `boot`-method of your extension.

```
$kernel->add(Migration1000::class, fn() => new Migration1000, ['migration']);
```

Now you can execute the migrations by running `bin/console migration:apply-all` .

## Routing / Middlewares

Phespro uses the router implementation of the phpleague. You can find the documentation here:

https://route.thephpleague.com/

You can simply add routes and middlewares in the `bootHttp`-method of your extension.

## ORM

We don't think, that every application should be shipped with an ORM. If you want to use an ORM, just pick the ORM you
would like to use.

## Template Engine

Phespro comes with the preinstalled library <a href="https://packagist.org/packages/mschop/notee">NoTee</a> for
generating HTML. You can either use this library (recommend) or install any templating engine that you would like to use.

<a href="https://notee.readthedocs.io/en/latest/">Link to NoTee Documentation</a>

## Validation

Phespro does not ship any validation engine. Pick the validation engine, that fits you needs best:

https://packagist.org/?query=validation

## Logging

When using Phespro you need to explicitly activate Logging. We want to give you the freedom of choice regarding Logging.
By default, the `NullLogger` is used, which is like sending the logs to `/dev/null`. This means, that nothing gets logged
unless you explicitly activate logging.

You can activate logging by replacing the service `\Psr\LoggerInterface` with the `decorate` function. Your logger must
implement the interface `Psr\LoggerInterface`.

The following example uses Monolog:

```php
$container->decorate(\Psr\LoggerInterface::class, function() {
    $logger = new Logger('MyLogger');
    $logger->pushHandler(new StreamHandler('/path/to/your/logfile.log'));
    return $logger;
});
```