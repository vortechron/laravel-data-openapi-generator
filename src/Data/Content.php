<?php

namespace Vortechron\OpenApiGenerator\Data;

use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Wrapping\WrapExecutionType;
use Vortechron\OpenApiGenerator\Attributes\CustomContentType;

class Content extends Data
{
    public function __construct(
        /** @var string[] */
        protected array $types,
        public Schema $schema,
    ) {
    }

    public static function fromReflection(ReflectionNamedType $type, ReflectionMethod|ReflectionFunction $method): self
    {
        return new self(
            types: self::typesFromReflection($type),
            schema: Schema::fromDataReflection($type, $method),
        );
    }

    public static function fromClass(string $class, ReflectionMethod|ReflectionFunction $method): self
    {
        $type = $method->getReturnType();

        return new self(
            types: self::typesFromReflection($type),
            schema: Schema::fromDataReflection($class),
        );
    }

    /**
     * @return array<int|string,mixed>
     */
    public function transform(
        bool $transformValues = true,
        WrapExecutionType $wrapExecutionType = WrapExecutionType::Disabled,
        bool $mapPropertyNames = true,
    ): array {
        return collect($this->types)->mapWithKeys(
            fn (string $content_type) => [$content_type => parent::transform($transformValues, $wrapExecutionType, $mapPropertyNames)]
        )->toArray();
    }

    /**
     * @return string[]
     */
    protected static function typesFromReflection(ReflectionNamedType|ReflectionType|null $type): array
    {
        if ($type instanceof ReflectionNamedType && !$type->isBuiltin()) {
            /** @var class-string $name */
            $name       = $type->getName();
            $reflection = new ReflectionClass($name);

            $custom_content_attribute = $reflection->getAttributes(CustomContentType::class);

            if (count($custom_content_attribute) > 0) {
                return $custom_content_attribute[0]->getArguments()['type'];
            }
        }

        return ['application/json'];
    }
}
