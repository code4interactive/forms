<?php

namespace Code4\Forms;

use Illuminate\Support\MessageBag;

interface FormInterface {

    /**
     * Returns messages generated during validation
     * @return MessageBag
     */
    public function messages();

}