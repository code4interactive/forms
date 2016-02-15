<?php

namespace Code4\Forms\Test;

abstract class TestCase extends \PHPUnit_Framework_TestCase {

    protected $abstractField = [
        'type' => 'text',
        'name' => '',
        'id' => '',
        'value' => 'config_value',
        'attributes' => [
            'class' => 'myClass',
            'data-placeholder' => 'My Field'
        ]
    ];
}
