<?php

namespace Box\Component\Console\Exception;

use RuntimeException;

/**
 * Thrown for application related issues.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 *
 * @codeCoverageIgnore
 */
class ApplicationException extends RuntimeException
{
    /**
     * Creates an exception for when a non-builder container is used.
     *
     * @return ApplicationException The new exception.
     */
    public static function notContainerBuilder()
    {
        return new self(
            'The container is not an instance of `ContainerBuilder`.'
        );
    }
}
