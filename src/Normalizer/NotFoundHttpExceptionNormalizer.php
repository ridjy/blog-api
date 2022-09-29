<?php

namespace App\Normalizer;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class NotFoundHttpExceptionNormalizer extends AbstractNormalizer
{
    public function normalize(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $result['code'] = Response::HTTP_NOT_FOUND;

        $result['body'] = [
            'code' => Response::HTTP_NOT_FOUND,
            'message' => $exception->getMessage()
        ];

        return $result;
    }
}