<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "News API",
    version: "1.0.0",
    description: "API для управління новинами з блоковою структурою контенту."
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "sanctum",
    description: "Введіть токен, отриманий при логіні"
)]
class SwaggerAnnotations
{
}
