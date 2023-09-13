<?php

namespace Phespro\Phespro\Security\Csrf;

use NoTee\CreateTagEvent;
use NoTee\Nodes\TagNode;
use NoTee\SubscriberInterface;

readonly class NoTeeSubscriber implements SubscriberInterface
{
    public function __construct(
        protected TokenProviderInterface $csrfTokenProvider,

    )
    {
    }

    public function notify(CreateTagEvent $event): TagNode
    {
        $node = $event->getNode();
        if ($node->getTagName() !== 'form') {
            return $node;
        }

        $method = strtolower($node->getAttributes()['method'] ?? 'get');

        if ($method === 'get') {
            return $node;
        }

        return $node->appendChild(
            $event->getNodeFactory()->tag('input', [
                ['type' => 'hidden', 'value' => $this->csrfTokenProvider->get(), 'name' => 'csrf_token']
            ])
        );
    }
}