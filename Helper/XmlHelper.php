<?php

namespace Box\Component\Console\Helper;

use Symfony\Component\Console\Helper\Helper;

/**
 * Provides support for the XML file format.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class XmlHelper extends Helper
{
    /**
     * Formats the XML string for colorized output.
     *
     * @param string $xml The XML string.
     *
     * @return string The formatted XML string.
     */
    public function colorize($xml)
    {
        $xml = preg_replace(
            '/(<[^!][^>]+>)/',
            '<fg=cyan>\1</fg=cyan>',
            $xml
        );

        $xml = preg_replace(
            '/(\S+=)("[^"]+")/',
            '<fg=green>\1</fg=green><fg=yellow>\2</fg=yellow>',
            $xml
        );

        return $xml;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'xml';
    }
}
