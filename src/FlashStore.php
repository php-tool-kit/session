<?php

declare(strict_types=1);

namespace Ptk\Session;

class FlashStore
{
    public function __construct()
    {
        $_SESSION['_flash'] = null;
    }
    public function set(mixed $value): self
    {
        $_SESSION['_flash'] = $value;
        return $this;
    }

    public function get(): mixed
    {
        $flash = $_SESSION['_flash'];
        $this->clear();
        return $flash;
    }
    public function peek(): mixed
    {
        return $_SESSION['_flash'];
    }

    public function clear(): void
    {
        $_SESSION['_flash'] = null;
    }

    public function has(): bool
    {
        return !empty($_SESSION['_flash']);
    }
}