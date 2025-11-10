<?php
use App\Helpers\AccessGuard;

if (!function_exists('access')) {
    function access()
    {
        return new AccessGuard();
    }
}

if (!function_exists('returnError')) {

    function returnError($errors = false, $message = 'Error', $code = 400)
    {
        return response([
            'success' => false,
            'message' => $message,
            'error' => $errors
        ], $code);
    }
}

if (!function_exists('returnSuccess')) {

    function returnSuccess($data, $message = 'Success')
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }
}