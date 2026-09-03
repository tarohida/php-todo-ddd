<?php
declare(strict_types=1);

use App\Application\Http\Controller\CreateTaskController;
use App\Application\Http\Controller\DeleteTaskController;
use App\Application\Http\Controller\HealthCheckController;
use App\Application\Http\Controller\ListTaskController;
use App\Application\Http\Controller\UpdateTaskCompletionController;
use App\Application\Task\CreateTask;
use App\Application\Task\DeleteTask;
use App\Application\Task\ListTasks;
use App\Application\Task\UpdateTaskCompletion;
use App\Domain\Task\TaskRepositoryInterface;
use App\Infrastructure\Task\TaskRepository;
use DI\Container;
use Psr\Container\ContainerInterface;
use Slim\Exception\HttpException;
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
$responseFactory = $app->getResponseFactory();
$errorMiddleware->setDefaultErrorHandler(
    function ($request, \Throwable $exception) use ($responseFactory) {
        $status = $exception instanceof HttpException ? $exception->getCode() : 500;
        $messages = [
            400 => 'Bad Request',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
        ];
        $body = json_encode([
            'error' => [
                'status' => $status,
                'message' => $messages[$status] ?? 'Internal Server Error',
            ],
        ], JSON_THROW_ON_ERROR);
        $response = $responseFactory->createResponse($status);
        $response->getBody()->write($body);
        return $response->withHeader('Content-Type', 'application/json');
    }
);

$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    if ($request->getHeaderLine('Origin') !== $_ENV['ALLOW_ORIGIN_URL']) {
        return $response;
    }
    return $response
        ->withHeader('Access-Control-Allow-Origin', $_ENV['ALLOW_ORIGIN_URL'])
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Accept, Origin')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE, OPTIONS')
        ->withHeader('Vary', 'Origin');
});

$app->run();
