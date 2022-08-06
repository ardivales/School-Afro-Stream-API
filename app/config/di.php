<?php

use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Mvc\Model\Manager as ModelsManager;
use Phalcon\Session\Adapter\Files as Session;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Mvc\Micro;
use Dmkit\Phalcon\Auth\Middleware\Micro as AuthMicro;

// Initializing a DI Container
$di = new \Phalcon\DI\FactoryDefault();

/**
 * Overriding Response-object to set the Content-type header globally
 */
$di->setShared(
    'response', function () {
    $response = new \Phalcon\Http\Response();
    $response->setContentType('application/json', 'utf-8');

    return $response;
}
);

/**
 * Start Session
 */
$di->setShared(
    'session', function () {
    $session = new Session();

    $session->start();

    return $session;
}
);

/** Common config */
$di->setShared('config', $config);
$di->setShared(
    "modelsManager", function () {
    return new ModelsManager();
}
);
/** Database */
$di->set(
    "db", function () use ($config) {
    return new PdoMysql(
        [
            "host" => $config->database->host,
            "username" => $config->database->username,
            "password" => $config->database->password,
            "dbname" => $config->database->dbname,
            "charset" => $config->database->charset,
        ]
    );
}
);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();
    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);
    return $url;
});

$di->set('schema', function () {
    $config = $this->getConfig();
    $schema = $config->database->schema;
    return $schema;
});

/** Service to perform operations with the Students */
//$di->setShared('students_service', '\App\Services\StudentsService');
return $di;
