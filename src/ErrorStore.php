<?php

declare(strict_types=1);

namespace Ptk\Session;

class ErrorStore
{
    public function __construct()
    {
        $_SESSION['_error'] = [];
    }
    public function set(mixed $error): self
    {
        $_SESSION['_error'][] = $error;
        return $this;
    }
    
    public function get(): mixed
    {
        $error = current($_SESSION['_error']);
        next($_SESSION['_error']);
        return $error;
    }
    
    public function getAll(): array
    {
        return $_SESSION['_error'];
    }

    public function clear(): void
    {
        $_SESSION['_error'] = [];
    }

    public function has(): bool
    {
        return !empty($_SESSION['_error']);
    }
}