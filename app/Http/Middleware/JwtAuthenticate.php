<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\JwtService;
use Illuminate\Http\Request;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;

class JwtAuthenticate
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['message' => 'Token not provided'], 401);
        }

        try {
            $payload = $this->jwtService->decode($token);
            $request->merge(['user_id' => $payload->sub]);
        } catch (ExpiredException $e) {
            return response()->json(['message' => 'Token has expired'], 401);
        } catch (SignatureInvalidException $e) {
            return response()->json(['message' => 'Invalid token signature'], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid token'], 401);
        }

        return $next($request);
    }
}
