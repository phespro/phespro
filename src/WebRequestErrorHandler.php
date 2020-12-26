<?php


namespace Phespro\Phespro;


use Laminas\Diactoros\Response;
use NoTee\NodeFactory;
use NoTee\NoTee;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class WebRequestErrorHandler implements WebRequestErrorHandlerInterface
{
    public function __construct(
        protected LoggerInterface $logger,
        protected bool $displayErrorDetails,
    ) { }

    function handle(\Throwable $err): ResponseInterface
    {
        try {
            $this->logger->error('Unkown error occured', [
                'message' => $err->getMessage(),
                'file' => $err->getFile(),
                'line' => $err->getLine(),
                'trace' => $err->getTrace(),
            ]);
        } catch (\Throwable) {
            trigger_error(
                "Tried logging error message, but an error occured on trying to log the error. Please check write perms for logs.",
                E_USER_ERROR,
            );
        }

        $nf = NoTee::create()->getNodeFactory();
        $html = $nf->document(
            $nf->html(
                $nf->head(
                    $nf->meta(['charset' => 'utf-8']),
                    $nf->title('Unkown error occured'),
                    $nf->style(
                        $nf->raw("
                            body { padding: 0; margin: 0; }
                            .header { height: 4rem; line-height: 4rem; background-color: red; color: white; padding: 1rem;}
                            .content { padding: 1rem; }
                            td { vertical-align: top; }
                        "),
                    ),
                ),
                $nf->body(
                    $nf->div(['class' => 'header'], 'Unkown Error Occured'),
                    $nf->div(
                        ['class' => 'content'],
                        match($this->displayErrorDetails) {
                            false => $nf->div('Please contact the administrator'),
                            true => $nf->wrapper(
                                $nf->table(
                                    $nf->tr(
                                        $nf->td('Message'),
                                        $nf->td($err->getMessage()),
                                    ),
                                    $nf->tr(
                                        $nf->td('File'),
                                        $nf->td($err->getFile()),
                                    ),
                                    $nf->tr(
                                        $nf->td('Line'),
                                        $nf->td($err->getLine()),
                                    ),
                                    $nf->tr(
                                        $nf->td('Trace'),
                                        $nf->td(
                                            $nf->pre(
                                                $err->getTraceAsString()
                                            ),
                                        ),
                                    ),
                                ),
                            ),
                        },
                    ),
                ),
            ),
        );

        $response = new Response(status: 500);
        $response->getBody()->write((string)$html);

        return $response;
    }
}