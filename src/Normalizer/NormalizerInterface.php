<?php

namespace App\Normalizer;

//contrat sur l'implémentation de la méthode
interface NormalizerInterface
{
    public function normalize(\Exception $exception);

    public function supports(\Exception $exception);
}