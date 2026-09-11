<?php

// A documentação PHPDoc foi adicionada às classes, métodos e propriedades para manter compatibilidade com o PHPStan em Português do Brasil, sem alterar o código-fonte original.

declare(strict_types=1);

namespace Ptk\Session;

/**
 * Classe responsável pelo armazenamento e gerenciamento de erros na sessão.
 */
class ErrorStore
{
    /**
     * Construtor da classe ErrorStore.
     * Inicializa a estrutura de erros no array de sessão.
     */
    public function __construct()
    {
        $_SESSION['_error'] = [];
    }

    /**
     * Adiciona um novo erro ao repositório de erros na sessão.
     *
     * @param mixed $error O erro a ser armazenado.
     * @return $this
     */
    public function set(mixed $error): self
    {
        // @phpstan-ignore offsetAccess.nonOffsetAccessible
        $_SESSION['_error'][] = $error;
        return $this;
    }
    
    /**
     * Obtém o erro atual da fila e avança o ponteiro interno de erros.
     *
     * @return mixed O erro atual ou false caso não existam mais erros.
     */
    public function get(): mixed
    {
        // @phpstan-ignore argument.type
        $error = current($_SESSION['_error']);
        // @phpstan-ignore argument.type
        next($_SESSION['_error']);
        return $error;
    }
    
    /**
     * Retorna todos os erros armazenados na sessão.
     *
     * @return array<mixed> Lista contendo todos os erros armazenados.
     */
    public function getAll(): array
    {
        // @phpstan-ignore return.type
        return $_SESSION['_error'];
    }

    /**
     * Limpa todos os erros armazenados na sessão.
     *
     * @return void
     */
    public function clear(): void
    {
        $_SESSION['_error'] = [];
    }

    /**
     * Verifica se existem erros armazenados na sessão.
     *
     * @return bool Retorna true se houver erros, false caso contrário.
     */
    public function has(): bool
    {
        return !empty($_SESSION['_error']);
    }
}
