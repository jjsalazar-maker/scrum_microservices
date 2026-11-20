<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// 1. Configuración de la base de datos (Eloquent)
require __DIR__ . '/../app/global/database_config.php';

// 2. Crear la aplicación Slim
$app = AppFactory::create();

// 3. Middleware de CORS
$cors = require __DIR__ . '/../app/middlewares/cors.php';
$cors($app);

// 4. Registrar endpoints de Sprints
$sprintsRoutes = require __DIR__ . '/../app/presentation/routers/endpoints.php';
$sprintsRoutes($app);

// 5. Registrar endpoints de Retrospectivas
$retrosRoutes = require __DIR__ . '/../app/presentation/routers/retros_endpoints.php';
$retrosRoutes($app);

// 6. Ejecutar la aplicación
$app->run();
