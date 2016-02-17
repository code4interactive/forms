<?php

namespace Code4\Forms\Fields;

interface FieldsInterface {

    /**
     * @param mixed $value
     * @param null|string $key
     * @return mixed
     */
    function value($value, $key = null);

    /**
     * @param null|array $data
     * @return mixed
     */
    function attributes($data = null);

    function render();

}