<?php

use Illuminate\Routing\Route;
use Spatie\LaravelData\Data;
use Vortechron\OpenApiGenerator\Data\OpenApi;
use Vortechron\OpenApiGenerator\Data\RequestBody;

it('can detect no request body', function () {
    foreach (['basic', 'intParameter', 'stringParameter', 'modelParameter', 'requestNoData'] as $function) {
        $route  = new Route('get', '/', [Controller::class, $function]);
        $method = methodFromRoute($route);

        expect(RequestBody::getFirstOfClassType($method, LaravelData::class))
            ->toBeNull();
    }
});

it('can detect request body', function () {
    foreach (['requestBasic', 'contentType', 'allCombined'] as $function) {
        $route  = new Route('get', '/', [Controller::class, $function]);
        $method = methodFromRoute($route);

        expect(RequestBody::getFirstOfClassType($method, Data::class))
            ->toBeInstanceOf(ReflectionNamedType::class);
    }
});

it('can create no request body', function () {
    foreach (['basic', 'intParameter', 'stringParameter', 'modelParameter', 'requestNoData'] as $function) {
        $route  = new Route('get', '/', [Controller::class, $function]);
        $method = methodFromRoute($route);

        expect(RequestBody::fromRoute($method)?->toArray())
            ->toBeNull();
    }
});

it('can create data request body', function () {
    foreach (['requestBasic', 'allCombined'] as $function) {
        $route  = new Route('get', '/', [Controller::class, $function]);
        $method = methodFromRoute($route);

        expect(RequestBody::fromRoute($method)?->toArray())
            ->toBe([
                'content' => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/RequestData',
                        ],
                    ],
                ],
            ]);

        expect(OpenApi::getTempSchemas())->toMatchArray(
            ['RequestData' => 'Vortechron\\OpenApiGenerator\\Test\\RequestData']
        );
    }
});
