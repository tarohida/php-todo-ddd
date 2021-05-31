<?php
declare(strict_types=1);


namespace App\Application\Controller\Http\Api\Response;


use JetBrains\PhpStorm\Pure;

/**
 * Class ValidationApiProblem
 * @package App\Application\Controller\Http\Api\Response
 */
class ValidationApiProblem extends HttpApiProblem
{
    #[Pure] public function __construct(string $detail, array $extensions)
    {
        $type = 'https://github.com/tarohida/php-todo-ddd/wiki/errors#validation-error';
        $title = 'Validation Failed';
        $status = 400;
        parent::__construct($type, $title, $status, $detail, $extensions);
    }
}