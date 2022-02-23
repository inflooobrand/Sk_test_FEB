<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
    * Send Success Response
    *
    * @method response
    *
    * @param string $msg
    *
    * @param array $data
    *
    * @return \Illuminate\Http\JsonResponse
    */
    public function response(string $msg, array $data = []): JsonResponse
    {
        $output = [
            "success" => true,
            "code" => Response::HTTP_OK,
            "locale" => app()->getLocale(),
            "message" => $msg,
        ];

        if (count($data) > 0) {
            foreach ($data as $key => $value) {
                $output[$key] = $value;
            }
        }
        return response()->json($output, Response::HTTP_OK);
    }
}
