<?php

namespace Box\Component\Console\Command;

use Symfony\Component\Console\Command\Command;

/**
 * Manages shared functionality for commands.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
abstract class AbstractCommand extends Command
{
    /**
     * Returns the processed and wrapped help.
     *
     * @return string The processed help.
     */
    public function getProcessedHelp()
    {
        // remove breaks for continuous lines
        $help = parent::getProcessedHelp();
        $help = preg_replace(
            '/([^\n])\n([^\n])/',
            '\1 \2',
            $help
        );

        return $this->wrap($help);
    }

    /**
     * Wraps text at a specific width while ignoring HTML tags.
     *
     * @param string  $text  The text to wrap.
     * @param integer $width The maximum width.
     *
     * @return string The wrapped text.
     *
     * @codeCoverageIgnore
     */
    protected function wrap($text, $width = 80)
    {
        $break = false;
        $current = 0;
        $ignore = false;
        $length = strlen($text);
        $wrapped = '';

        // iterate through each character in the text
        for ($i = 0; $i < $length; $i++) {
            $char = $text[$i];
            $previous = isset($text[$i - 1]) ? $text[$i - 1] : '';

            // ignore html tags
            if ('<' === $char) {
                $ignore = true;
            } elseif ('>' === $previous) {
                $ignore = false;

            // reset state if line break
            } elseif ("\n" === $previous) {
                $break = false;
                $current = 0;
            }

            // add break if appropriate
            if (!$ignore) {
                if ($width <= $current++) {
                    $break = true;
                }

                if ($break && (' ' === $char)) {
                    $break = false;
                    $current = 0;
                    $wrapped .= "\n";

                    continue;
                }
            }

            $wrapped .= $char;
        }

        return $wrapped;
    }
}
