<?php

use App\Exceptions\ConnectionActionException;
use App\Models\Agency;
use App\Models\Connection;
use App\Models\Property;
use App\Models\User;
use App\Services\ConnectionService;

test('owner can accept a pending connection', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);

    $result = app(ConnectionService::class)->accept($connection, $owner);

    expect($result->status)->toBe('accepted');
});

test('owner can reject a pending connection', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);

    $result = app(ConnectionService::class)->reject($connection, $owner);

    expect($result->status)->toBe('rejected');
});

test('a non-owner cannot accept a connection', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);

    expect(fn () => app(ConnectionService::class)->accept($connection, $intruder))
        ->toThrow(ConnectionActionException::class);
});

test('a connection that has already been responded to cannot be responded to again', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create(['property_id' => $property->id]);

    app(ConnectionService::class)->accept($connection, $owner);

    expect(fn () => app(ConnectionService::class)->reject($connection->fresh(), $owner))
        ->toThrow(ConnectionActionException::class);
});

test('a pending connection past its expiry is transitioned to expired instead of responded to', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create([
        'property_id' => $property->id,
        'expires_at' => now()->subDay(),
    ]);

    expect(fn () => app(ConnectionService::class)->accept($connection, $owner))
        ->toThrow(ConnectionActionException::class);

    expect($connection->fresh()->status)->toBe('expired');
});

test('a connection with a future expiry can still be accepted', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $connection = Connection::factory()->create([
        'property_id' => $property->id,
        'expires_at' => now()->addDay(),
    ]);

    $result = app(ConnectionService::class)->accept($connection, $owner);

    expect($result->status)->toBe('accepted');
});

test('mine() only returns connections on the given user\'s properties', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $ownProperty = Property::factory()->create(['user_id' => $owner->id]);
    $otherProperty = Property::factory()->create(['user_id' => $other->id]);

    Connection::factory()->create(['property_id' => $ownProperty->id]);
    Connection::factory()->create(['property_id' => $otherProperty->id]);

    expect(app(ConnectionService::class)->mine($owner)->count())->toBe(1);
});

test('mine() excludes owner-initiated connections', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);

    Connection::factory()->create(['property_id' => $property->id, 'initiated_by' => 'owner']);
    Connection::factory()->create(['property_id' => $property->id, 'initiated_by' => 'agency']);

    expect(app(ConnectionService::class)->mine($owner)->count())->toBe(1);
});

test('sentByMe() only returns owner-initiated connections on the given user\'s properties', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    $ownProperty = Property::factory()->create(['user_id' => $owner->id]);
    $otherProperty = Property::factory()->create(['user_id' => $other->id]);

    Connection::factory()->create(['property_id' => $ownProperty->id, 'initiated_by' => 'owner']);
    Connection::factory()->create(['property_id' => $ownProperty->id, 'initiated_by' => 'agency']);
    Connection::factory()->create(['property_id' => $otherProperty->id, 'initiated_by' => 'owner']);

    expect(app(ConnectionService::class)->sentByMe($owner)->count())->toBe(1);
});

test('an owner can initiate a connection to an agency', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $agency = Agency::factory()->create();

    $connection = app(ConnectionService::class)->initiate($owner, $property, $agency, 'Interested in listing.');

    expect($connection->status)->toBe('pending');
    expect($connection->initiated_by)->toBe('owner');
    expect($connection->agency_id)->toBe($agency->id);
});

test('an owner cannot initiate a connection for a property they do not own', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $agency = Agency::factory()->create();

    expect(fn () => app(ConnectionService::class)->initiate($intruder, $property, $agency, null))
        ->toThrow(ConnectionActionException::class);
});

test('an owner cannot initiate a second pending connection to the same agency for the same property', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $agency = Agency::factory()->create();

    app(ConnectionService::class)->initiate($owner, $property, $agency, 'First request.');

    expect(fn () => app(ConnectionService::class)->initiate($owner, $property, $agency, 'Second request.'))
        ->toThrow(ConnectionActionException::class);

    expect(Connection::where('property_id', $property->id)->where('agency_id', $agency->id)->count())->toBe(1);
});

test('an owner can re-initiate a connection to the same agency once the earlier request is no longer pending', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $agency = Agency::factory()->create();

    $first = app(ConnectionService::class)->initiate($owner, $property, $agency, 'First request.');
    app(ConnectionService::class)->acceptAsAgency($first, $agency);

    $second = app(ConnectionService::class)->initiate($owner, $property, $agency, 'Second request.');

    expect($second->status)->toBe('pending');
});

test('an owner can send a pending connection to a different agency for the same property', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $agency = Agency::factory()->create();
    $otherAgency = Agency::factory()->create();

    app(ConnectionService::class)->initiate($owner, $property, $agency, 'To agency one.');
    $second = app(ConnectionService::class)->initiate($owner, $property, $otherAgency, 'To agency two.');

    expect($second->status)->toBe('pending');
});

test('an agency can accept an owner-initiated connection', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $agency = Agency::factory()->create();
    $connection = Connection::factory()->create([
        'property_id' => $property->id,
        'agency_id' => $agency->id,
        'initiated_by' => 'owner',
    ]);

    $result = app(ConnectionService::class)->acceptAsAgency($connection, $agency);

    expect($result->status)->toBe('accepted');
});

test('an agency can reject an owner-initiated connection', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $agency = Agency::factory()->create();
    $connection = Connection::factory()->create([
        'property_id' => $property->id,
        'agency_id' => $agency->id,
        'initiated_by' => 'owner',
    ]);

    $result = app(ConnectionService::class)->rejectAsAgency($connection, $agency);

    expect($result->status)->toBe('rejected');
});

test('a different agency cannot respond to an owner-initiated connection', function () {
    $owner = User::factory()->create();
    $property = Property::factory()->create(['user_id' => $owner->id]);
    $agency = Agency::factory()->create();
    $intruderAgency = Agency::factory()->create();
    $connection = Connection::factory()->create([
        'property_id' => $property->id,
        'agency_id' => $agency->id,
        'initiated_by' => 'owner',
    ]);

    expect(fn () => app(ConnectionService::class)->acceptAsAgency($connection, $intruderAgency))
        ->toThrow(ConnectionActionException::class);
});

test('an agency cannot respond to its own agency-initiated connection via the agency-response methods', function () {
    $agency = Agency::factory()->create();
    $property = Property::factory()->create();
    $connection = Connection::factory()->create([
        'property_id' => $property->id,
        'agency_id' => $agency->id,
        'initiated_by' => 'agency',
    ]);

    expect(fn () => app(ConnectionService::class)->acceptAsAgency($connection, $agency))
        ->toThrow(ConnectionActionException::class);
});
