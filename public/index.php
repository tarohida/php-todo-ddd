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

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container();
$container->set(PDO::class, function () {
    $db_host = $_ENV['DB_HOST'];
    $db_name = $_ENV['DB_NAME'];
    return new PDO(
        "pgsql:host=$db_host;port=5432;dbname=$db_name;",
        $_ENV['DB_USER'],
        $_ENV['DB_PASSWORD']
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

$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', 'http://localhost:8080')
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();