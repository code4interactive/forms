<?php

namespace Code4\Forms;

class FormsFactory {

    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    /**
     * Makes field from class
     * @param $class
     * @param $fieldName
     * @param $data
     * @return mixed
     */
    public function makeField($class, $fieldName, $data) {
        $class = $this->findFieldClass($class);
        return new $class($fieldName, $data);
    }

    /**
     * Tests passed string if is an user class or field name. Returns found class
     * @param $field
     * @return string
     * @throws \Exception
     */
    public function findFieldClass($field) {
        //Custom class
        if (class_exists($field)) {
            return $field;
        }

        //Field class
        $class = '\Code4\Forms\Fields\\'.$field;
        if (class_exists($class)) {
            return $class;
        }

        //Default class if field class not found
        $class = \Code4\Forms\Fields\text::class;
        if (class_exists($class)) {
            return $class;
        }

        throw new \Exception('Unable to find class: '.$class);
    }

}