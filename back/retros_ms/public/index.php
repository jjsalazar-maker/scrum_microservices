<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/global/database_config.php';

$cors = require __DIR__.'/../app/middlewares/cors.php';
$sprintss_routers = require __DIR__.'/../app/presentation/routers/endpoints.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Hello world!");
    return $response;
});

$app->post('/suma', function (Request $request, Response $response, $args) {
    $body = $request->getBody()->getContents();
    $data = json_decode($body, true);
    $nombre = $data["nombre"];
    $fecha_inicio = $data["fecha_inicio"];
    $fecha_fin = $data["fecha_fin"] 
    $response->getBody()->write("$num1 + $num2 = $sum");
    return $response;
});

$app->run();

---------------//ELOQUENT---------------

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => '127.0.0.1',
    'database'  => 'mi_base',
    'username'  => 'root',
    'password'  => '',
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

-----------------------//CORS-----------------------
use Psr\Http\Message\ServerRequestInterface as Request;

return function ($app) {
    $app->options('/{routes:.+}', fn($req, $res) => $res);

    $app->add(function (Request $request, $handler) {
        $origin = $request->getHeaderLine('Origin') ?: '*';
        $response = $handler->handle($request);
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', 'true');

        if ($request->getMethod() === 'OPTIONS') {
            return $response->withStatus(200);
        }

        return $response;
    });

};


-------//MIDLEWARE--------------------------
$app->add(function (Request $request, $handler) {
        $headers = $request->getHeader('Authorization');
        $token = $headers[0] ?? null;

        // Ejemplo simple: validar un token fijo
        if ($token !== 'Bearer 12345') {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'No autorizado']));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }

        // Si pasa, continúa al siguiente middleware o a la ruta
        return $handler->handle($request);
    });


    ---------------//ENDPOINTS--------------------
$app->get('/admin', function ($req, $res) {
    $res->getBody()->write("Zona segura");
    return $res;
})->add(function ($req, $handler) {
    $token = $req->getHeaderLine('Authorization');
    if ($token !== 'Bearer 12345') {
        $res = new \Slim\Psr7\Response();
        $res->getBody()->write('No autorizado');
        return $res->withStatus(401);
    }
    return $handler->handle($req);
});


