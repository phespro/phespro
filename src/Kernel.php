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
use Phespro\Phespro\Http\Middlewares\CsrfMiddleware;
use Phespro\Phespro\Http\WebRequestErrorHandler;
use Phespro\Phespro\Http\WebRequestErrorHandlerInterface;
use Phespro\Phespro\Migration\CliMigrator;
use Phespro\Phespro\Migration\CliMigratorInterface;
use Phespro\Phespro\Migration\Commands\ApplyAllCommand;
use Phespro\Phespro\Migration\Commands\CreateCommand;
use Phespro\Phespro\Migration\MigrationStateStorageInterface;
use Phespro\Phespro\Security\Csrf\TokenGenerator;
use Phespro\Phespro\Security\Csrf\TokenGeneratorInterface;
use Phespro\Phespro\Security\Csrf\TokenProvider;
use Phespro\Phespro\Security\Csrf\TokenProviderInterface;
use Phespro\Phespro\Security\Csrf\TokenStorageInterface;
use Phespro\Phespro\Security\Csrf\TokenValidator;
use Phespro\Phespro\Security\Csrf\TokenValidatorInterface;
use Phespro\Phespro\Security\Csrf\NoTeeSubscriber;
use Phespro\Phespro\Security\Csrf\PhpSessionTokenStorage;
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
use League\Route\Http\Exception as LeagueHttpException;

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
            foreach ($this->getByTag('extension') as $extension) {
                assert($extension instanceof ExtensionInterface);
                $extension->bootHttp($this, $router);
            }

            $config = $this->get('config');
            assert($config instanceof FrameworkConfiguration);

            if ($config->autoCsrfProtect) {
                $router->middleware($this->get(CsrfMiddleware::class));
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
            if ($err instanceof LeagueHttpException) {
                $response = $response->withStatus($err->getStatusCode());
            }
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
            autoCsrfProtect: getenv('PHESPRO_AUTO_CSRF_PROTECT') ?: true,
        ));

        $this->add('router', function(Container $c) {
            $strategy = (new ApplicationStrategy())->setContainer($c);
            assert($strategy instanceof ApplicationStrategy);
            $router = new Router;
            $router->setStrategy($strategy);
            return $router;
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

        $this->add(TokenValidatorInterface::class, fn() => new TokenValidator);

        $this->add(TokenStorageInterface::class, fn() => new PhpSessionTokenStorage);

        $this->add(TokenGeneratorInterface::class, fn() => new TokenGenerator);

        $this->add(TokenProviderInterface::class, fn(ContainerInterface $c) => new TokenProvider(
            $c->get(TokenGeneratorInterface::class),
            $c->get(TokenStorageInterface::class),
        ));

        $this->add(NoTeeSubscriber::class, fn(ContainerInterface $c) => new NoTeeSubscriber(
            $c->get(TokenProviderInterface::class),
        ));

        $this->add(CsrfMiddleware::class, fn(ContainerInterface $c) => new CsrfMiddleware(
            $c->get(TokenProviderInterface::class),
            $c->get(TokenValidatorInterface::class),
        ));

        $this->add(
            NoTeeInterface::class,
            function(ContainerInterface $c) {
                $config = $c->get('config');
                assert($config instanceof FrameworkConfiguration);

                $noTee = NoTee::create(
                    templateDirs: $c->get('template_dirs'),
                    defaultContext: $c->get('template_context'),
                    debug: $config->debugNoTee,
                );

                if ($config->autoCsrfProtect) {
                    $noTee->getNodeFactory()->subscribe($c->get(NoTeeSubscriber::class));
                }

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
