<?php

namespace Adadgio\GearBundle\Component\Api\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

use Adadgio\GearBundle\Component\Api;
use Adadgio\GearBundle\Component\Reflection\ReflectionAnalysis;

/**
 * Listens to controller events an injects an \ApiRequest object in controllers
 * where the api annotation is found and a type hint "ApiRequest" is there.
 */
class ApiKernelEventListener
{
    /**
     * @var object \ApiCoreService
     */
    private $apiCore;

    /**
     * @var object \Doctrine\Common\Annotations\Reader
     */
    private $reader;

    /**
     * Dependecy injections, see bundle services.yml for details.
     *
     * @param object \Doctrine\Common\Annotations\Reader
     * @param object
     */
    public function __construct(\Doctrine\Common\Annotations\Reader $reader, Api\ApiCoreService $apiCore)
    {
        $this->reader = $reader;
        $this->apiCore = $apiCore;
    }

    /**
     * Fired during any controller call. Responsible for injecting
     * an \ApiRequest in the controller when annotation is found and
     * a type hint "ApiRequest" is found. Also throw exceptions on
     * invlid authentication
     *
     * @param object FilterControllerEvent\Event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        $controller = $event->getController();

        // return if this is not a controller
        if (!is_array($controller)) {
            return;
        }

        // prepare \Reflection objects to find their respective annotations
        // $reflectionClass  = new \ReflectionClass($controller[0]); // actual controller called
        $reflectionObject = new \ReflectionObject($controller[0]); // actual controller called
        $reflectionMethod = $reflectionObject->getMethod($controller[1]); // actual method called

        // will be a merge of class and methods annotations
        foreach($this->reader->getMethodAnnotations($reflectionMethod) as $annotation) {
            // do nothing if our anontation is not found
            if(!$annotation instanceof Api\Annotation\Api) {
                continue;
            }

            // let the \ApiCoreService create an \ApiRequest object internally and set internal
            // variable to do his own sauce later on (it needs annotation params of course)
            $this->apiCore->handleRequest($request, $annotation);

            // also dont listen to anything else than json requests
            if (!$this->apiCore->getApiRequest()->isJson()) {
                return;
            }

            // let the ApiCoreService do check ups and pass
            // authentication to other dedicated security services
            if (false === $this->apiCore->isSecured()) {
                $error = $this->apiCore->getError();
                throw new Api\ApiException($error['message'], $error['code']);
            }

            // a priori,
            // if (false === $this->apicore->isSecured()) {
            //     $error = $this->apicore->getError();
            //     throw new ApiException($error->message, $error->code);
            // }

            // make sure the controller has a ApiEngine\ApiHandler instanceof type hinted
            // argument somewhere in its param. This is found using the full controller
            // name (and namespace), method and PHP \Reflection class.
            // Example: indexAction(Request $request, ApiHandler $api){...}
            $analysis = new ReflectionAnalysis();
            $analysis->of($request->attributes->get('_controller'));
            
            $argumentName = $analysis->findTypeHintedArgName(Api\ApiRequest::class);
            // ReflectionAnalysis::ofController($request->attributes->get('_controller'));
            // $argumentName = ReflectionAnalysis::getArgumentTypeHintedWith('ApiHandler');

            if (!$argumentName) {
                // no type hinted argument was
                // declared in the controller method
                return;
            }

            // hum, whats that?
            if ($request->attributes->has($argumentName)) {
                var_dump('It has! But when does this happen?');
                // the request attribute already has that argument, we
                // cant override it. the developer messed things up!
                return;
            }

            // retrieve a \ApiRequest object from the \ApiCoreService and pass
            // it as argument to the controller where the argument was found
            $apiRequest = $this->apiCore->getApiRequest();
            $request->attributes->set($argumentName, $apiRequest);
            return;
        }
    }

    /**
     * Executed when a exception is raised on a controller.
     * @param object GetResponseForExceptionEvent\Event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();
        $contentType = $request->headers->get('content-type');

        // $consideredApiRequests = array(
        //     'application/json', 'application/json; charset=utf-8'
        // );

        // the exception must be of type \ApiException, only then we
        // send a json response otherwise this will apply to each exception
        // in every controller. We dont want that
        if ($exception instanceof Api\ApiException) {
            $event->setResponse(
                new JsonResponse(array('type' => 'error', 'message' => $exception->getMessage()), $exception->getCode())
            );
            return;
        }

        // // catch ApiException to return a Json response rather than the Symfony basic exception
        // if ($exception instanceof \Adadgio\RocketBundle\Component\ApiEngine\ApiException) {
        //
        //     $event->setResponse(ApiEngine\ApiResponse::fire(array(
        //         'message' => $exception->getMessage(),
        //     ), $exception->getCode()));
        //
        // } else if (in_array($contentType, $consideredApiRequests)) {
        //     // catch \ApiException errors to return a Json response rather than the Symfony basic exception
        //     // and/or check if header is made from an API or with content type application/json. Both return
        //     // nicer debuggable messages in JSON for all APIs.
        //
        //     // get error message code
        //     $code = empty($exception->getCode()) ? 500 : $exception->getCode();
        //
        //     // display more error message when in dev or staging
        //     if ($this->environment === 'prod') {
        //         $error = $exception->getMessage();
        //     } else {
        //         $error = $this->getVerboseErrorMessageFrom($exception);
        //     }
        //
        //     // display a nice error response
        //     $event->setResponse(ApiEngine\ApiResponse::fire(array('message' => $error), $code));
        // }
    }

    /**
     * Find more relevant verbose error message (line, etc.)
     *
     * @return string Verbose error message.
     */
    private function getVerboseErrorMessageFrom($exception)
    {
        $file = $exception->getFile();
        $parts = explode('/', $file);
        $fileInfo = end($parts);

        return sprintf('%s in file %s at line %s', $exception->getMessage(), $fileInfo, $exception->getLine());
    }
}
