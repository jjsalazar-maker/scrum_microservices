<?php

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// 1. Configuración de la base de datos (Eloquent)
require __DIR__ . '/../app/global/database_cofig.php';

// 2. Crear la aplicación Slim
$app = AppFactory::create();

// 3. Middleware de CORS
$cors = require __DIR__ . '/../app/middlewares/cors.php';
$cors($app);

// 4. Registrar las rutas / endpoints
$routes = require __DIR__ . '/../app/presentation/routers/endpoints.php';
$routes($app);

// 5. Ejecutar la aplicación
$app->run();
