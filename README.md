# Ptk\Session

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)

**php-tool-kit/session** — Utilitários para sessão em PHP.

Uma biblioteca leve e orientada a objetos para gerenciar sessões PHP com segurança, oferecendo repositórios especializados para dados gerais, mensagens *flash*, dados antigos de formulário (*old input*) e erros.

## Recursos

- Inicialização segura da sessão com configurações recomendadas por padrão (`HttpOnly`, `Secure`, `SameSite=Lax`, `use_strict_mode`).
- API fluida (encadeamento de métodos via `return $this`).
- Repositórios dedicados acessíveis como propriedades públicas somente leitura:
  - `Session::$flash` — dados temporários que vivem até serem lidos;
  - `Session::$old` — repopulação de entradas de formulário;
  - `Session::$error` — fila de erros.
- Controle completo do ciclo de vida da sessão: `commit`, `abort`, `reset`, `regenerate`, `destroy`, entre outros.
- Código compatível com análise estática (PHPStan) e tipos estritos (`strict_types=1`).

## Requisitos

- PHP >= 8.5.7

## Instalação

Via Composer:

```bash
composer require php-tool-kit/session
```

## Uso básico

```php
use Ptk\Session\Session;

$session = new Session();

// Armazenar e recuperar valores genéricos
$session->set('usuario', 'everton');
echo $session->get('usuario'); // everton

// Verificar existência e remover
if ($session->has('usuario')) {
    $session->remove('usuario');
}
```

O construtor inicia a sessão automaticamente caso ela ainda não esteja ativa. Você pode personalizar as opções passadas ao `session_start()`:

```php
$session = new Session([
    'cookie_httponly' => 1,
    'cookie_secure' => 1,
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => 1,
    'gc_maxlifetime' => 3600,
]);
```

Se as sessões estiverem desabilitadas no ambiente ou falharem ao iniciar, uma `RuntimeException` será lançada.

## Mensagens flash

Dados *flash* persistem entre requisições até serem lidos, sendo limpos logo em seguida — ideais para mensagens de sucesso/aviso.

```php
// Na requisição que processa o formulário
$session->flash->set('Cadastro realizado com sucesso!');

// Na requisição seguinte (ex.: renderização da página)
if ($session->flash->has()) {
    echo $session->flash->get(); // limpa após a leitura
}

// Ler sem limpar
$valor = $session->flash->peek();
```

## Dados antigos (old input)

Úteis para repopular formulários após falhas de validação.

```php
// Ao detectar erro de validação
$session->old->set('email', $_POST['email']);
// ou
$session->old->setFromArray($_POST);

// No formulário
$email = $session->old->get('email', default: ''); 
$session->old->clear(); // normalmente após o sucesso
```

> Observação: o método `get()` retorna `null` quando o campo não existe.

## Erros

Repositório em formato de fila: cada chamada a `get()` retorna o erro atual e avança o ponteiro interno.

```php
$session->error
    ->set('Informe um e-mail válido.')
    ->set('A senha deve ter no mínimo 8 caracteres.');

if ($session->error->has()) {
    while (($erro = $session->error->get()) !== false) {
        echo htmlspecialchars((string) $erro) . '<br>';
    }
}

$session->error->getAll(); // todos os erros
$session->error->clear();  // limpa tudo
```

## Ciclo de vida da sessão

| Método | Descrição |
|---|---|
| `hasSession()` | Indica se a sessão está ativa (`PHP_SESSION_ACTIVE`). |
| `regenerate()` | Gera um novo ID de sessão (útil após login, para mitigar *session fixation*). |
| `commit()` | Grava os dados e encerra a sessão. |
| `abort()` | Descarta as alterações desde o início e encerra a sessão. |
| `reset()` | Restaura o array `$_SESSION` aos valores gravados originalmente. |
| `clear()` | Remove todas as variáveis de sessão (`session_unset()`). |
| `getId()` / `getName()` | Retorna o ID e o nome da sessão. |
| `destroy()` | Destrói a sessão e invalida o cookie de sessão. |

```php
// Exemplo após autenticação bem-sucedida
$session->regenerate();
$session->set('autenticado', true);
$session->commit();
```

## Configurações padrão

As opções abaixo são aplicadas por padrão e podem ser sobrescritas no construtor:

```php
[
    'cookie_httponly' => 1,
    'cookie_secure' => 1,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => 1,
]
```

## Desenvolvimento

### Executar os testes

```bash
composer test
```

Executa o [Pest](https://pestphp.com) com relatório de cobertura (HTML em `./coverage/html/`) e verificação de cobertura de tipos.

### Análise estática

```bash
composer static
```

Executa o [PHPStan](https://phpstan.org) na base do código.

### Verificação de código

```bash
composer code      # corrige e verifica PSR-1, PSR-2 e PSR-12
composer fix-code  # apenas corrige (phpcbf)
composer psr-code  # apenas verifica (phpcs)
```

## Documentação completa

A documentação gerada está disponível em: <https://php-tool-kit.github.io/session/html/>

## Contribuição

Contribuições são bem-vindas! Abra uma *issue* em [github.com/php-tool-kit/session/issues](https://github.com/php-tool-kit/session/issues) ou envie um *pull request*.

## Licença

Esta biblioteca é distribuída sob a licença [MIT](LICENSE).

---

Desenvolvida por [Everton da Rosa](https://everton3x.github.io).