<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\JsonResponse;

class UserEmailException extends Exception
{
    /**
     * Http Status Code
     */
    protected $code = Response::HTTP_FORBIDDEN;
    /**
     * Report the exception.
     * @method report
     */
    public function report()
    {
    }
    /**
     * Render the exception into an HTTP response.
     * @method render
     * @return \Illuminate\Http\JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => $this->code,
            'locale' => app()->getLocale(),
            'message' =>  __('messages.EMAIL_ALREADY_EXISTS'),
        ], $this->code);
    }
}
