<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BaseApiController extends Controller
{
    protected function success($data = null, string $message = 'Success', int $code = 200): \Illuminate\Http\JsonResponse
    {
        $response = ['success' => true, 'message' => $message];
        if (!is_null($data)) $response['data'] = $data;
        return response()->json($response, $code);
    }

    protected function error(string $message = 'Error', int $code = 400, $errors = null): \Illuminate\Http\JsonResponse
    {
        $response = ['success' => false, 'message' => $message];
        if (!is_null($errors)) $response['errors'] = $errors;
        return response()->json($response, $code);
    }

    protected function paginated($paginator, string $message = 'Success'): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data'    => $paginator->items(),
            'meta'    => [
                'total'        => $paginator->total(),
                'per_page'     => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
            ],
        ]);
    }
}
