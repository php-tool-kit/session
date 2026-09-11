<?php

use Ptk\Session\FlashStore;
use Ptk\Session\Session;

test('FlashStore::set() & FlashStore::get() & FlashStore::clear() & FlashStore::has()', function(){
    $session = new Session();
    $msg = 'Arbitrary message';
    expect($session->flash->set($msg))->toBeInstanceOf(FlashStore::class);
    expect($session->flash->has())->toBeTrue();
    expect($session->flash->get())->toBe($msg);
    expect($session->flash->has())->toBeFalse();
});

test('FlashStore::peek()', function(){
    $session = new Session();
    $msg = 'Arbitrary message';
    $session->flash->set($msg);
    expect($session->flash->peek())->toBe($msg);
    expect($session->flash->has())->toBeTrue();
});
