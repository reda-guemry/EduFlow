<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Http\Resources\CourseResource;
use App\Services\CourseService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;


class CourseController extends Controller
{


    public function __construct(
        private CourseService $courseService
    ){}

    /**
     * Liste tous les cours.
     *
     * @OA\Get(
     *     path="/courses",
     *     operationId="getCoursesList",
     *     tags={"Courses"},
     *     summary="Lister tous les cours",
     *     description="Récupère la liste de tous les cours disponibles. Les teachers voient uniquement leurs propres cours.",
     *     @OA\Response(
     *         response=200,
     *         description="Liste des cours récupérée avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="courses", type="array", items=@OA\Items(
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
     *             ))
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $courses = $this->courseService->getAll();

        return response() -> json([
            'courses' => $courses , 
        ], 200) ;
    }

    /**
     * Crée un nouveau cours.
     *
     * @OA\Post(
     *     path="/courses",
     *     operationId="createCourse",
     *     tags={"Courses"},
     *     summary="Créer un nouveau cours",
     *     description="Crée un nouveau cours. Seuls les teachers peuvent créer des cours.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données du cours",
     *         @OA\JsonContent(
     *             required={"title", "description", "price", "category_id"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="PHP Avancé"),
     *             @OA\Property(property="description", type="string", example="Cours approfondi de PHP avec patterns modernes"),
     *             @OA\Property(property="price", type="number", format="float", minimum=0, example=49.99),
     *             @OA\Property(property="category_id", type="integer", format="int64", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Cours créé avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="id", type="integer", format="int64", example=1),
     *                 @OA\Property(property="title", type="string", example="PHP Avancé"),
     *                 @OA\Property(property="description", type="string", example="Cours approfondi de PHP avec patterns modernes"),
     *                 @OA\Property(property="price", type="number", format="float", example=49.99),
     *                 @OA\Property(property="teacher_id", type="integer", format="int64", example=2),
     *                 @OA\Property(property="category_id", type="integer", format="int64", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             }
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
    public function store(StoreCourseRequest $request): CourseResource
    {
        $validated = $request->validated();
        $validated['teacher_id'] =  auth('api')->user()->id;

        $course = $this->courseService->create($validated);

        return new CourseResource($course);
    }

    /**
     * Affiche les détails d'un cours.
     *
     * @OA\Get(
     *     path="/courses/{id}",
     *     operationId="getCourseById",
     *     tags={"Courses"},
     *     summary="Récupérer un cours par ID",
     *     description="Récupère les détails complets d'un cours spécifique",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du cours",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cours récupéré avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="id", type="integer", format="int64", example=1),
     *                 @OA\Property(property="title", type="string", example="PHP Avancé"),
     *                 @OA\Property(property="description", type="string", example="Cours approfondi de PHP avec patterns modernes"),
     *                 @OA\Property(property="price", type="number", format="float", example=49.99),
     *                 @OA\Property(property="teacher_id", type="integer", format="int64", example=2),
     *                 @OA\Property(property="category_id", type="integer", format="int64", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cours non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Course not found.")
     *         )
     *     )
     * )
     */
    public function show(int $id): CourseResource
    {
        $course = $this->courseService->getById($id);
        return new CourseResource($course);
    }

    /**
     * Met à jour un cours.
     *
     * @OA\Put(
     *     path="/courses/{id}",
     *     operationId="updateCourse",
     *     tags={"Courses"},
     *     summary="Mettre à jour un cours",
     *     description="Met à jour les détails d'un cours existant. Seul le teacher propriétaire peut mettre à jour.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du cours",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données à mettre à jour",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", maxLength=255, example="PHP Avancé - Edition 2"),
     *             @OA\Property(property="description", type="string", example="Cours approfondi de PHP avec patterns modernes et async"),
     *             @OA\Property(property="price", type="number", format="float", minimum=0, example=59.99),
     *             @OA\Property(property="category_id", type="integer", format="int64", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cours mis à jour avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             properties={
     *                 @OA\Property(property="id", type="integer", format="int64", example=1),
     *                 @OA\Property(property="title", type="string", example="PHP Avancé - Edition 2"),
     *                 @OA\Property(property="description", type="string", example="Cours approfondi de PHP avec patterns modernes et async"),
     *                 @OA\Property(property="price", type="number", format="float", example=59.99),
     *                 @OA\Property(property="teacher_id", type="integer", format="int64", example=2),
     *                 @OA\Property(property="category_id", type="integer", format="int64", example=1),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             }
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
     *         response=422,
     *         description="Erreur de validation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Seul le teacher propriétaire peut mettre à jour"
     *     )
     * )
     */
    public function update(UpdateCourseRequest $request, int $id): CourseResource|JsonResponse
    {
        $validated = $request->validated();

        try {
            $course = $this->courseService->update($id, auth('api')->user()->id, $validated);
            return new CourseResource($course);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }

    /**
     * Supprime un cours.
     *
     * @OA\Delete(
     *     path="/courses/{id}",
     *     operationId="deleteCourse",
     *     tags={"Courses"},
     *     summary="Supprimer un cours",
     *     description="Supprime un cours existant. Seul le teacher propriétaire peut supprimer.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du cours",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Cours supprimé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Course deleted successfully.")
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
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès refusé - Seul le teacher propriétaire peut supprimer"
     *     )
     * )
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->courseService->delete($id, auth('api')->user()->id);
            return response()->json([
                'success' => true,
                'message' => 'Course deleted successfully.',
            ], 200);
        } catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 403);
        }
    }
}
