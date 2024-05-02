<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

trait Helper
{

    public function statusCodes($status = false)
    {
        $array = [
            'success' => 200,
            'validation' => 422,
            'not-found-data' => 404,
            'exception' => 205,
            'error-update' => 203,
            'error-insert' => 204,            
            'error-delete' => 207
        ];
        if ($status) {
            return $array[$status];
        }
        return $array;
    }

    public function outApiJson($statusCode, $messages, $data = null, $responseStatus = 200)
    {

        $outData = [];
        $outData['code'] = $this->statusCodes($statusCode);
        $outData['messages'] = $messages;
        if ($data) {
            $outData['data'] = $data;
        }
        return response()->json($outData, $responseStatus);
    }

    public function getExceptionMessage(Exception $exception): string
    {
       return config('app.env') == 'local' ? $exception->getMessage() : trans('main.exception');
    }
}
