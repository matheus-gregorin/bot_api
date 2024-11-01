<?php

namespace App\Http\Middleware;

use App\Repository\BlackListTokensRepository;
use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try{
            $data = $request->header();

            $tokenHeader = $data['authorization'][0] ?? false;

            if($tokenHeader){

                $blacklistTokenRepository = app(BlackListTokensRepository::class);
                $validToken = $blacklistTokenRepository->getByToken($tokenHeader);

                if(!empty($validToken)){
                    throw new Exception("Expired", 404);
                }

                $explodeString = explode("Bearer ", $tokenHeader, 2) ?? false;
                if(!$explodeString){
                    throw new Exception('Token not content Bearer', 401);
                }

                $token = $explodeString[1] ?? false;
                if(!$token){
                    throw new Exception('Invalid', 401);
                }
    
                $token = JWT::decode($token, new Key(env('SECRET_JWT') ?? "X", 'HS256'));

                if(!$token){
                    throw new Exception("Action not permited, contact support");
                }

                $request['email_guest'] = $token->email;
                $request['permissions_guest'] = $token->permissions;
                $request['tokenExpired'] = $token->exp;

                return $next($request);
            }

            throw new Exception('Not contains Auth', 401);

        } catch (Exception $e) {
            return response()->json(['message' => "Token error - " . $e->getMessage()]);

        }
    }
}
