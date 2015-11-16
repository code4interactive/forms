<?php

namespace Code4\Forms;

class TestForm extends AbstractForm {

    protected $configPath = 'Modules/Forms/test.yml';

    protected $messages = [
        'required' => 'Pole :attribute jest wymagane'
    ];

    protected $fieldNames = [
        'name' => 'Nazwa firmy'
    ];

    protected $customRules = [
        'name' => 'customValidate'
    ];

    //Override construct to load config
    public function __construct() {
        parent::__construct();
        $this->loadFromConfigYaml(__DIR__ . '/../config/test.yml');
    }

    //Custom validation class
    public function customValidate($request) {
        return null;
    }

}