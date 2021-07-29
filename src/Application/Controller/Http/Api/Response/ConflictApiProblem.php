<?php
declare(strict_types=1);


namespace App\Application\Controller\Http\Api\Response;


use JetBrains\PhpStorm\Pure;

/**
 * Class ConflictApiProblem
 * @package App\Application\Controller\Http\Api\Response
 */
class ConflictApiProblem extends HttpApiProblem
{
    #[Pure] public function __construct(string $detail, array $extensions)
    {
        $type = 'https://github.com/tarohida/php-todo-ddd/wiki/errors#conflict';
        $title = 'Conflict';
        $status = 409;
        parent::__construct($type, $title, $status, $detail, $extensions);
    }
}