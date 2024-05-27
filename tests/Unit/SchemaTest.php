<?php

use Spatie\LaravelData\DataCollection;
use Vortechron\OpenApiGenerator\Data\OpenApi;
use Vortechron\OpenApiGenerator\Data\Schema;
use Vortechron\OpenApiGenerator\Test\ContentTypeData;
use Vortechron\OpenApiGenerator\Test\Controller;
use Vortechron\OpenApiGenerator\Test\IntEnum;
use Vortechron\OpenApiGenerator\Test\RequestData;
use Vortechron\OpenApiGenerator\Test\ReturnData;
use Vortechron\OpenApiGenerator\Test\StringEnum;

it('can create built-in schema', function () {
    foreach (['int' => 'integer', 'string' => 'string', 'float' => 'number', 'bool' => 'boolean'] as $type => $expected) {
        expect(Schema::fromDataReflection($type)->toArray())
            ->toBe([
                'type' => $expected,
            ]);
    }
});

it('can create array schema', function () {
    foreach (['collection', 'array'] as $function) {
        $reflection = new ReflectionMethod(Controller::class, $function);

        expect(Schema::fromDataReflection(DataCollection::class, $reflection)->toArray())
            ->toBe([
                'type'  => 'array',
                'items' => [
                    '$ref' => '#/components/schemas/ReturnData',
                ],
            ]);
    }
});

it('can create int enum schema', function () {
    expect(Schema::fromDataReflection(IntEnum::class)->toArray())
        ->toBe([
            'type' => 'integer',
        ]);
});

it('can create string enum schema', function () {
    expect(Schema::fromDataReflection(StringEnum::class)->toArray())
        ->toBe([
            'type' => 'string',
        ]);
});

it('can create ref data schema', function () {
    foreach ([RequestData::class, ReturnData::class, ContentTypeData::class] as $class) {
        expect(Schema::fromDataReflection($class)->toArray())
            ->toBe([
                '$ref' => '#/components/schemas/' . class_basename($class),
            ]);

        expect(OpenApi::getTempSchemas())->toMatchArray(
            [class_basename($class) => $class]
        );
    }
});

it('can create data schema', function () {
    $schema = Schema::fromDataClass(RequestData::class);
    expect($schema)->toHaveProperty('type', 'object');
    expect($schema->toArray()['properties'])->toHaveLength(13);
});
