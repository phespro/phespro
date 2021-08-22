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

TODO add docs