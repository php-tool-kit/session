<?php

use Ptk\Session\Session;

test('Verifica se uma sessão foi criada.', function(){
    $session = new Session();
    expect($session->hasSession())->toBeTrue();
});

test('Dispara RuntimeException se nenhuma sessão foi iniciada.', function(){
    $session = new Session();
    $session->destroy();
    $session->get('key');
})->throws(RuntimeException::class);

test('Verifica se dados são salvos na sessão e depois recuperados.', function(){
    $session = new Session();
    $data = 'Arbitrary data';
    $session->set('my-data', $data);
    expect($session->get('my-data'))->toBe($data);
});

test('Verifica se dados são removidos da sessão.', function(){
    $session = new Session();
    $data = 'Arbitrary data';
    $session->set('my-data', $data);
    $session->remove('my-data');
    expect($session->get('my-data'))->toBeNull();
});

test('Verifica se dados são estão salvos na sessão.', function(){
    $session = new Session();
    $data = 'Arbitrary data';
    $session->set('my-data', $data);
    expect($session->has('my-data'))->toBeTrue();
    $session->remove('my-data');
    expect($session->has('my-data'))->toBeFalse();
});

test('Verifica se a sessão foi regenerada.', function(){
    $session = new Session();
    $old_id = session_id();
    $session->regenerate();
    $new_id = session_id();
    expect($old_id !== $new_id)->toBeTrue();
});

test('Verifica o id da sessão.', function(){
    $session = new Session();
    expect($session->getId() === session_id())->toBeTrue();
});

test('Dispara RuntimeException pois não tem sessão iniciada.', function(){
    $session = new Session();
    $session->set('my-data', 'Arbitrary data');
    $session->commit();
    $session->get('my-data');
})->throws(RuntimeException::class);

test('Verifica se Session::abort() e Session::commit() têm o comportamento esperado.', function(){
    $session = new Session();
    $session->set('my-data', 'Arbitrary data');
    $session->commit();
    $session = new Session();
    $session->set('my-data-will-aborted', 'Arbitrary data');
    $session->abort();
    $session = new Session();
    expect($session->has('my-data'))->toBeTrue();
    expect($session->has('my-data-will-aborted'))->toBeFalse();
});

test('Verifica se o nome da sessão é retornado.', function(){
    $session = new Session();
    expect($session->getName() === session_name())->toBeTrue();
});

test('Verifica se Session::reset() tem o comportamento esperado.', function(){
    $session = new Session();
    $session->set('my-data', 'Arbitrary data');
    $session->commit();
    $session = new Session();
    $session->set('my-data-will-aborted', 'Arbitrary data');
    $session->reset();
    expect($session->has('my-data'))->toBeTrue();
    expect($session->has('my-data-will-aborted'))->toBeFalse();
});

test('Verifica se Session::clear() tem o comportamento esperado.', function(){
    $session = new Session();
    $session->set('my-data', 'Arbitrary data');
    $session->commit();
    $session = new Session();
    $session->set('my-data-will-aborted', 'Arbitrary data');
    $session->clear();
    expect($session->has('my-data'))->toBeFalse();
    expect($session->has('my-data-will-aborted'))->toBeFalse();
});

// Testando exceções em Session::init()

class DisabledSessionMock extends Session
{

    public function __construct(array|null $options = null)
    {

    }

    public function callInit(): void
    {
        $this->init([]);
    }

    public function status(): int
    {
        return PHP_SESSION_DISABLED;
    }
}

test('Dispara RuntimeException se as sessões estiverem desabilitadas.', function(){
    $session = new DisabledSessionMock();
    $session->callInit();
})->throws(RuntimeException::class);

class FailSessionMock extends Session
{
    public function __construct(array|null $options = null)
    {

    }

    public function callInit(): void
    {
        $this->init([]);
    }

    public function status(): int
    {
        return PHP_SESSION_NONE;
    }

    protected function start(array $options): bool
    {
        return false;
    }
}

test('Dispara RuntimeException se acontecer uma falha com session_start().', function(){
    $session = new FailSessionMock();
    $session->callInit();
})->throws(RuntimeException::class);