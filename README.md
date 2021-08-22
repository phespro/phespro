# phespro

Another PHP framework? Yes and here is why:

- Caches are bad -> No built in caches
- Minimalistic -> Most frameworks do too much
- Stable -> Because of its minimalism, updating to new versions should be easy
- Simple -> Easy to set up, easy to understand
- Modern -> Built with modern PHP (>=8.0)
- Extensible -> Very simple to extend

## Setup

You will need composer for setting up Phespro (Composer installation under: https://getcomposer.org).
If you have composer installed, you can run the following command:

```
composer create-project phespro/project:dev-master your_project_name
```

Alternatively, you can include phespro into your project and do the setup manually:

```
composer require phespro/phespro:dev-master
```

## The Kernel / The Container

Phespro is built around a dependency injection container. Everything, that happens in Phespro is done through the
container. Therefore the kernel is the dependency injection container.

## Using Extensions

Phespro is built around extensions. When you write your application, you need to build at minimum one extension.

Extensions are registered when creating the kernel.

```
$kernel = new Kernel([
    MyExtension::class,
]);
```

Please note, that your extensions are executed in the order, that they are contained in the passed extension array.

Your extension class needs to implement the interface `Phespro\Phespro\Extensibility\ExtensionInterface`.

```
use Phespro\Phespro\Kernel;
use League\Route\Router;

class MyExtension implements Phespro\Phespro\Extensibility\ExtensionInterface
{
    static function preBoot(Kernel $kernel)
    {
        // primarily used for registering your extension service in the dependency injection container
        $kernel->add(static::class, fn() => new static, ['extension']);
    }

    function boot(Kernel $kernel)
    {
        // here you can register all your services. This includes actions (for web request handling).
        $kernel->add(IndexGet::class, fn() => new IndexGet);
    }

    function bootHttp(Router $router)
    {
        // register your routes and middlewares
        $router->get('/', IndexGet::class));
    }
}
```

You may extend the class `Phespro\Phespro\Extensibility\AbstractExtension` for simplicity reasons. But you don't have to.

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

## Routing / Middlewares

Phespro uses the router implementation of the phpleague. You can find the documentation here:

https://route.thephpleague.com/

You can simply add routes and middlewares in the `bootHttp`-method of your extension.
