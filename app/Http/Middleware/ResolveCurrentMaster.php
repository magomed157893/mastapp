<?php

namespace App\Http\Middleware;

use App\Models\Master;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveCurrentMaster
{
    public function handle(Request $request, Closure $next): Response
    {
        $master = Master::find($request->header('X-Master-Id'));

        if (is_null($master)) {
            return response()->json(
                ['message' => 'Current master not found'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        $request->attributes->set('current_master', $master);
        return $next($request);
    }
}
