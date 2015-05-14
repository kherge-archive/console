<?php

namespace Box\Component\Console\Exception;

use RuntimeException;

/**
 * Thrown for definition related issues.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 *
 * @codeCoverageIgnore
 */
class DefinitionException extends RuntimeException
{
    /**
     * Creates a new exception for a definition that does not exist.
     *
     * @param string $id The definition identifier.
     *
     * @return DefinitionException The new exception.
     */
    public static function notExist($id)
    {
        return new self(
            "The definition \"$id\" does not exist."
        );
    }
}
