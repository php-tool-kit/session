<?php

declare(strict_types=1);

namespace Ptk\Session;

/**
 * Classe responsável pelo armazenamento temporário (flash) de dados na sessão.
 */
class FlashStore
{
    /**
     * Construtor da classe FlashStore.
     * Inicializa a chave de dados flash na sessão com null.
     */
    public function __construct()
    {
        $_SESSION['_flash'] = null;
    }

    /**
     * Define o valor da mensagem/dado flash na sessão.
     *
     * @param mixed $value Valor a ser armazenado temporariamente.
     * @return $this
     */
    public function set(mixed $value): self
    {
        $_SESSION['_flash'] = $value;
        return $this;
    }

    /**
     * Recupera o dado flash e limpa seu valor da sessão.
     *
     * @return mixed O valor armazenado na sessão ou null.
     */
    public function get(): mixed
    {
        $flash = $_SESSION['_flash'];
        $this->clear();
        return $flash;
    }

    /**
     * Visualiza o dado flash armazenado sem removê-lo da sessão.
     *
     * @return mixed O valor atualmente armazenado.
     */
    public function peek(): mixed
    {
        return $_SESSION['_flash'];
    }

    /**
     * Limpa o dado flash da sessão.
     *
     * @return void
     */
    public function clear(): void
    {
        $_SESSION['_flash'] = null;
    }

    /**
     * Verifica se há algum dado flash armazenado na sessão.
     *
     * @return bool Retorna true se houver dado flash, false caso contrário.
     */
    public function has(): bool
    {
        return !empty($_SESSION['_flash']);
    }
}
