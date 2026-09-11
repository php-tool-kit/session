<?php

declare(strict_types=1);

namespace Ptk\Session;

class OldStore
{
    public function __construct()
    {
        $_SESSION['_old'] = [];
    }
    public function set(string $fieldName, mixed $fieldValue): self
    {
        $_SESSION['_old'][$fieldName] = $fieldValue;
        return $this;
    }
    
    public function setFromArray(array $data): self
    {
        foreach($data as $fieldName => $fieldValue) {
            $_SESSION['_old'][$fieldName] = $fieldValue;
        }
        return $this;
    }

    public function get(string $fieldName): mixed
    {
        return $_SESSION['_old'][$fieldName] ?? null;
    }
    
    public function getAll(): array
    {
        return $_SESSION['_old'];
    }

    public function clear(): void
    {
        $_SESSION['_old'] = [];
    }

    public function has(): bool
    {
        return !empty($_SESSION['_old']);
    }
}