<?php

namespace App\Normalizer;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;

//contrat sur l'implémentation de la méthode
interface NormalizerInterface
{
    public function normalize(ExceptionEvent $exception);

    public function supports(ExceptionEvent $exception);
}