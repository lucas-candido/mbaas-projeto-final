<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *     version="1.0",
 *     title="PUCPR Projeto Final - Webservices e MBaaS ",
 *     description="API criada como projeto final da matéria de Webservices e MBaaS - por Lucas Cândido dos Santos",
 *     @OA\Contact(name="Lucas Cândido dos Santos")
 * )
 * @OA\Server(
 *     url="http://localhost:8000/api/v1",
 *     description="API server"
 * )
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      in="header",
 *      name="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * ),
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
