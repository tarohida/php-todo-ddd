<?php
declare(strict_types=1);

use App\Application\Actions\Task\CreateTaskAction;
use App\Application\Actions\Task\CreateTaskActionInterface;
use App\Application\Actions\Task\DeleteTaskAction;
use App\Application\Actions\Task\DeleteTaskActionInterface;
use App\Application\Actions\Task\ListTasksAction;
use App\Application\Actions\Task\ListTasksActionInterface;
use App\Application\Actions\Task\ViewTaskAction;
use App\Application\Actions\Task\ViewTaskActionInterface;
use App\Application\Controllers\Http\CreateTaskController;
use App\Application\Controllers\Http\CreateTaskControllerInterface;
use App\Application\Controllers\Http\CreateTaskFormController;
use App\Application\Controllers\Http\CreateTaskFormControllerInterface;
use App\Application\Controllers\Http\DeleteTaskController;
use App\Application\Controllers\Http\DeleteTaskControllerInterface;
use App\Application\Controllers\Http\DeleteTaskFormController;
use App\Application\Controllers\Http\DeleteTaskFormControllerInterface;
use App\Application\Controllers\Http\ListTasksController;
use App\Application\Controllers\Http\ListTasksControllerInterface;
use App\Application\Controllers\Http\ViewTaskController;
use App\Application\Controllers\Http\ViewTaskControllerInterface;
use App\Domain\Task\TaskRepositoryInterface;
use App\Domain\Task\TaskService;
use App\Domain\Task\TaskServiceInterface;
use App\Infrastructure\Task\TaskRepository;
use DI\Container;
use eftec\bladeone\BladeOne;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$container = new Container();
$container->set(PDO::class, function(){
    $db_host = 'db';
    $db_name = 'db_name';
    $db_user = 'db_user';
    $db_password = 'db_password';
    return new PDO(
        "pgsql:host=${db_host};dbname=${db_name};",
        $db_user,
        $db_password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
});
$container->set(TaskRepositoryInterface::class, function (ContainerInterface $container) {
    $pdo = $container->get(PDO::class);
    return new TaskRepository($pdo);
});

$container->set(BladeOne::class, function (){
    $views = __DIR__ . '/../views';
    $cache = __DIR__ . '/../cache';
    return new BladeOne($views, $cache, BladeOne::MODE_AUTO);
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

$container->set(TaskServiceInterface::class, function (ContainerInterface $container) {
    $db = $container->get(TaskRepositoryInterface::class);
    return new TaskService($db);
});

$container->set(ViewTaskActionInterface::class, function (ContainerInterface $container) {
    $db = $container->get(TaskRepositoryInterface::class);
    $service = $container->get(TaskServiceInterface::class);
    return new ViewTaskAction($db, $service);
});

$container->set(ListTasksActionInterface::class, function (ContainerInterface $container) {
    $repository = $container->get(TaskRepositoryInterface::class);
    $service = $container->get(TaskServiceInterface::class);
    return new ListTasksAction($repository, $service);
});

$container->set(CreateTaskActionInterface::class, function (ContainerInterface $container) {
    $repository = $container->get(TaskRepositoryInterface::class);
    $service = $container->get(TaskServiceInterface::class);
    return new CreateTaskAction($repository, $service);
});

$container->set(DeleteTaskActionInterface::class, function (ContainerInterface $container) {
    $repository = $container->get(TaskRepositoryInterface::class);
    return new DeleteTaskAction($repository);
});

$container->set(ViewTaskControllerInterface::class, function (ContainerInterface $container) {
    $action = $container->get(ViewTaskActionInterface::class);
    return new ViewTaskController($action);
});

$container->set(ListTasksControllerInterface::class, function (ContainerInterface $container) {
    $action = $container->get(ListTasksActionInterface::class);
    return new ListTasksController($action);
});

$container->set(CreateTaskControllerInterface::class, function (ContainerInterface $container) {
    $action = $container->get(CreateTaskActionInterface::class);
    return new CreateTaskController($action);
});

$container->set(CreateTaskFormControllerInterface::class, function (ContainerInterface $container) {
    $blade = $container->get(BladeOne::class);
    $logger = $container->get(LoggerInterface::class);
    return new CreateTaskFormController($blade, $logger);
});

$container->set(DeleteTaskFormControllerInterface::class, function (ContainerInterface $container) {
    $view = $container->get(BladeOne::class);
    $logger = $container->get(LoggerInterface::class);
    return new DeleteTaskFormController($view, $logger);
});

$container->set(DeleteTaskControllerInterface::class, function (ContainerInterface $container) {
    $action = $container->get(DeleteTaskActionInterface::class);
    return new DeleteTaskController($action);
});

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->redirect('/', '/tasks');
$app->get('/tasks', ListTasksControllerInterface::class);
$app->get('/tasks/create', CreateTaskFormControllerInterface::class);
$app->get('/tasks/delete', DeleteTaskFormControllerInterface::class);
$app->get('/tasks/{id}', ViewTaskControllerInterface::class);
$app->post('/tasks/{id}', CreateTaskControllerInterface::class);
$app->delete('/tasks/{id}', DeleteTaskControllerInterface::class);

$app->run();

