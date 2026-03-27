<?php


namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
   
    public function __construct(
        private CategoryService $categoryService
    ){}

    /**
     * Liste toutes les catégories.
     *
     * @OA\Get(
     *     path="/categories",
     *     operationId="getCategoriesList",
     *     tags={"Categories"},
     *     summary="Liste toutes les catégories",
     *     description="Récupère la liste complète de toutes les catégories disponibles",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des catégories récupérée avec succès",
     *         @OA\JsonContent(
     *             type="array",
     *             items=@OA\Items(
     *                 type="object",
     *                 properties={
     *                     @OA\Property(property="id", type="integer", format="int64", example=1),
     *                     @OA\Property(property="name", type="string", example="Développement Web"),
     *                     @OA\Property(property="slug", type="string", example="developpement-web"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-27T10:30:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2026-03-27T10:30:00Z")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = $this->categoryService->getAllCategories();
        return CategoryResource::collection($categories);
    }

    /**
     * Crée une nouvelle catégorie.
     *
     * @OA\Post(
     *     path="/categories",
     *     operationId="createCategory",
     *     tags={"Categories"},
     *     summary="Créer une nouvelle catégorie",
     *     description="Crée une nouvelle catégorie avec validation des données",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de la catégorie",
     *         @OA\JsonContent(
     *             required={"name", "slug"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="Développement Web"),
     *             @OA\Property(property="slug", type="string", maxLength=255, example="developpement-web")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Catégorie créée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category created successfully."),
     *             @OA\Property(property="data", type="object",
     *                 properties={
     *                     @OA\Property(property="id", type="integer", format="int64", example=1),
     *                     @OA\Property(property="name", type="string", example="Développement Web"),
     *                     @OA\Property(property="slug", type="string", example="developpement-web"),
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
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Rôle teacher requis"
     *     )
     * )
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $category = $this->categoryService->createCategory($validated);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => new CategoryResource($category),
        ], 201);
    }

    /**
     * Affiche les détails d'une catégorie.
     *
     * @OA\Get(
     *     path="/categories/{id}",
     *     operationId="getCategoryById",
     *     tags={"Categories"},
     *     summary="Récupérer une catégorie par ID",
     *     description="Récupère les détails complets d'une catégorie spécifique",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Catégorie récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 properties={
     *                     @OA\Property(property="id", type="integer", format="int64", example=1),
     *                     @OA\Property(property="name", type="string", example="Développement Web"),
     *                     @OA\Property(property="slug", type="string", example="developpement-web"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Catégorie non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     )
     * )
     */
    public function show(int $id): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategoryById($id);
            return response()->json([
                'success' => true,
                'data' => new CategoryResource($category),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }
    }

    /**
     * Met à jour une catégorie.
     *
     * @OA\Put(
     *     path="/categories/{id}",
     *     operationId="updateCategory",
     *     tags={"Categories"},
     *     summary="Mettre à jour une catégorie",
     *     description="Met à jour les détails d'une catégorie existante",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données à mettre à jour",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", maxLength=255, example="Développement Web Avancé"),
     *             @OA\Property(property="slug", type="string", maxLength=255, example="developpement-web-avance")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Catégorie mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Category updated successfully."),
     *             @OA\Property(property="data", type="object",
     *                 properties={
     *                     @OA\Property(property="id", type="integer", format="int64", example=1),
     *                     @OA\Property(property="name", type="string", example="Développement Web Avancé"),
     *                     @OA\Property(property="slug", type="string", example="developpement-web-avance"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Catégorie non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Erreur de validation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Rôle teacher requis"
     *     )
     * )
     */
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $validated = $request->validated();

        try {
            $category = $this->categoryService->updateCategory($id, $validated);
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'data' => new CategoryResource($category),
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }
    }

    /**
     * Supprime une catégorie.
     *
     * @OA\Delete(
     *     path="/categories/{id}",
     *     operationId="deleteCategory",
     *     tags={"Categories"},
     *     summary="Supprimer une catégorie",
     *     description="Supprime une catégorie (imposible si elle contient des cours)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de la catégorie",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Catégorie supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Catégorie non trouvée",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Category not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Conflit - Impossible de supprimer une catégorie avec des cours",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Cannot delete a category that has associated courses.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Rôle teacher requis"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->categoryService->deleteCategory($id);

            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully.',
            ], 204);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
    }
}
