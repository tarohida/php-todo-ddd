<?php
declare(strict_types=1);

use App\Application\Actions\Task\ListTaskAction;
use App\Application\Actions\Task\ViewTaskAction;
use App\Application\Controller\Http\ListTaskController;
use App\Application\Controller\Http\ViewTaskController;
use App\Domain\Task\TaskService;
use App\Infrastructure\Task\TaskDB;
use DI\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->set(TaskDB::class, function () {
    $db_host = 'db';
    $db_name = 'db_name';
    $db_user = 'db_user';
    $db_password = 'db_password';
    $pdo = new PDO(
        "pgsql:host=${db_host};dbname=${db_name};",
        $db_user,
        $db_password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    return new TaskDB($pdo);
});

$container->set(TaskService::class, function (ContainerInterface $container) {
    $db = $container->get(TaskDB::class);
    return new TaskService($db);
});

$container->set(ViewTaskAction::class, function (ContainerInterface $container) {
    $db = $container->get(TaskDB::class);
    $service = $container->get(TaskService::class);
    return new ViewTaskAction($db, $service);
});

$container->set(ListTaskAction::class, function (ContainerInterface $container) {
    $db = $container->get(TaskDB::class);
    $service = $container->get(TaskService::class);
    return new ListTaskAction($db, $service);
});

$container->set(ViewTaskController::class, function (ContainerInterface $container) {
    $action = $container->get(ViewTaskAction::class);
    return new ViewTaskController($action);
});

$container->set(ListTaskController::class, function (ContainerInterface $container) {
    $action = $container->get(ListTaskAction::class);
    return new ListTaskController($action);
});

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
{
    $response->getBody()->write('Hello World');
    return $response;
});

$app->get('/tasks', ListTaskController::class);
$app->get('/tasks/{id}', ViewTaskController::class);

$app->run();

