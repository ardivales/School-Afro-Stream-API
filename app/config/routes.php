<?php
// not found URLs
$app->notFound(
    function () use ($app) {
        $exception = new \App\Controllers\HttpExceptions\Http404Exception(
            _('URI not found or error in request.'), \App\Controllers\AbstractController::ERROR_NOT_FOUND, new \Exception('URI not found: ' . $app->request->getMethod() . ' ' . $app->request->getURI())
        );
        throw $exception;
    }
);
