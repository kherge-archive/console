<?php

namespace Box\Component\Console\DependencyInjection\Configuration;

/**
 * Defines how a configuration interface with replaceable defaults must be implemented.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
interface DefaultsInterface
{
    /**
     * Returns the default values.
     *
     * @return array The default values.
     */
    public function getDefaultValues();

    /**
     * Returns the name of the parameter that holds the current values.
     *
     * @return string The name of the parameter.
     */
    public function getParameterName();

    /**
     * Merges new default values with the original default values.
     *
     * @param array $values The default values.
     */
    public function mergeDefaultValues(array $values);
}
