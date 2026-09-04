<?php
declare(strict_types=1);

use App\Application\Http\Controller\CreateTaskController;
use App\Application\Http\Controller\DeleteTaskController;
use App\Application\Http\Controller\HealthCheckController;
use App\Application\Http\Controller\ListTaskController;
use App\Application\Http\Controller\UpdateTaskCompletionController;
use App\Application\Http\Error\JsonErrorHandler;
use App\Application\Http\Middleware\RequestLoggingMiddleware;
use App\Application\Task\CreateTask;
use App\Application\Task\DeleteTask;
use App\Application\Task\ListTasks;
use App\Application\Task\UpdateTaskCompletion;
use App\Domain\Task\TaskRepositoryInterface;
use App\Infrastructure\Logging\JsonLoggerFactory;
use App\Infrastructure\Task\TaskRepository;
use DI\Container;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container();
$container->set(LoggerInterface::class, static function () {
    return (new JsonLoggerFactory())->create();
});
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
    return new ListTaskController(new ListTasks($repository));
});

$container->set(CreateTaskController::class, function (ContainerInterface $c) {
    $repository = $c->get(TaskRepositoryInterface::class);
    return new CreateTaskController(new CreateTask($repository));
});

$container->set(DeleteTaskController::class, function (ContainerInterface $c) {
    $repository = $c->get(TaskRepositoryInterface::class);
    return new DeleteTaskController(new DeleteTask($repository));
});
$container->set(UpdateTaskCompletionController::class, function (ContainerInterface $c) {
    $repository = $c->get(TaskRepositoryInterface::class);
    return new UpdateTaskCompletionController(new UpdateTaskCompletion($repository));
});
$container->set(HealthCheckController::class, function (ContainerInterface $c) {
    return new HealthCheckController(static fn (): PDO => $c->get(PDO::class));
});
AppFactory::setContainer($container);
$app = AppFactory::create();
$responseFactory = $app->getResponseFactory();
$jsonErrorHandler = new JsonErrorHandler($responseFactory);
$container->set(JsonErrorHandler::class, $jsonErrorHandler);
$container->set(RequestLoggingMiddleware::class, static function (ContainerInterface $c) {
    return new RequestLoggingMiddleware(
        $c->get(LoggerInterface::class),
        $c->get(JsonErrorHandler::class),
    );
});

$app->get('/tasks', ListTaskController::class);
$app->post('/tasks', CreateTaskController::class);
$app->post('/tasks/create', CreateTaskController::class);
$app->delete('/tasks/{id}', DeleteTaskController::class);
$app->patch('/tasks/{id}', UpdateTaskCompletionController::class);
$app->get('/health', HealthCheckController::class);

$app->options('/{routes:.+}', function ($request, $response) {
    return $response;
});

$app->addBodyParsingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(false, true, true);
$errorMiddleware->setDefaultErrorHandler($jsonErrorHandler);

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    if ($request->getHeaderLine('Origin') !== $_ENV['ALLOW_ORIGIN_URL']) {
        return $response;
    }
    return $response
        ->withHeader('Access-Control-Allow-Origin', $_ENV['ALLOW_ORIGIN_URL'])
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept, Origin, X-Request-Id')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE, OPTIONS')
        ->withHeader('Access-Control-Expose-Headers', 'X-Request-Id')
        ->withHeader('Vary', 'Origin');
});

$app->add(RequestLoggingMiddleware::class);

$app->run();
