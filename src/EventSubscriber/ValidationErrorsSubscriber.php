<?php

namespace App\EventSubscriber;

use App\Extensions\HttpExceptionsTrait;
use FOS\RestBundle\Exception\InvalidParameterException;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Psr\Log\LoggerInterface;

/**
 * Este Subscriber intercepta los Controller de la API REST que usan ParamFetcher para obtener los parametros
 *
 * La idea es devolver TODOS los errores de validacion en una respuesta HTTP con codigo de error 400 Bad Request;
 * el comportamiento por defecto de ParamFetcher es solamente devolver el primer parametro que falla alguna validacion
 *
 */
class ValidationErrorsSubscriber implements EventSubscriberInterface
{
    use HttpExceptionsTrait;

    private $paramFetcher;
    private $log;

    public function __construct(ParamFetcherInterface $paramFetcher,LoggerInterface $logger)
    {
        $this->paramFetcher = $paramFetcher;
        $this->log = $logger;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $errors = [];

        foreach ($this->paramFetcher->getParams() as $param) {
            try {
                $this->paramFetcher->get($param->name, $param->strict);

            } catch (InvalidParameterException $e) {
                $errors[$param->name] = $this->getValidationMessages($e->getViolations());
            }
        }
        if (count($errors) > 0) {
            $this->createBadRequestException($this->serializeValidationErrors($errors));
        }
    }

    /**
     * Serializa el arreglo asociativo de errores de validacion en un JSON con todos los errores por cada campo,
     * para poder enviarlo al cliente
     *
     * Por ejemplo, si el arreglo es:
     * [
     *   "nombre" => "No puede ser vacio",
     *   "DNI" => "Debe ser numerico",
     * ]
     *
     * Devuelve un string asi:
     *
     * {
     *   "nombre": "No puede ser vacio",
     *   "DNI": "Tiene que ser numerico"
     * }
     *
     * @param array $errors
     *
     * @return string
     */
    protected function serializeValidationErrors(array $errors)
    {
        var_dump($errors);
        return json_encode($errors);

    }

    /**
     * Devuelve un string con todos los mensajes de error de validacion de un campo
     *
     * @param ConstraintViolationListInterface $violations
     *
     * @return string
     */
    protected function getValidationMessages(ConstraintViolationListInterface $violations)
    {
        $message = '';

        foreach ($violations as $key => $violation) {
            if ($key > 0) {
                $message .= "\n";
            }

            $message .= $violation->getMessage();
        }

        return $message;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
