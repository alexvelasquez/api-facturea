<?php

namespace App\Extensions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Este trait incluye los metodos para lanzar las excepciones HTTP mas comunes
 */
trait HttpExceptionsTrait
{
    /**
     * Lanza una excepcion con codigo HTTP 400 (Bad Request)
     *
     * @param string $message
     *
     * @throws BadRequestHttpException
     */
    protected function createBadRequestException($message)
    {
        throw new BadRequestHttpException($message);
    }
}
