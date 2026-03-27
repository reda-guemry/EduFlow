<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     title="EduFlow API",
 *     version="1.0.0",
 *     description="Documentation API complète pour la plateforme LMS EduFlow. Gestion des cours, étudiants, enseignants, favoris, inscriptions et groupes.",
 *     contact={
 *         "name": "EduFlow Support",
 *         "email": "support@eduflow.dev"
 *     },
 *     license={
 *         "name": "MIT",
 *         "url": "https://opensource.org/licenses/MIT"
 *     }
 * )
 *
 * @OA\Server(
 *     url="{protocol}://{host}:{port}/api",
 *     description="EduFlow API Server",
 *     variables={
 *         @OA\ServerVariable(
 *             serverVariable="protocol",
 *             enum={"http", "https"},
 *             default="http"
 *         ),
 *         @OA\ServerVariable(
 *             serverVariable="host",
 *             enum={"localhost", "api.eduflow.dev"},
 *             default="localhost"
 *         ),
 *         @OA\ServerVariable(
 *             serverVariable="port",
 *             enum={"8000", "443"},
 *             default="8000"
 *         )
 *     }
 * )
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     name="Authorization",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     securityScheme="bearerAuth",
 *     description="Bearer Token pour l'authentification JWT"
 * )
 *
 * @OA\Components(
 *     @OA\Schema(
 *         schema="SuccessResponse",
 *         type="object",
 *         properties={
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Operation successful"),
 *             @OA\Property(property="data", type="object")
 *         }
 *     ),
 *     @OA\Schema(
 *         schema="ErrorResponse",
 *         type="object",
 *         properties={
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Error message")
 *         }
 *     ),
 *     @OA\Schema(
 *         schema="ValidationError",
 *         type="object",
 *         properties={
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Validation failed"),
 *             @OA\Property(property="errors", type="object")
 *         }
 *     )
 * )
 */
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
