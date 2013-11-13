<?php

namespace Techworker\Nano\Tests;

use Techworker\Nano as Nano;

class NanoTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $template = Nano::tpl("Hello {name}", array('name' => 'techworker'));
        $this->assertEquals("Hello techworker", $template);
    }

    public function testSprintf()
    {
        $template = Nano::tpl("Agent {number|%03d}", array('number' => 7));
        $this->assertEquals("Agent 007", $template);

        $template = Nano::tpl('${price|%.2f}', array('price' => 122));
        $this->assertEquals("$122.00", $template);
    }

    public function testDefault()
    {
        $template = Nano::tpl("Agent {number|%03d}", array('xyz' => 7), "unknown");
        $this->assertEquals("Agent unknown", $template);
    }

    public function testDeep()
    {
        $data = array(
            'root' => array(
                'level1' => array(
                    'name' => 'value'
                ),
                'level2' => array(
                    'level3' => array(
                        'price' => 122
                    )
                )
            )
        );
        $template = Nano::tpl("{root:level1:name}", $data);
        $this->assertEquals("value", $template);

        $template = Nano::tpl('${root:level2:level3:price|%.2f}', $data);
        $this->assertEquals("$122.00", $template);
    }

    public function testObject()
    {
        $data = new \stdClass();
        $data->root = array(
            'level1' => array(
                'name' => 'value'
            )
        );

        $template = Nano::tpl("{root:level1:name}", $data);
        $this->assertEquals("value", $template);
    }
}