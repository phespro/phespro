<?php


namespace Phespro\Phespro;


use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Router;
use Phespro\Container\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Laminas\Diactoros\ServerRequestFactory;


class Kernel
{
    private Container $container;

    public function __construct(array $config)
    {
        $this->container = new Container;

        $this->container->add('config', fn() => $config);

        $this->container->addFactory('router', fn() => new Router);

        $this->container->addFactory('cli_application', function(Container $c) {
            $app = new Application('Phespro CLI');
            foreach ($c->getByTag('cli_command') as $command) {
                $app->add($command);
            }
            return $app;
        });
    }

    public function handleWebRequest(bool $emit = true, ServerRequestInterface $request = null): ResponseInterface
    {
        $router = $this->container->get('router');
        assert($router instanceof Router);
        foreach($this->container->getByTag('plugin') as $plugin) {
            if (!$plugin instanceof PluginInterface) {
                throw new \Exception("Invalid plugin. Plugins must implement interface " . PluginInterface::class);
            }
            $plugin->initializeWeb($router);
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
        return $response;
    }

    /**
     * This method should be called from cli php script
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     * @throws \Exception
     */
    public function handleCli(InputInterface $input = null, OutputInterface $output = null): void
    {
        $app = $this->container->get('cli_application');
        assert($app instanceof Application);
        $app->run($input, $output);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }
}