<?php

namespace Vortechron\OpenApiGenerator\Test;

use Spatie\LaravelData\Data;
use Vortechron\OpenApiGenerator\Attributes\CustomContentType;

#[CustomContentType(type: ['application/json', 'application/xml'])]
class ContentTypeData extends Data
{
    public function __construct(
        public string $message = 'test',
    ) {
    }

    public static function create(mixed ...$parameters): self
    {
        return new self();
    }
}
