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
        $this->assertSame(true, $this->_object->value());
        $this->_object->value(false);
        $this->assertSame(false, $this->_object->value());
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

}