<?php

namespace App\Controllers;

use App\Controllers\HttpExceptions\Http400Exception;
use App\Controllers\HttpExceptions\Http422Exception;
use App\Controllers\HttpExceptions\Http500Exception;
use App\Services\AbstractService;
use App\Services\StudentsService;

/**
 * Class AbstractController
 *
 * @property \Phalcon\Http\Request $request
 * @property \Phalcon\Http\Response $htmlResponse
 * @property \Phalcon\Db\Adapter\Pdo\Postgresql $db
 * @property \Phalcon\Config $config
 * @property \App\Services\STUDENTsService $STUDENTsService
 * @property \App\Models\STUDENTs $STUDENT
 */
abstract class AbstractController extends \Phalcon\DI\Injectable
{

    /**
     * Route not found. HTTP 404 Error
     */
    const ERROR_NOT_FOUND = 1;

    /**
     * Invalid Request. HTTP 400 Error.
     */
    const ERROR_INVALID_REQUEST = 2;

    /**
     * Validate data.
     *
     * @param String $validation The validation.
     * @param array $data The data.
     * @throws Http400Exception
     */
    public static function validate($validation, $data)
    {
        $errors = [];
        $messages = $validation->validate($data);
        if (count($messages)) {
            foreach ($messages as $message) {
                $errors[$message->getField()] = $message->getMessage();
            }
        }
        if ($errors) {
            $exception = new Http400Exception(_('Input parameters validation error'), self::ERROR_INVALID_REQUEST);
            throw $exception->addErrorDetails($errors);
        }
    }

    /**
     * Handle exceptions.
     *
     * @param ServiceException $e The exception.
     * @throws Http422Exception
     * @throws Http500Exception
     */
    public static function handle_exceptions($e)
    {
        switch ($e->getCode()) {
            case AbstractService::ERROR_ALREADY_EXISTS:

                /* STUDENT */
            case StudentsService::ERROR_UNABLE_CREATE_STUDENT:
            case StudentsService::ERROR_EMAIL_ALREADY_USED:
            case StudentsService::ERROR_STUDENT_NOT_FOUND:
            case StudentsService::ERROR_UNABLE_UPDATE_STUDENT:


                throw new Http422Exception($e->getMessage(), $e->getCode(), $e);
            default:
                throw new Http500Exception($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get the data.
     *
     * @param json $std_data The std data.
     * @param json $json_data The json data.
     * @return array $data The data.
     */
    public static function get_data($std_data, $json_data = NULL, $default_value_array = [])
    {
        if (!is_null($json_data)) {
            $std_data = json_decode($json_data);
        }
        $data = json_decode(json_encode($std_data), true);
        if (isset($data) && !empty($data)) {
            foreach ($data as $key => $value) {
                if (empty($value) && 0 != $value) {
                    $data[$key] = NULL;
                }
            }
        } else {
            $data = array();
        }

        foreach ($default_value_array as $value) {
            if (!isset($data[$value])) {
                $data[$value] = NULL;
            }
        }
        return $data;
    }

}
