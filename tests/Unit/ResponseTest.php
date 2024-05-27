<?php

use Illuminate\Routing\Route;
use Vortechron\OpenApiGenerator\Data\OpenApi;
use Vortechron\OpenApiGenerator\Data\Response;
use Vortechron\OpenApiGenerator\Test\Controller;

it('can create data response', function () {
    foreach (['basic', 'intParameter', 'stringParameter', 'modelParameter', 'requestBasic', 'allCombined'] as $function) {
        $route  = new Route('get', '/', [Controller::class, $function]);
        $method = methodFromRoute($route);

        expect(Response::fromRoute($method)->toArray())
            ->toBe([
                'description' => $function,
                'content'     => [
                    'application/json' => [
                        'schema' => [
                            '$ref' => '#/components/schemas/ReturnData',
                        ],
                    ],
                ],
            ]);
    }

    expect(OpenApi::getTempSchemas())->toMatchArray(
        ['ReturnData' => 'Vortechron\\OpenApiGenerator\\Test\\ReturnData']
    );
});

it('can create collection response', function () {
    foreach (['array', 'collection'] as $function) {
        $route  = new Route('get', '/', [Controller::class, $function]);
        $method = methodFromRoute($route);

        expect(Response::fromRoute($method)->toArray())
            ->toBe([
                'description' => $function,
                'content'     => [
                    'application/json' => [
                        'schema' => [
                            'type'  => 'array',
                            'items' => [
                                '$ref' => '#/components/schemas/ReturnData',
                            ],
                        ],
                    ],
                ],
            ]);
    }

    expect(OpenApi::getTempSchemas())->toMatchArray(
        ['ReturnData' => 'Vortechron\\OpenApiGenerator\\Test\\ReturnData']
    );
});

it('cannot create incomplete collection response', function () {
    foreach (['arrayIncompletePath', 'arrayFail', 'collectionIncompletePath', 'collectionFail'] as $function) {
        $route  = new Route('get', '/', [Controller::class, $function]);
        $method = methodFromRoute($route);

        expect(fn () => Response::fromRoute($method)->toArray())
            ->toThrow(RuntimeException::class);
    }
});
