<?php
declare(strict_types=1);

use App\Application\Http\Controller\ListTaskController;
use DI\Container;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/', ListTaskController::class);

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

$app->run();