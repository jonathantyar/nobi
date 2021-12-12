<?php

namespace App\Helpers;

class ResponseWrapper
{
    /**
     * Response Success
     * @message required message of response
     * @result optional object of response
     * @code code status of response
     */
    public static function success(
        String $message,
        Mixed $result = [],
        Bool $status = true,
        Int $code = 200
    ) {
        return response()->json([
            'success' => $status,
            'message' => $message,
            'data' => $result,
        ], $code);
    }

    /**
     * Response Success
     * @message required message of response
     * @code code status of response
     */
    public static function error(
        String $message,
        Int $code = 422
    ) {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $code);
    }
}
