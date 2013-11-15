<?php

namespace Techworker\Nano\Tests;

use Techworker\Nano as Nano;


class Income
{
    public function getAmount()
    {
        return 0;
    }
}


class NanoTest extends \PHPUnit_Framework_TestCase
{
    public function testStatic()
    {
        $template = Nano::tpl("Hello {name}", array('name' => 'techworker'));
        $this->assertEquals("Hello techworker", $template);
        $data = array('name' => array(
            'first' => 'Benjamin',
            'last' => 'Ansbach')
        );
        $template = Nano::tpl("Hello {name:first} {name:last}", $data);
        $this->assertEquals("Hello Benjamin Ansbach", $template);
    }

    public function testData()
    {
        $nano = new Nano("{test}");
        $nano->data(array("test" => "test"));
        $nano->data(new \stdClass());
        $this->assertEquals($nano, "");
        $nano->value("test", "test");
        $nano->data(array("test" => "test"));
    }

    public function testComplex()
    {
// Complex Example
        $empOfTheMonth = new \stdClass();
        $empOfTheMonth->name = 'Benjamin Ansbach';
        $empOfTheMonth->month = 2;
        $empOfTheMonth->year = 2013;

        $data = [
            'company' => [
                'name' => 'Techworker',
                'employees' => ['Benjamin Ansbach', 'Techworker'],
                'income' => new Income(),
                'empofmonth' => $empOfTheMonth
            ]
        ];

$company = <<<EOT
Name: {company:name}
Employees: {company:employees:0} and {company:employees:1}
Income: {company:income:amount|%.2f}$
Employee of the Month: {company:empofmonth:name} in {company:empofmonth:year}/{company:empofmonth:month|%02d}
EOT;
$expected = <<<EOT
Name: Techworker
Employees: Benjamin Ansbach and Techworker
Income: 0.00$
Employee of the Month: Benjamin Ansbach in 2013/02
EOT;

        $this->assertEquals($expected, new Nano($company, $data));

// outputs:
// Name: Techworker
// Employees: Benjamin Ansbach and Techworker
// Income: 0.00$
// Employee of the Month: Benjamin Ansbach in 2013/02
    }
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDefault()
    {
        $nano = new Nano("{test}");
        $nano->def("test");
        $this->assertEquals($nano, "test");
        $nano->default("");
        $this->assertEquals($nano, "");
        $this->assertEquals($nano, "");
        $this->assertEquals($nano, "");

        $nano->default();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testDefault2()
    {
        $nano = new Nano("{test}");
        $nano->callSomething("test");
    }

    public function testFast()
    {
        $template = new Nano("Agent {number|%03d}", array('xyz' => 7), "unknown");
        $this->assertEquals("Agent unknown", $template);
    }

    public function testTemplateThenData()
    {
        $template = (new Nano("Agent {number|%03d}"))->value('number', 7);
        $this->assertEquals("Agent 007", (string)$template);

        $template = (new Nano("Agent {number|%03d}"))->data(array('number' => 7));
        $this->assertEquals("Agent 007", $template);
    }

    public function testNothingThenTemplateThenData()
    {
        $template = (new Nano())->template("Agent {number|%03d}")->value('number', 7);
        $this->assertEquals("Agent 007", $template);

        $template = (new Nano())->template("Agent {number|%03d}")->data(array('number' => 7));
        $this->assertEquals("Agent 007", $template);
    }


    public function testSprintf()
    {
        $template = Nano::tpl("Agent {number|%03d}", array('number' => 7));
        $this->assertEquals("Agent 007", $template);

        $template = Nano::tpl('${price|%.2f}', array('price' => 122));
        $this->assertEquals("$122.00", $template);
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