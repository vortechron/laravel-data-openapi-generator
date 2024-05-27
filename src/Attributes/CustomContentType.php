<?php

namespace Vortechron\OpenApiGenerator\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CustomContentType
{
    public function __construct(
        /** @var string[] $type */
        public array $type
    ) {
    }
}
