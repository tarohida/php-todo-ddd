<?php
declare(strict_types=1);

use App\Application\Actions\Task\CreateTaskAction;
use App\Application\Actions\Task\DeleteTaskAction;
use App\Application\Actions\Task\ListTasksAction;
use App\Application\Actions\Task\ViewTaskAction;
use App\Application\Controllers\Http\CreateTaskController;
use App\Application\Controllers\Http\DeleteTaskController;
use App\Application\Controllers\Http\ListTasksController;
use App\Application\Controllers\Http\ViewTaskController;
use App\Domain\Task\TaskService;
use App\Infrastructure\Task\TaskRepository;
use DI\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->set(TaskRepository::class, function () {
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
    return new TaskRepository($pdo);
});

$container->set(TaskService::class, function (ContainerInterface $container) {
    $db = $container->get(TaskRepository::class);
    return new TaskService($db);
});

$container->set(ViewTaskAction::class, function (ContainerInterface $container) {
    $db = $container->get(TaskRepository::class);
    $service = $container->get(TaskService::class);
    return new ViewTaskAction($db, $service);
});

$container->set(ListTasksAction::class, function (ContainerInterface $container) {
    $repository = $container->get(TaskRepository::class);
    $service = $container->get(TaskService::class);
    return new ListTasksAction($repository, $service);
});

$container->set(CreateTaskAction::class, function (ContainerInterface $container) {
    $repository = $container->get(TaskRepository::class);
    $service = $container->get(TaskService::class);
    return new CreateTaskAction($repository, $service);
});

$container->set(DeleteTaskAction::class, function (ContainerInterface $container) {
    $repository = $container->get(TaskRepository::class);
    return new DeleteTaskAction($repository);
});

$container->set(ViewTaskController::class, function (ContainerInterface $container) {
    $action = $container->get(ViewTaskAction::class);
    return new ViewTaskController($action);
});

$container->set(ListTasksController::class, function (ContainerInterface $container) {
    $action = $container->get(ListTasksAction::class);
    return new ListTasksController($action);
});

$container->set(CreateTaskController::class, function (ContainerInterface $container) {
    $action = $container->get(CreateTaskAction::class);
    return new CreateTaskController($action);
});

$container->set(DeleteTaskController::class, function (ContainerInterface $container) {
    $action = $container->get(DeleteTaskAction::class);
    return new DeleteTaskController($action);
});

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
{
    $response->getBody()->write('Hello World');
    return $response;
});

$app->get('/tasks', ListTasksController::class);
$app->get('/tasks/{id}', ViewTaskController::class);
$app->post('/tasks/{id}', CreateTaskController::class);
$app->delete('/tasks/{id}', DeleteTaskController::class);

$app->run();

