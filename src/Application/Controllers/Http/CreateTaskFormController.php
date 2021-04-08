<?php
declare(strict_types=1);


namespace App\Application\Controllers\Http;


use eftec\bladeone\BladeOne;
use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

class CreateTaskFormController implements CreateTaskFormControllerInterface
{
    /**
     * @var BladeOne
     */
    private BladeOne $blade;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(BladeOne $blade, LoggerInterface $logger)
    {
        $this->blade = $blade;
        $this->logger = $logger;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        try {
            $view = $this->blade->run('task.create_form');
        } catch (Exception $e) {
            $this->logger->error($e);
            $view = 'rendering error';
        }
        $response->getBody()->write($view);
        return $response;
    }
}