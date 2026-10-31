<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/Base/database.php';

$app = AppFactory::create();

$students_endpoints = require __DIR__.'/../app/Presentation/Routers/students_endpoints.php';
$teachers_endpoints = require __DIR__.'/../app/Presentation/Routers/teachers_endpoints.php';
$cors = require __DIR__.'/../app/Presentation/Middleware/cors.php';

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->post("/sumar", function (Request $request, Response $response, $args) {
    $body = $request->getBody()->getContents();
    $data = json_decode($body, true);
    $num1 = $data["numero1"];
    $num2 = $data["numero2"];
    $suma = $num1 + $num2;
    $response->getBody()->write("$num1 + $num2 = $suma");
    return $response;
});

$students_endpoints($app);
$teachers_endpoints($app);

// Manejo de errores: si algo falla (ruta inexistente, error inesperado) se responde en JSON.
// Se agrega ANTES que el CORS para que las respuestas de error tambien lleven las cabeceras CORS.
// Con displayErrorDetails = true se ve el detalle del error (solo para desarrollo).
$error_middleware = $app->addErrorMiddleware(true, true, true);
$error_middleware->getDefaultErrorHandler()->forceContentType('application/json');

// Politicas de CORS (ver "Configuracion de politicas de cors" del PDF)
$cors($app);

$app->run();
