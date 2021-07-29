<?php
declare(strict_types=1);


namespace App\Application\Controller\Http\Api\Response;


/**
 * Interface Rfc7807ResponseInterface
 * @package App\Application\Controller\Http\Api\Response
 */
interface Rfc7807ResponseInterface
{
    public function getType(): ?string;
    public function getTitle(): string;
    public function getStatus(): ?int;
    public function getDetail(): ?string;
    public function getExtensions(): array;
}