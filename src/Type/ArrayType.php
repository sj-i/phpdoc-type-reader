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

class ArrayType extends AtomicType
{
    /** @var Type[] */
    public array $parameter_types;

    public ArrayKeyType $key_type;

    public Type $value_type;

    public function __construct(Type $value_type, ?ArrayKeyType $key_type = null)
    {
        if (is_null($key_type)) {
            $key_type = new ArrayKeyType();
        }
        $this->key_type = $key_type;
        $this->value_type = $value_type;
        $this->parameter_types = [$value_type, $key_type];
    }
}
