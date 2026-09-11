<?php

declare(strict_types=1);

namespace Ptk\Session;

use RuntimeException;

/**
 * Classe principal para gerenciamento de sessões HTTP e sub-repositórios.
 */
class Session
{
    /**
     * Repositório de dados temporários (flash).
     */
    public readonly FlashStore $flash;

    /**
     * Repositório de dados antigos (formulários).
     */
    public readonly OldStore $old;

    /**
     * Repositório de erros.
     */
    public readonly ErrorStore $error;

    /**
     * Opções de configuração padrão da sessão PHP.
     *
     * @var array<string, mixed>
     */
    protected array $defaults = [
        'cookie_httponly' => 1,
        'cookie_secure' => 1,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => 1,
    ];

    /**
     * Construtor da classe Session.
     *
     * @param array<string, mixed>|null $options Opções personalizadas para inicializar a sessão.
     * @throws RuntimeException Se as sessões estiverem desabilitadas ou falharem ao iniciar.
     */
    public function __construct(?array $options = null)
    {
        $this->init($options ?? $this->defaults);

        $this->flash = new FlashStore();
        $this->old = new OldStore();
        $this->error = new ErrorStore();
    }

    /**
     * Inicializa a sessão com as configurações informadas.
     *
     * @param array<string, mixed> $options Opções para session_start().
     * @return void
     * @throws RuntimeException Se as sessões estiverem desabilitadas ou ocorrer falha ao iniciar.
     */
    protected function init(array $options): void
    {
        if ($this->status() === PHP_SESSION_DISABLED) {
            throw new RuntimeException('Sessions are disabled.');
        }
        if ($this->status() === PHP_SESSION_NONE) {
            if (!$this->start($options)) {
                throw new RuntimeException('Fails on start session.');
            }
        }
    }

    /**
     * Verifica se a sessão está ativa.
     *
     * @return bool Retorna true se a sessão estiver ativa, false caso contrário.
     */
    public function hasSession(): bool
    {
        return ($this->status() === PHP_SESSION_ACTIVE);
    }

    /**
     * Lança uma exceção informando a ausência de uma sessão válida.
     *
     * @return void
     * @throws RuntimeException Sempre lança a exceção quando invocado.
     */
    private function throwNoSession(): void
    {
        throw new RuntimeException("Not valid session started.");
    }

    /**
     * Valida se a sessão está ativa antes de executar operações.
     *
     * @return void
     * @throws RuntimeException Se a sessão não estiver ativa.
     */
    private function checkSession(): void
    {
        if (!$this->hasSession()) {
            $this->throwNoSession();
        }
    }

    /**
     * Armazena um valor na sessão sob uma determinada chave.
     *
     * @param string $key Chave de identificação.
     * @param mixed $value Valor a ser armazenado.
     * @return $this
     */
    public function set(string $key, mixed $value): self
    {
        $this->checkSession();
        $_SESSION[$key] = $value;
        return $this;
    }

    /**
     * Recupera um valor armazenado na sessão por sua chave.
     *
     * @param string $key Chave do valor a ser buscado.
     * @return mixed O valor armazenado ou null caso não exista.
     */
    public function get(string $key): mixed
    {
        $this->checkSession();
        return $_SESSION[$key] ?? null;
    }

    /**
     * Remove uma chave e seu respectivo valor da sessão.
     *
     * @param string $key Chave a ser removida.
     * @return void
     */
    public function remove(string $key): void
    {
        $this->checkSession();
        unset($_SESSION[$key]);
    }

    /**
     * Verifica se uma determinada chave existe na sessão.
     *
     * @param string $key Chave a ser verificada.
     * @return bool Retorna true se a chave existir, false caso contrário.
     */
    public function has(string $key): bool
    {
        $this->checkSession();
        return key_exists($key, $_SESSION);
    }

    /**
     * Destrói a sessão atual e apaga o cookie de sessão do cliente, se utilizado.
     *
     * @return void
     */
    public function destroy(): void
    {
        $this->checkSession();
        $_SESSION = [];
        $name = session_name();
        if ($name === false) {
            $name = '';
        }
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                $name,
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

    /**
     * Regenera o ID da sessão mantendo as informações existentes.
     *
     * @return $this
     */
    public function regenerate(): self
    {
        $this->checkSession();
        session_regenerate_id(true);
        return $this;
    }

    /**
     * Retorna o ID da sessão atual.
     *
     * @return string O ID da sessão.
     */
    public function getId(): string
    {
        $this->checkSession();
        $id = session_id();
        if ($id === false) {
            return '';
        }
        return $id;
    }
    
    /**
     * Descarta as alterações no array da sessão e encerra a sessão.
     *
     * @return bool Retorna true em caso de sucesso ou false em caso de falha.
     */
    public function abort(): bool
    {
        $this->checkSession();
        return session_abort();
    }
    
    /**
     * Salva os dados da sessão e encerra a sessão.
     *
     * @return $this
     */
    public function commit(): self
    {
        $this->checkSession();
        session_commit();
        return $this;
    }
    
    /**
     * Retorna o nome da sessão atual.
     *
     * @return string O nome da sessão.
     */
    public function getName(): string
    {
        $this->checkSession();
        $name = session_name();
        if ($name === false) {
            return '';
        }
        return $name;
    }
    
    /**
     * Reinicializa o array da sessão com os valores originalmente gravados.
     *
     * @return $this
     */
    public function reset(): self
    {
        $this->checkSession();
        session_reset();
        return $this;
    }
    
    /**
     * Limpa todas as variáveis de sessão registradas.
     *
     * @return $this
     */
    public function clear(): self
    {
        $this->checkSession();
        session_unset();
        return $this;
    }

    /**
     * Retorna o status atual da sessão PHP.
     *
     * @return int O status da sessão (ex: PHP_SESSION_DISABLED, PHP_SESSION_NONE, PHP_SESSION_ACTIVE).
     */
    public function status(): int
    {
        return session_status();
    }
    
    /**
     * Executa a função session_start() nativa com as opções fornecidas.
     *
     * @param array<string, mixed> $options Opções passadas para a inicialização da sessão.
     * @return bool Retorna true se a sessão foi iniciada com sucesso, false caso contrário.
     */
    protected function start(array $options): bool
    {
        return session_start($options);
    }
}
