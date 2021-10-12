<?php
declare(strict_types=1);

use App\Application\Http\Controller\CreateTaskController;
use App\Application\Http\Controller\DeleteTaskController;
use App\Application\Http\Controller\ListTaskController;
use App\Domain\Task\CreateTaskService;
use App\Domain\Task\TaskRepositoryInterface;
use App\Infrastructure\Task\TaskRepository;
use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->set(PDO::class, function () {
    return new PDO(
        'pgsql:host=db;port=5432;dbname=db_name;',
        'db_user',
        'db_password'
    );
});

$container->set(TaskRepositoryInterface::class, function (ContainerInterface $c) {
    $pdo = $c->get(PDO::class);
    return new TaskRepository($pdo);
});

$container->set(ListTaskController::class, function (ContainerInterface $c) {
    $repository = $c->get(TaskRepositoryInterface::class);
    return new ListTaskController($repository);
});

$container->set(CreateTaskController::class, function (ContainerInterface $c) {
    $repository = $c->get(TaskRepositoryInterface::class);
    $service = new CreateTaskService($repository);
    return new CreateTaskController($service);
});

$container->set(DeleteTaskController::class, function (ContainerInterface $c) {
    $repository = $c->get(TaskRepositoryInterface::class);
    return new DeleteTaskController($repository);
});
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/tasks', ListTaskController::class);
$app->post('/tasks/create', CreateTaskController::class);
$app->delete('/tasks/{id}', DeleteTaskController::class);

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();