<?php

use Ptk\Session\OldStore;
use Ptk\Session\Session;

test('OldStore::setFromArray() & OldStore::getAll()', function(){
    $session = new Session();
    $data = [
        'name' => 'John Wick',
        'email' => 'jwick@mail.com'
    ];
    expect($session->old->setFromArray($data))->toBeInstanceOf(OldStore::class);
    expect($session->old->getAll())->toBeArray()->toBe($data);
});

test('OldStore::set() & OldStore::get()', function(){
    $session = new Session();
    expect($session->old->set('name', 'John Wick'))->toBeInstanceOf(OldStore::class);
    expect($session->old->get('name'))->toBe('John Wick');
});

test('OldStore::has() & OldStore::clear()', function(){
    $session = new Session();
    $data = [
        'name' => 'John Wick',
        'email' => 'jwick@mail.com'
    ];
    $session->old->setFromArray($data);
    expect($session->old->has())->toBeTrue();
    $session->old->clear();
    expect($session->old->has())->toBeFalse();
});