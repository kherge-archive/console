<?php

namespace Box\Component\Console\Helper;

use Symfony\Component\Console\Helper\Helper;

/**
 * Provides support for the YAML file format.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class YamlHelper extends Helper
{
    /**
     * Formats the YAML string for colorized output.
     *
     * @param string $yaml The YAML string.
     *
     * @return string The formatted YAML string.
     */
    public function colorize($yaml)
    {
        return preg_replace(
            array(
                '/(#[^\n]+)/',
                '/^(\s*)(\w+:)/'
            ),
            array(
                '<fg=green>\1</fg=green>',
                '\1<fg=yellow>\2</fg=yellow>'
            ),
            $yaml
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'yaml';
    }
}
