<?php

namespace App\Helpers;

use Response;

class JSONResponse {

    public static function send($status, $data, $status_code = 200)
    {
        $response = [];
        if ($status_code < 300 && $status_code > 199) {
            $response['data'] = [
                'code' => $status_code,
                'success' => $status,
                'result' => $data
            ];
        } else {
            $response['error'] = [
                'code' => $status_code,
                'success' => $status,
                'result' => $data
            ];
        }

        return Response::json($response, $status_code);
    }
}