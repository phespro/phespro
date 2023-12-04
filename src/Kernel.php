<?php


namespace Phespro\Phespro;


use Exception;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Http\Exception as LeagueHttpException;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Phespro\Container\Container;
use Phespro\Container\ServiceAlreadyDefinedException;
use Phespro\Phespro\Configuration\FrameworkConfiguration;
use Phespro\Phespro\Extensibility\ExtensionInterface;
use Phespro\Phespro\Http\Middlewares\CsrfMiddleware;
use Phespro\Phespro\Http\Server\StartServerCommand;
use Phespro\Phespro\Http\WebRequestErrorHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Kernel extends Container
{
    /**
     * @template T of ExtensionInterface
     *
     * @param iterable<class-string<T>> $extensions
     * @throws ServiceAlreadyDefinedException
     */
    function __construct(iterable $extensions = [])
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
                $router->middleware($this->getObject(CsrfMiddleware::class));
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
            $handler = $this->getObject(WebRequestErrorHandlerInterface::class);
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
     * @template T of ExtensionInterface
     *
     * @param iterable<class-string<T>> $extensions
     * @throws ServiceAlreadyDefinedException
     * @throws Exception
     */
    protected function preBoot(iterable $extensions): void
    {
        foreach($extensions as $extension) {
            echo($extension);
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

        $this->add(LoggerInterface::class, fn() => new NullLogger);

        Security\_Services::register($this);
        Migration\_Services::register($this);
        NoTee\_Services::register($this);
        Http\_Services::register($this);
        Configuration\_Services::register($this);
        Assets\_Services::register($this);
    }
}
