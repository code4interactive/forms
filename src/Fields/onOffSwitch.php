<?php

namespace Code4\Forms\Fields;

use Code4\Forms\Traits\checkedTrait;
use Code4\Forms\Traits\groupFieldTrait;

class onOffSwitch extends AbstractField {

    use checkedTrait;

    protected $_view = 'onOffSwitch';
    protected $_type = 'onOffSwitch';

    public function __construct($itemId, $config) {

        if (array_key_exists('group', $config)) {
            $this->group($config['group']);
            unset($config['group']);
        }

        parent::__construct($itemId, $config);

        if (array_key_exists('checked', $config)) {
            $this->checked = true;
        }

        $this->attributes()->add('class', 'onoffswitch-checkbox');
    }
}