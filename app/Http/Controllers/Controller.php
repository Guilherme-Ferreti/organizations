<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function respondCreated(array|JsonResource $data = []): JsonResponse
    {
        if ($data instanceof JsonResource) {
            return $data->response()->setStatusCode(Response::HTTP_CREATED);
        }

        return response()->json($data, Response::HTTP_CREATED);
    }

    protected function respondOk(array $data): JsonResponse
    {
        return response()->json($data);
    }
}
