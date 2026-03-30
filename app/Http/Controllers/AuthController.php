<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RefreshRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
    ) {
    }

    /**
     * Enregistre un nouvel utilisateur.
     *
     * @OA\Post(
     *     path="/register",
     *     operationId="register",
     *     tags={"Auth"},
     *     summary="S'enregistrer",
     *     description="Crée un nouveau compte utilisateur (étudiant ou teacher)",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données d'inscription",
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "email", "password", "password_confirmation", "role"},
     *             @OA\Property(property="first_name", type="string", maxLength=255, example="Jean"),
     *             @OA\Property(property="last_name", type="string", maxLength=255, example="Dupont"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, example="jean.dupont@example.com"),
     *             @OA\Property(property="password", type="string", minLength=8, example="SecurePassword123"),
     *             @OA\Property(property="password_confirmation", type="string", minLength=8, example="SecurePassword123"),
     *             @OA\Property(property="role", type="string", enum={"student", "teacher"}, example="student")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Utilisateur enregistré avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User registered successfully."),
     *             @OA\Property(property="data", type="object",
     *                 properties={
     *                     @OA\Property(property="id", type="integer", format="int64", example=1),
     *                     @OA\Property(property="first_name", type="string", example="Jean"),
     *                     @OA\Property(property="last_name", type="string", example="Dupont"),
     *                     @OA\Property(property="email", type="string", example="jean.dupont@example.com"),
     *                     @OA\Property(property="role", type="string", example="student"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register(RegisterRequest $request)
    {
        $reponse = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'User registered successfully.',
            'data' => [
                'user' => new UserResource($reponse['user']),
                'token' => $reponse['token'],
                // 'refresh_token' => $reponse['refresh_token'],
            ]
        ], 201)->cookie('refresh_token', $reponse['refresh_token'], 60 * 24 * 7, '/api/refresh', null, false, true);

    }

    /**
     * Se connecte avec email et mot de passe.
     *
     * @OA\Post(
     *     path="/login",
     *     operationId="login",
     *     tags={"Auth"},
     *     summary="Se connecter",
     *     description="Authentifie un utilisateur et retourne un token JWT",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Identifiants de connexion",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, example="jean.dupont@example.com"),
     *             @OA\Property(property="password", type="string", minLength=8, example="SecurePassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Connexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Connexion réussie"),
     *             @OA\Property(property="data", type="object",
     *                 properties={
     *                     @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                     @OA\Property(property="refresh_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                     @OA\Property(property="user", type="object",
     *                         properties={
     *                             @OA\Property(property="id", type="integer", format="int64", example=1),
     *                             @OA\Property(property="first_name", type="string", example="Jean"),
     *                             @OA\Property(property="last_name", type="string", example="Dupont"),
     *                             @OA\Property(property="email", type="string", example="jean.dupont@example.com"),
     *                             @OA\Property(property="role", type="string", example="student")
     *                         }
     *                     )
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Email ou mot de passe incorrect",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Email ou mot de passe incorrect")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
                // 'refresh_token' => $result['refresh_token'],
            ]
        ], 200)->cookie(
                'refresh_token',
                $result['refresh_token'],
                60 * 24 * 7,
                '/api/refresh',
                null,
                false,
                true
            );
    }

    /**
     * Rafraîchit le token JWT.
     *
     * @OA\Post(
     *     path="/refresh",
     *     operationId="refreshToken",
     *     tags={"Auth"},
     *     summary="Rafraîchir le token",
     *     description="Utilise un refresh token pour obtenir un nouveau token JWT",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Refresh token",
     *         @OA\JsonContent(
     *             required={"refresh_token"},
     *             @OA\Property(property="refresh_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Token rafraîchi avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Token refreshed successfully"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *             @OA\Property(property="refresh_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Refresh token invalide",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Invalid refresh token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function refresh(Request $request)
    {
        $newToken = $this->authService->refresh($request->cookie('refresh_token'));

        if (!$newToken) {
            return response()->json([
                'error' => 'Invalid refresh token'
            ], 401);
        }

        return response()->json([
            'message' => 'Token refreshed successfully',
            'user'=> new UserResource($newToken['user']),
            // 'refresh_token' => $newToken['refresh_token'],
            'token' => $newToken['token'],
        ], 200)->cookie(
                'refresh_token',
                $newToken['refresh_token'],
                60 * 24 * 7,
                '/api/refresh',
                null,
                false,
                true
            );
    }

    /**
     * Se déconnecte.
     *
     * @OA\Post(
     *     path="/logout",
     *     operationId="logout",
     *     tags={"Auth"},
     *     summary="Se déconnecter",
     *     description="Déconnecte l'utilisateur et invalide son token JWT",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Déconnexion réussie",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Déconnexion réussie")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function logout()
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ], 200);
    }


}
