<?php


namespace Phespro\Phespro;


use Exception;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use NoTee\NoTee;
use NoTee\NoTeeInterface;
use Phespro\Container\Container;
use Phespro\Container\ServiceAlreadyDefinedException;
use Phespro\Phespro\Assets\AssetLocatorInterface;
use Phespro\Phespro\Assets\NoopAssetLocator;
use Phespro\Phespro\Configuration\FrameworkConfiguration;
use Phespro\Phespro\Extensibility\ExtensionInterface;
use Phespro\Phespro\Http\WebRequestErrorHandler;
use Phespro\Phespro\Http\WebRequestErrorHandlerInterface;
use Phespro\Phespro\Migration\CliMigrator;
use Phespro\Phespro\Migration\CliMigratorInterface;
use Phespro\Phespro\Migration\Commands\ApplyAllCommand;
use Phespro\Phespro\Migration\Commands\CreateCommand;
use Phespro\Phespro\Migration\MigrationStateStorageInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Phespro\Phespro\Migration\MigrationStateStorage\MemoryMigrationStateStorage;

class Kernel extends Container
{
    function __construct(array $extensions = [])
    {
        $this->registerFrameworkServices();
        $this->preBoot($extensions);
        $this->boot();
    }

    function handleWebRequest(bool $emit = true, ServerRequestInterface $request = null): ResponseInterface
    {
        try {
            $router = $this->get('router');
            assert($router instanceof Router);
            foreach($this->getByTag('extension') as $extension) {
                assert($extension instanceof ExtensionInterface);
                $extension->bootHttp($router);
            }
            if ($request === null) {
                $request = ServerRequestFactory::fromGlobals(
                    $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
                );
            }
            $response = $router->dispatch($request);
            if ($emit) {
                (new SapiEmitter)->emit($response);
            }
        } catch (\Throwable $err) {
            $handler = $this->get(WebRequestErrorHandlerInterface::class);
            assert($handler instanceof WebRequestErrorHandlerInterface);
            $response = $handler->handle($err);
            if ($emit) {
                (new SapiEmitter)->emit($response);
            }

        }
        return $response;
    }

    /**
     * This method should be called from cli php script
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @throws Exception
     */
    function handleCli(InputInterface $input = null, OutputInterface $output = null): void
    {
        $app = $this->get('cli_application');
        assert($app instanceof Application);
        $app->run($input, $output);
    }

    /**
     * @param string[] $extensions
     * @throws ServiceAlreadyDefinedException
     * @throws Exception
     */
    protected function preBoot(array $extensions): void
    {
        foreach($extensions as $extension) {
            assert(is_subclass_of($extension, ExtensionInterface::class));
            $extension::preBoot($this);
        }
    }

    protected function boot(): void
    {
        foreach ($this->getByTag('extension') as $extension) {
            assert($extension instanceof ExtensionInterface);
            $extension->boot($this);
        }
    }

    protected function registerFrameworkServices(): void
    {
        $this->add('config', fn() => new FrameworkConfiguration(
            displayErrorDetails: getenv('PHESPRO_DISPLAY_ERROR_DETAILS') ?: false,
            debugNoTee: getenv('PHESPRO_DEBUG_NOTEE') ?: false,
        ));

        $this->add('router', function(Container $c) {
            $strategy = (new ApplicationStrategy())->setContainer($c);
            assert($strategy instanceof ApplicationStrategy);
            return (new Router)->setStrategy($strategy);
        });

        $this->add('cli_application', function(Container $c) {
            $app = new Application('Phespro CLI');
            foreach ($c->getByTag('cli_command') as $command) {
                $app->add($command);
            }
            return $app;
        });

        $this->add(MigrationStateStorageInterface::class, fn() => new MemoryMigrationStateStorage);

        $this->add(CliMigratorInterface::class, fn(Container $c) => new CliMigrator(
            $c->get(MigrationStateStorageInterface::class),
            $c->getByTag('migration'),
            $c->get(LoggerInterface::class),
        ));

        $this->add(
            ApplyAllCommand::class,
            fn(Container $c) => new ApplyAllCommand($c->get(CliMigratorInterface::class)),
            ['cli_command']
        );

        $this->add(
            CreateCommand::class,
            fn() => new CreateCommand,
            ['cli_command'],
        );

        $this->add(
            'template_dirs',
            fn() => [],
        );

        $this->add(
            'template_context',
            fn(Container $c) => [
                'asset' => fn(string $path) => $c->get(AssetLocatorInterface::class)->get($path),
            ],
        );

        $this->add(
            NoTeeInterface::class,
            function(ContainerInterface $c) {
                $noTee = NoTee::create(
                    templateDirs: $c->get('template_dirs'),
                    defaultContext: $c->get('template_context'),
                );
                return $noTee;
            }
        );

        $this->add(
            WebRequestErrorHandlerInterface::class,
            fn(ContainerInterface $c) => new WebRequestErrorHandler(
                $c->get(LoggerInterface::class),
                $c->get('config'),
            )
        );

        $this->add(LoggerInterface::class, fn() => new NullLogger);

        $this->add(AssetLocatorInterface::class, fn() => new NoopAssetLocator);

        $this->decorateAll(function(Container $c, mixed $inner) {
            if (is_object($inner) && method_exists($inner, 'injectNoTee')) {
                $inner->injectNoTee($c->get(NoTeeInterface::class));
            }
            return $inner;
        });
    }
}
