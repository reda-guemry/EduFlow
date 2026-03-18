<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService , 
    ){}

    public function register(RegisterRequest $request)
    {
        $reponse = $this->authService->register($request->validated());

        return response()->json($reponse) ;

    }


    public function login(LoginRequest $request)
    {
        // return response()->json([$request->validated()]) ; 

        // $result = $this->authService->login($request->validated());
        $result = $this->authService->login($request->validated());

        // return response()->json($result) ;

        if (!$result) {
            return response()->json([
                'error' => 'Email ou mot de passe incorrect'
            ], 401); 
        }

        return response()->json([
            'message' => 'Connexion réussie',
            'data'    => $result
        ], 200);
    }

    public function refresh(RefreshRequest $request)
    {
        $newToken = $this->authService->refresh($request->refresh_token);

        if (!$newToken) {
            return response()->json([
                'error' => 'Invalid refresh token'
            ], 401);
        }

        return response()->json([
            'message' => 'Token refreshed successfully',
            'refresh_token' => $newToken['refresh_token'] , 
            'token' => $newToken['token'] ,
        ], 200);
    }

    public function logout()
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ], 200);
    }
    

}
