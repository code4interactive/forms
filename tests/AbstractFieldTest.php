<?php

namespace Code4\Forms\Test;

use Illuminate\Support\Collection;

class AbstractFieldTest extends TestCase {

    protected $_object;

    public function setUp() {
        $stub = $this->getMockBuilder('Code4\Forms\Fields\AbstractField')
            ->setConstructorArgs(['testField', $this->abstractField])
            ->getMockForAbstractClass();
        $this->_object = $stub;
    }

    public function testAttributesMethodReturnsDataPassedInConstructor() {
        $this->assertEquals('myClass', $this->_object->attributes()->get('class'));
    }

    public function testReplacingAttributes() {
        $this->_object->attributes([]);
        $this->assertEquals('', $this->_object->attributes()->get('class'));
    }

    public function testSettingFieldValueFromConfig() {
        $this->assertEquals('config_value', $this->_object->value());
    }

    public function testSettingFieldValueFromString() {
        $this->_object->value('test_value');
        $this->assertEquals('test_value', $this->_object->value());
    }

    public function testSettingFieldValueFromNumeric() {
        $this->_object->value(12);
        $this->assertEquals(12, $this->_object->value());
    }

    public function testSettingFieldValueFromBool() {
        $this->_object->value(true);
        $this->assertSame((string)true, $this->_object->value());
        $this->_object->value(false);
        $this->assertSame((string)false, $this->_object->value());
    }

    public function testSettingFieldValueFromArray() {
        $this->_object->value(['test_value','test_value2']);
        $this->assertEquals(['test_value','test_value2'], $this->_object->value());
    }

    public function testSettingFieldValueFromAssocArray() {
        $this->_object->value(['test_value'=>'Test Description','test_value2'=>'Test Description']);
        $this->assertEquals(['test_value','test_value2'], $this->_object->value());
    }

    public function testSettingFieldValueFromObject() {
        $genericObject = new \stdClass();
        $genericObject->testPropertyKey = 'test_value';

        $this->_object->value($genericObject, 'testPropertyKey');
        $this->assertEquals('test_value', $this->_object->value());
    }

    public function testSettingFieldValueFromCollectionOfObjects() {
        $genericObject = new \stdClass();
        $genericObject->testPropertyKey = 'test_value';
        $genericObject2 = new \stdClass();
        $genericObject2->testPropertyKey = 'test_value2';

        $this->_object->value(new Collection([$genericObject, $genericObject2]), 'testPropertyKey');
        $this->assertEquals(['test_value','test_value2'], $this->_object->value());
    }

    public function testAddingRules() {
        $this->_object->addRule("required");
        $this->assertEquals("required", $this->_object->rules());
    }

    public function testRemoveRules() {
        $this->_object->rules("required|min:10");
        $this->_object->removeRule("required");
        $this->assertEquals("min:10", $this->_object->rules());
    }

    public function testProperties() {
        $this->_object->setProperty('property1', 'value1');
        $this->_object->property2('value2');

        $this->assertEquals("value1", $this->_object->getProperty('property1'));
        $this->assertEquals("value2", $this->_object->property2());
    }

}