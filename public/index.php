<?php
declare(strict_types=1);

use App\Application\Action\TaskCreateAction;
use App\Application\Action\TaskCreateActionInterface;
use App\Application\Controller\Http\Api\TaskCreateController;
use App\Application\Controller\Http\Api\TaskCreateControllerInterface;
use App\Application\Controller\Http\Handler\HttpErrorHandler;
use App\Application\Controller\Http\Handler\ShutdownHandler;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskService;
use App\Domain\Task\TaskServiceInterface;
use App\Infrastructure\Task\TaskRepository;
use DI\Container;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
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
    $service = $c->get(TaskServiceInterface::class);
    return new TaskCreateAction($repository, $service);
});

$container->set(TaskCreateControllerInterface::class, function (ContainerInterface $c) {
    $action = $c->get(TaskCreateActionInterface::class);
    return new TaskCreateController($action);
});

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->redirect('/', 'tasks');
$app->get('/tasks', function (Request $request, Response $response) {
    $response->getBody()->write('not implement');
    return $response;
});

$app->post('/tasks/{id}', TaskCreateControllerInterface::class);

$displayErrorDetails = true;

$callableResolver = $app->getCallableResolver();
$responseFactory = $app->getResponseFactory();

$serverRequestCreator = ServerRequestCreatorFactory::create();
$request = $serverRequestCreator->createServerRequestFromGlobals();

$errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
$shutdownHandler = new ShutdownHandler($request, $errorHandler, $displayErrorDetails);
register_shutdown_function($shutdownHandler);
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, false, false);
$errorMiddleware->setDefaultErrorHandler($errorHandler);
$app->run();
