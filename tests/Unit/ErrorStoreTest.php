<?php

use Ptk\Session\ErrorStore;
use Ptk\Session\Session;

test('ErrorStore::set() & ErrorStore::get()', function(){
    $session = new Session();
    $msg1 = 'Arbitrary error 1';
    $msg2 = 'Arbitrary error 2';
    expect($session->error->set($msg1))->toBeInstanceOf(ErrorStore::class);
    expect($session->error->set($msg2))->toBeInstanceOf(ErrorStore::class);
    expect($session->error->get())->toBe($msg1);
    expect($session->error->get())->toBe($msg2);
    expect($session->error->get())->toBeFalse();
});

test('ErrorStore::getAll()', function(){
    $session = new Session();
    $msg1 = 'Arbitrary error 1';
    $msg2 = 'Arbitrary error 2';
    $session->error->set($msg1);
    $session->error->set($msg2);
    expect($session->error->getAll())->toBeArray()->toHaveCount(2);
});

test('ErrorStore::clear()', function(){
    $session = new Session();
    $msg1 = 'Arbitrary error 1';
    $msg2 = 'Arbitrary error 2';
    $session->error->set($msg1);
    $session->error->set($msg2);
    $session->error->clear();
    expect($session->error->getAll())->toBeArray()->toBeEmpty();
});

test('ErrorStore::has()', function(){
    $session = new Session();
    $msg1 = 'Arbitrary error 1';
    $msg2 = 'Arbitrary error 2';
    $session->error->set($msg1);
    $session->error->set($msg2);
    expect($session->error->has())->toBeTrue();
    $session->error->clear();
    expect($session->error->has())->toBeFalse();
});

