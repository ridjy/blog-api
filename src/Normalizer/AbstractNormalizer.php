<?php

namespace App\Normalizer;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

//Cette classe abstraite permet de faire en sorte que nous n'ayons pas à implémenter à nouveau la méthode supports.
abstract class AbstractNormalizer implements NormalizerInterface
{
    protected $exceptionTypes;

    public function __construct(array $exceptionTypes)
    {
        $this->exceptionTypes = $exceptionTypes;
    }

    //voir si l'exception est supporté
    public function supports(ExceptionEvent $exception) : bool
    {
        return in_array(get_class($exception), $this->exceptionTypes);
    }
}