<?php

namespace App\Actions\Support;

use RuntimeException;

class ActionException extends RuntimeException
{
    protected ?string $field;

    public function __construct(string $message, ?string $field = null)
    {
        parent::__construct($message);
        $this->field = $field;
    }

    public function getField(): ?string
    {
        return $this->field;
    }
}
