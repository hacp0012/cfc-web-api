<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class SanctumCustomMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    // return $next($request);
    $bearer = $request->bearerToken();

    if (!$bearer) {
      return response()->json([
        'success' => false,
        'error' => 'Autorization required!',
      ], 401);
    }

    [$id, $token] = explode('|', $bearer, 2);

    $item = DB::table('personal_access_tokens')->where('id', $id);// ->find($id);
    $instance = $item->first();

    if ($instance != null && hash('sha256', $token) === $instance->token) {

      if ($user = \App\Models\User::find($instance->tokenable_id)) {
        Auth::login($user);

        $item->update(['last_used_at' => now()]);

        return $next($request);
      }
    }

    return response()->json([
      'success' => false,
      'error' => 'Access denied.',
    ], 401);
  }
}
