<?php


namespace Phespro\Phespro;


use Laminas\Diactoros\Response;
use NoTee\NodeInterface;
use NoTee\NoTeeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class ErrorHandlerMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected bool $displayErrorDetails,
        protected NoTeeInterface $noTee,
    ) { }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch(\Throwable $error) {
            $this->logger->error('An unkown error occured on web request', [
                'trace' => $error->getTrace(),
                'message' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
            ]);

            $response = new Response();
            $response->getBody()->write((string)($this->displayErrorDetails ? $this->getDebug($error) : $this->getNonDebug()));
            $response = $response->withStatus(500, 'Unkown Server-Side error occured');
            return $response;
        }
    }

    protected function getNonDebug(): NodeInterface
    {
        $nf = $this->noTee->getNodeFactory();
        return $this->base(
            $nf->div('Please contact the administrator'),
        );
    }

    protected function getDebug(\Throwable $error): NodeInterface
    {
        $nf = $this->noTee->getNodeFactory();
        return $this->base(
            $nf->wrapper(
                $nf->table(
                    $nf->tr(
                        $nf->td('Message'),
                        $nf->td($error->getMessage()),
                    ),
                    $nf->tr(
                        $nf->td('File'),
                        $nf->td($error->getFile()),
                    ),
                    $nf->tr(
                        $nf->td('Line'),
                        $nf->td($error->getLine()),
                    ),
                    $nf->tr(
                        $nf->td('Trace'),
                        $nf->td(
                            $nf->pre(
                                $error->getTraceAsString()
                            ),
                        ),
                    )
                ),
            ),
        );
    }

    protected function base(NodeInterface $content): NodeInterface
    {
        $nf = $this->noTee->getNodeFactory();
        return $nf->document(
            $nf->html(
                $nf->head(
                    $nf->meta(['charset' => 'utf-8']),
                    $nf->title('Error occurred'),
                    $nf->style(
                        $nf->raw("
                            body { padding: 0; margin: 0; }
                            .header { height: 4rem; line-height: 4rem; background-color: red; color: white; padding: 1rem;}
                            .content { padding: 1rem; }
                            td { vertical-align: top; }
                        ")
                    )
                ),
                $nf->body(
                    $nf->div(['class' => 'header'], 'Unkown Error Occured'),
                    $nf->div(['class' => 'content'], $content),
                ),
            ),
        );
    }
}
