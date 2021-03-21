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

class ObjectType extends AtomicType
{
    public string $class_name;

    /**
     * ObjectType constructor.
     * @param string $class_name
     */
    public function __construct(string $class_name)
    {
        $this->class_name = $class_name;
    }
}
