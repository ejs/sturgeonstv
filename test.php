<?php
    define('SIMPLE_TEST', 'file:///Library/WebServer/Private/simpletest/');
    require_once(SIMPLE_TEST . 'unit_tester.php');
    require_once(SIMPLE_TEST . 'reporter.php');

    class TestOfCore extends UnitTestCase {
        function TestOfCore() {
            $this->UnitTestCase();
        }

        function testAddition() {
            $this->assertTrue(1);
            $this->assertTrue(1+1 == 2);
        }
    }
    
    $test = &new TestOfCore();
    $test->run(new HtmlReporter());
?>

