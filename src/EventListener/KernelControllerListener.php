<?php

namespace App\EventListener;

use App\Controller\BaseController;
use App\Mif;
use App\Request;
use App\Util\Authentication\AuthenticationProcessorFactory;
use App\Util\Authorization\AuthConstant;
use App\Util\Authorization\AuthorizationProcessorFactory;
use App\Validation\RequestValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * Class KernelControllerListener
 * @package App\EventListener
 */
class KernelControllerListener
{
    /**
     * @param ControllerEvent $event
     */
    public function onKernelController(ControllerEvent $event)
    {
        $objController = $event->getController();
        if (!is_array($objController)) {

            return;
        }

        $action = $objController[1];
        $controller = $objController[0];
        if (!($controller instanceof BaseController)) {

            return;
        }

        $this->checkSocAuthHash($event);

        if (!$this->runAuthentication($event, $controller, $action)) {

            return;
        }

        if (!$this->runAuthorization($event, $controller, $action)) {

            return;
        }

        if (!$this->runValidation($event, $controller, $action)) {

            return;
        }
    }

    /**
     * @param ControllerEvent $event
     * @param BaseController $controller
     * @param string $action
     * @return bool
     */
    private function runAuthentication(ControllerEvent $event, BaseController $controller, string $action): bool
    {
        $authProcessor = AuthenticationProcessorFactory::createByController($controller);
        if (!$authProcessor) {
            return true;
        }

        $authProcessor->runAuthentication($event->getRequest(), $controller, $action);
        if ($authProcessor->hasErrors()) {
            $this->injectErrorResponse($event, $authProcessor->getErrorResponse());

            return false;
        }

        return true;
    }

    /**
     * @param ControllerEvent $event
     * @param BaseController $controller
     * @param string $action
     * @return bool
     */
    private function runAuthorization(ControllerEvent $event, BaseController $controller, string $action): bool
    {
        $authProcessor = AuthorizationProcessorFactory::createByController($controller);
        if (!$authProcessor) {
            return true;
        }

        $authProcessor->runAuthorization($event->getRequest(), $controller, $action);
        if ($authProcessor->hasErrors()) {
            $this->injectErrorResponse($event, $authProcessor->getErrorResponse());

            return false;
        }

        return true;
    }

    /**
     * @param ControllerEvent $event
     * @param BaseController $controller
     * @param string $action
     * @return bool
     */
    private function runValidation(ControllerEvent $event, BaseController $controller, string $action): bool
    {
        if ($controller->ignoreValidationForm($action)) {

            return true;
        }

        $validator = new RequestValidator();
        /** @var Request $request */
        $request = $event->getRequest();
        $validator->validateRequest($request);
        if ($validator->hasErrors()) {
            $this->injectErrorResponse($event, $validator->getErrorResponse());

            return false;
        }

        return true;
    }

    /**
     * @param ControllerEvent $event
     * @param JsonResponse $response
     */
    private function injectErrorResponse(ControllerEvent $event, JsonResponse $response)
    {
        $event->setController(
            function() use ($response) {

                return $response;
            }
        );
    }

    /**
     * @param ControllerEvent $event
     */
    private function checkSocAuthHash($event)
    {
        $request = $event->getRequest();
        $socAuthHash = $request->get(AuthConstant::USER_SOC_AUTH_HASH);

        if (!is_null($socAuthHash)) {
            $email = $request->get(AuthConstant::USER_AUTH_FIELD_EMAIL);
            if (is_null($email)) {
                $logger = Mif::getServiceProvider()->Logger;
                $logger->error('email required');
            } else {
                Mif::getServiceProvider()->AuthenticationOAuth2Service->linkingAccountToSocNetwork($email, $socAuthHash);
            }
        }
    }
}
