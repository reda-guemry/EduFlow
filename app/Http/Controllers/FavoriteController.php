<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Services\FavoriteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends Controller
{
    public function __construct(
        private FavoriteService $favoriteService
    ){}

    /**
     * Liste les cours favoris de l'étudiant connecté.
     *
     * @OA\Get(
     *     path="/favorites",
     *     operationId="getFavoritesList",
     *     tags={"Favorites"},
     *     summary="Lister les cours favoris",
     *     description="Récupère la liste paginée des cours favoris de l'étudiant connecté",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Nombre de résultats par page (défaut: 15)",
     *         @OA\Schema(type="integer", format="int32", minimum=1, example=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des cours favoris récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             items=@OA\Items(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(property="id", type="integer", format="int64", example=1),
     *                     @OA\Property(property="title", type="string", example="PHP Avancé"),
     *                     @OA\Property(property="description", type="string", example="Cours approfondi de PHP avec patterns modernes"),
     *                     @OA\Property(property="price", type="number", format="float", example=49.99),
     *                     @OA\Property(property="teacher_id", type="integer", format="int64", example=2),
     *                     @OA\Property(property="category_id", type="integer", format="int64", example=1),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Rôle student requis"
     *     )
     * )
     */
    public function index(Request $request): AnonymousResourceCollection
    {

        $perPage = $request->query('per_page', 15);

        $favorites = $this->favoriteService->getUserFavorites(auth('api')->user()->id, $perPage);

        return CourseResource::collection($favorites);
    }

    /**
     * Ajoute un cours aux favoris.
     *
     * @OA\Post(
     *     path="/favorites/{course}",
     *     operationId="addFavorite",
     *     tags={"Favorites"},
     *     summary="Ajouter un cours aux favoris",
     *     description="Ajoute un cours à la liste des favoris de l'étudiant connecté",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="ID du cours à ajouter aux favoris",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cours ajouté aux favoris avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Course added to favorites successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cours non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Course not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflit - Cours déjà dans les favoris",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="This course is already in your favorites.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Rôle student requis"
     *     )
     * )
     */
    public function store(int $courseId): JsonResponse
    {

        try {
            $added = $this->favoriteService->addFavorite(auth('api')->user()->id, $courseId);

            if (!$added) {
                return response()->json([
                    'success' => false,
                    'message' => 'This course is already in your favorites.',
                ], 409);
            }

            return response()->json([
                'success' => true,
                'message' => 'Course added to favorites successfully.',
            ], 201);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found.',
            ], 404);
        }
    }

    /**
     * Supprime un cours des favoris.
     *
     * @OA\Delete(
     *     path="/favorites/{course}",
     *     operationId="removeFavorite",
     *     tags={"Favorites"},
     *     summary="Supprimer un cours des favoris",
     *     description="Supprime un cours de la liste des favoris de l'étudiant connecté",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="ID du cours à supprimer des favoris",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cours supprimé des favoris avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Course removed from favorites successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cours non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Course not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflit - Cours n'est pas dans les favoris",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="This course is not in your favorites.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Rôle student requis"
     *     )
     * )
     */
    public function destroy(int $courseId): JsonResponse
    {
        try {
            $removet = $this->favoriteService->removeFavorite( auth('api')->user()->id, $courseId);
            
            if (!$removet) {
                return response()->json([
                    'success' => false,
                    'message' => 'This course is not in your favorites.',
                ], 409);
            }

            return response()->json([
                'success' => true,
                'message' => 'Course removed from favorites successfully.',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found.',
            ], 404);
        }

    }

    
}
