<?php
declare(strict_types=1);

use App\Application\Http\Controller\CreateTaskController;
use App\Application\Http\Controller\ListTaskController;
use App\Domain\Task\CreateTaskService;
use App\Domain\Task\ListTaskService;
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

$container->set(ListTaskService::class, function (ContainerInterface $c) {
    $repository = $c->get(TaskRepositoryInterface::class);
    return new ListTaskService($repository);
});

$container->set(ListTaskController::class, function (ContainerInterface $c) {
    $service = $c->get(ListTaskService::class);
    return new ListTaskController($service);
});

$container->set(CreateTaskController::class, function (ContainerInterface $c) {
    $repository = $c->get(TaskRepository::class);
    $service = new CreateTaskService($repository);
    return new CreateTaskController($service);
});
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/tasks', ListTaskController::class);
$app->post('/tasks/create', CreateTaskController::class);

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();