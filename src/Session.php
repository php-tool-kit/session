<?php

declare(strict_types=1);

namespace Ptk\Session;

use RuntimeException;

class Session
{
    public readonly FlashStore $flash;
    public readonly OldStore $old;
    public readonly ErrorStore $error;

    protected array $defaults = [
        'cookie_httponly' => 1,
        'cookie_secure' => 1,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => 1,
    ];

    public function __construct(?array $options = null)
    {
        $this->init($options ?? $this->defaults);

        $this->flash = new FlashStore();
        $this->old = new OldStore();
        $this->error = new ErrorStore();
    }
    protected function init(?array $options = null): void
    {
        if ($this->status() === PHP_SESSION_DISABLED) {
            throw new RuntimeException('Sessions are disabled.');
        }
        if ($this->status() === PHP_SESSION_NONE) {
            if (!$this->start($options))
                throw new RuntimeException('Fails on start session.');
        }
    }

    public function hasSession(): bool
    {
        return ($this->status() === PHP_SESSION_ACTIVE);
    }

    private function throwNoSession(): void
    {
        throw new RuntimeException("Not valid session started.");
    }

    private function checkSession(): void
    {
        if (!$this->hasSession())
            $this->throwNoSession();
    }

    public function set(string $key, mixed $value): self
    {
        $this->checkSession();
        $_SESSION[$key] = $value;
        return $this;
    }

    public function get(string $key): mixed
    {
        $this->checkSession();
        return $_SESSION[$key] ?? null;
    }

    public function remove(string $key): void
    {
        $this->checkSession();
        unset($_SESSION[$key]);
    }

    public function has(string $key): bool
    {
        $this->checkSession();
        return key_exists($key, $_SESSION);
    }

    public function destroy(): void
    {
        $this->checkSession();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                [
                    'expires' => time() - 42000,
                    'path' => $params['path'],
                    'domain' => $params['domain'],
                    'secure' => $params['secure'],
                    'httponly' => $params['httponly']
                ]
            );
        }
        session_destroy();
    }

    public function regenerate(): self
    {
        $this->checkSession();
        session_regenerate_id(true);
        return $this;
    }

    public function getId(): string
    {
        $this->checkSession();
        return session_id();
    }
    
    public function abort(): bool
    {
        $this->checkSession();
        return session_abort();
    }
    
    public function commit(): self
    {
        $this->checkSession();
        session_commit();
        return $this;
    }
    
    public function getName(): string
    {
        $this->checkSession();
        return session_name();
    }
    
    public function reset(): self
    {
        $this->checkSession();
        session_reset();
        return $this;
    }
    
    public function clear(): self
    {
        $this->checkSession();
        session_unset();
        return $this;
    }

    public function status(): int
    {
        return session_status();
    }
    
    protected function start(array $options): bool
    {
        return session_start($options);
    }


}