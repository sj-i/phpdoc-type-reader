<?php

/**
 * This file is part of the sj-i/phpdoc-type-reader package.
 *
 * (c) sji <sji@sj-i.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PhpDocTypeReader\Type;

class GenericType extends AtomicType
{
    public AtomicType $base_type;

    /** @var Type[] */
    public array $parameter_types;

    /**
     * GenericType constructor.
     * @param AtomicType $base_type
     * @param Type[] $parameter_types
     */
    public function __construct(AtomicType $base_type, array $parameter_types)
    {
        $this->base_type = $base_type;
        $this->parameter_types = $parameter_types;
    }
}
