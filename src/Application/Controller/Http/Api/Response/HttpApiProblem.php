<?php
declare(strict_types=1);


namespace App\Application\Controller\Http\Api\Response;


use App\Exception\TodoAppException;
use JetBrains\PhpStorm\Pure;

/**
 * Class HttpApiProblem
 * @package App\Application\Controller\Http\Api\Response
 */
abstract class HttpApiProblem extends TodoAppException implements Rfc7807ResponseInterface
{
    /**
     * HttpApiProblem constructor.
     * @param string $type
     * @param string $title
     * @param int $status
     * @param string $detail
     * @param array $extensions
     */
    #[Pure] public function __construct(
        private string $type,
        private string $title,
        private int $status,
        private string $detail,
        private array $extensions
    ){
        parent::__construct();
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function getExtensions(): array
    {
        return $this->extensions;
    }
}