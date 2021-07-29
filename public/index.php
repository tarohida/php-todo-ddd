<?php
declare(strict_types=1);

use App\Application\Action\TaskCreateAction;
use App\Application\Action\TaskCreateActionInterface;
use App\Application\Controller\Http\Api\ListTaskApiController;
use App\Application\Controller\Http\Api\ListTaskApiControllerInterface;
use App\Application\Controller\Http\Api\TaskCreateController;
use App\Application\Controller\Http\Api\TaskCreateControllerInterface;
use App\Application\Controller\Http\Api\DeleteTaskApiController;
use App\Application\Controller\Http\Api\DeleteTaskApiControllerInterface;
use App\Application\Controller\Http\Handler\HttpErrorHandler;
use App\Application\Controller\Http\Handler\ShutdownHandler;
use App\Domain\Task\DeleteTaskService;
use App\Domain\Task\DeleteTaskServiceInterface;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskService;
use App\Domain\Task\TaskServiceInterface;
use App\Infrastructure\Task\TaskRepository;
use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;
use eftec\bladeone\BladeOne;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Factory\ServerRequestCreatorFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->set(PDO::class, function () {
    return new PDO(
        sprintf('pgsql:host=%s;dbname=%s', 'db', 'db_name'),
        'db_user',
        'db_password'
    );
});
$container->set(BladeOne::class, function () {
    return new BladeOne(__DIR__.'/../views',__DIR__.'/../compiles');
});
$container->set(TaskRepositoryInterface::class, function (ContainerInterface $c) {
    $pdo = $c->get(PDO::class);
    return new TaskRepository($pdo);
});

$container->set(TaskServiceInterface::class, function (ContainerInterface $c) {
    $repository = $c->get(TaskRepositoryInterface::class);
    return new TaskService($repository);
});

$container->set(TaskCreateActionInterface::class, function (ContainerInterface $c){
    $repository = $c->get(TaskRepositoryInterface::class);
    return new TaskCreateAction($repository);
});

$container->set(DeleteTaskServiceInterface::class, function (ContainerInterface $c) {
    $repository = $c->get(TaskRepositoryInterface::class);
    return new DeleteTaskService($repository);
});

$container->set(TaskCreateControllerInterface::class, function (ContainerInterface $c) {
    $action = $c->get(TaskCreateActionInterface::class);
    return new TaskCreateController($action);
});

$container->set(ListTaskApiControllerInterface::class, function (ContainerInterface $c) {
    $repository = $c->get(TaskRepositoryInterface::class);
    return new ListTaskApiController($repository);
});

$container->set(DeleteTaskApiControllerInterface::class, function (ContainerInterface $c) {
    $service = $c->get(DeleteTaskServiceInterface::class);
    return new DeleteTaskApiController($service);
});

$container->set(LoggerInterface::class, function () {
    $formatter = new LineFormatter();
    $formatter->includeStacktraces(true);
    $handler = new StreamHandler('php://stdout', Logger::DEBUG);
    $handler->setFormatter($formatter);
    $logger = new Logger('php-todo-app');
    $logger->pushHandler($handler);
    return $logger;
});

try {
    $logger = $container->get(LoggerInterface::class);
} catch (DependencyException | NotFoundException $e) {
    die('container error');
}

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addRoutingMiddleware();

$app->redirect('/', '/api/tasks');
$app->post('/api/tasks/create', TaskCreateControllerInterface::class);
$app->get('/api/tasks', ListTaskApiControllerInterface::class);
$app->delete('/api/tasks/{id}', DeleteTaskApiControllerInterface::class);
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


$displayErrorDetails = true;

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory, $logger);
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
$errorMiddleware->setDefaultErrorHandler($errorHandler);
$app->run();
