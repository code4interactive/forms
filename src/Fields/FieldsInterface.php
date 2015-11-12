<?php

namespace Code4\Forms\Fields;

interface FieldsInterface {

    /**
     * @param null|mixed $value
     * @param null|string $key
     * @return mixed
     */
    function value($value = null, $key = null);

    /**
     * @param null|array $data
     * @return mixed
     */
    function attributes($data = null);

    function render();

}