<?php /** @noinspection PhpTooManyParametersInspection */
declare(strict_types=1);

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write('Hello world!');
    return $response;
});

$logger = new Logger('app');
$streamHandler = new StreamHandler('php://stdout');
$logger->pushHandler($streamHandler);

$errorMiddleware = $app->addErrorMiddleware(true, true, true, $logger);

$app->run();