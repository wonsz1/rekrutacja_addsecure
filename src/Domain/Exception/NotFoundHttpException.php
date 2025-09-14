<?php

declare(strict_types=1);

namespace Domain\Exception;

class NotFoundHttpException extends \DomainException
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
