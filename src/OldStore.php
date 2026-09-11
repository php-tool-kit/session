<?php

declare(strict_types=1);

namespace Ptk\Session;

/**
 * Classe responsável pelo armazenamento de dados antigos (como entradas de formulário) na sessão.
 */
class OldStore
{
    /**
     * Construtor da classe OldStore.
     * Inicializa o repositório de dados antigos na sessão.
     */
    public function __construct()
    {
        $_SESSION['_old'] = [];
    }

    /**
     * Define o valor antigo para um campo específico.
     *
     * @param string $fieldName Nome do campo.
     * @param mixed $fieldValue Valor do campo.
     * @return $this
     */
    public function set(string $fieldName, mixed $fieldValue): self
    {
        // @phpstan-ignore offsetAccess.nonOffsetAccessible
        $_SESSION['_old'][$fieldName] = $fieldValue;
        return $this;
    }
    
    /**
     * Preenche os dados antigos a partir de um array associativo.
     *
     * @param array<string, mixed> $data Array contendo os pares campo => valor.
     * @return $this
     */
    public function setFromArray(array $data): self
    {
        foreach ($data as $fieldName => $fieldValue) {
            // @phpstan-ignore offsetAccess.nonOffsetAccessible
            $_SESSION['_old'][$fieldName] = $fieldValue;
        }
        return $this;
    }

    /**
     * Obtém o valor antigo de um campo específico.
     *
     * @param string $fieldName Nome do campo a ser recuperado.
     * @return mixed O valor do campo ou null se não existir.
     */
    public function get(string $fieldName): mixed
    {
        // @phpstan-ignore offsetAccess.nonOffsetAccessible
        return $_SESSION['_old'][$fieldName] ?? null;
    }
    
    /**
     * Retorna todos os dados antigos armazenados na sessão.
     *
     * @return array<string, mixed> Array associativo com todos os campos e valores.
     */
    public function getAll(): array
    {
        // @phpstan-ignore return.type
        return $_SESSION['_old'];
    }

    /**
     * Limpa todos os dados antigos armazenados na sessão.
     *
     * @return void
     */
    public function clear(): void
    {
        $_SESSION['_old'] = [];
    }

    /**
     * Verifica se existem dados antigos armazenados na sessão.
     *
     * @return bool Retorna true se houver dados, false caso contrário.
     */
    public function has(): bool
    {
        return !empty($_SESSION['_old']);
    }
}
