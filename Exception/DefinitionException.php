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

    /**
     * Creates a new exception for a tagged service that is abstract.
     *
     * @param string $id  The service identifier.
     * @param string $tag The tag name.
     *
     * @return DefinitionException
     */
    public static function taggedServiceAbstract($id, $tag)
    {
        return new self(
            "The service \"$id\" for \"$tag\" is abstract."
        );
    }

    /**
     * Creates a new exception for a tagged service that is not public.
     *
     * @param string $id  The service identifier.
     * @param string $tag The tag name.
     *
     * @return DefinitionException
     */
    public static function taggedServiceNotPublic($id, $tag)
    {
        return new self(
            "The service \"$id\" for \"$tag\" is not public."
        );
    }

    /**
     * Creates a new exception for a tagged service that is not a subclass.
     *
     * @param string $id    The service identifier.
     * @param string $tag   The tag name.
     * @param string $class The expected parent class.
     *
     * @return DefinitionException
     */
    public static function taggedServiceNotSubclass($id, $tag, $class)
    {
        return new self(
            "The service \"$id\" is not a subclass of \"$class\" for \"$tag\"."
        );
    }
}
