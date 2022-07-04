<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ApiCsrfValidationSubscriber implements EventSubscriberInterface
{
    public function onKernelRequest(RequestEvent $event): void
    {

        if (!$event->isMainRequest()){
            return;
        }

        $request = $event->getRequest();

        if ($request->isMethodSafe(false)){
            return;
        }

        if ($request->headers->get('Content-Type') != 'application/json'){
            $response = new JsonResponse([
                'message' => 'Invalid Content-Type'
            ], 415);
            $event->setResponse($response);
            return;
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
