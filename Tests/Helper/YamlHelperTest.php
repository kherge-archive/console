<?php

namespace Helper;

use Box\Component\Console\Helper\YamlHelper;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class YamlHelperTest extends TestCase
{
    /**
     * The helper.
     *
     * @var YamlHelper
     */
    private $helper;

    /**
     * Verifies that the YAML data is correctly colored.
     */
    public function testColorize()
    {
        self::assertEquals(
            <<<EXPECTED
<fg=green># this is a comment</fg=green>
<fg=yellow>this_is_a_key:</fg=yellow>

    <fg=green># this is a key: in a comment</fg=green>
    <fg=yellow>sub_key:</fg=yellow> value <fg=green># trailing comment</fg=green>
    <fg=yellow>another:</fg=yellow> "#value"
EXPECTED
            ,
            $this->helper->colorize(
                <<<YAML
# this is a comment
this_is_a_key:

    # this is a key: in a comment
    sub_key: value # trailing comment
    another: "#value"
YAML
            )
        );
    }

    /**
     * Creates a new helper instance for testing.
     */
    protected function setUp()
    {
        $this->helper = new YamlHelper();
    }
}
