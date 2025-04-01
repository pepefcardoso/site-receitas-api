<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller as Controller;

class BaseController extends Controller
{
    /**
     * @param callable $callback
     * @return \Illuminate\Http\JsonResponse
     */
    protected function execute(callable $callback)
    {
        try {
            return $callback();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
