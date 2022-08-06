<?php

use App\Controllers\AbstractHttpException;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;
use Dmkit\Phalcon\Auth\Middleware\Micro as AuthMicro;
use App\Controllers\HttpExceptions\Http401Exception;

try {
    // Loading Configs
    $config = require(__DIR__ . '/../app/config/config.php');

    // Autoloading classes
    require __DIR__ . '/../app/config/loader.php';
    require __DIR__ . '/../app/config/CrosPlugin.php';
    // Initializing DI container
    /** @var \Phalcon\DI\FactoryDefault $di */
    $di = require __DIR__ . '/../app/config/di.php';
    $di->set('cors', function () {
        return new CORSPlugin();
    }, true);

    $em = new EventsManager();
    $em->attach('micro:beforeHandleRoute', $di->get('cors'));
    // Initializing application
    $app = new \Phalcon\Mvc\Micro();
    // Setting DI container
    $app->setDI($di);
    $app->setEventsManager($em);
    // Setting up routing
    require __DIR__ . '/../app/config/routes.php';


    // Making the correct answer after executing
    $app->after(
        function () use ($app) {
            // Getting the return value of method
            $return = $app->getReturnedValue();

            if (is_array($return)) {
                // Transforming arrays to JSON
                $app->response->setContent(json_encode($return));
            } elseif (!strlen($return)) {
                // Successful response without any content
                $app->response->setStatusCode('204', 'No Content');
            } else {
                // Unexpected response
                throw new Exception('Bad Response');
            }

            // Sending response to the client
            $app->response->send();
        }
    );
    $app->handle();
} catch (AbstractHttpException $e) {
    $response = $app->response;
    $response->setStatusCode($e->getCode(), $e->getMessage());
    $response->setJsonContent($e->getAppError());
    $response->send();
} catch (\Phalcon\Http\Request\Exception $e) {
    $app->response->setStatusCode(400, 'Bad request')
        ->setJsonContent([
            AbstractHttpException::KEY_CODE => 400,
            AbstractHttpException::KEY_MESSAGE => 'Bad request'
        ])
        ->send();
} catch (\Exception $e) {
    // Standard error format
    $result = [
        AbstractHttpException::KEY_CODE => 500,
        AbstractHttpException::KEY_MESSAGE => $e->getMessage()
    ];

    // Sending error response
    $app->response->setStatusCode(500, 'Internal Server Error')
        ->setJsonContent($result)
        ->send();
}
