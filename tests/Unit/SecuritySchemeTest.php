<?php

use Illuminate\Routing\Route;
use Vortechron\OpenApiGenerator\Data\SecurityScheme;
use Vortechron\OpenApiGenerator\Test\Controller;

it('can create no security scheme', function () {
    $route = new Route('get', '/', [Controller::class, 'basic']);

    expect(SecurityScheme::fromRoute($route)?->toArray())
        ->toBeNull();
});

it('can create sanctum security scheme', function () {
    $route = new Route('get', '/', [Controller::class, 'basic']);
    $route->middleware('auth:sanctum');

    expect(SecurityScheme::fromRoute($route)?->toArray())
        ->toBe([[
            SecurityScheme::BEARER_SECURITY_SCHEME => [],
        ]]);
});

it('can create permissions security scheme', function () {
    $route = new Route('get', '/', [Controller::class, 'basic']);
    $route->middleware('auth:sanctum');
    $route->middleware('can:dummy');

    expect(SecurityScheme::fromRoute($route)?->toArray())
        ->toBe([[
            SecurityScheme::BEARER_SECURITY_SCHEME => ['dummy'],
        ]]);
});
