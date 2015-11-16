<?php
namespace Code4\Forms\Traits;

use Illuminate\Support\Collection;

trait groupFieldTrait {

    protected $group = null;


    public function group($elements = null) {

        if (is_null($this->group)) {
            $this->group = new Collection();
        }

        if (is_null($elements)) {
            return $this->group;
        }

        if (is_array($elements)) {
            foreach ($elements as $fieldName => $fieldValue) {
                $this->group->put($fieldName, \FormsFactory::makeField($this->_type, $fieldName, $fieldValue));
            }
        }

    }

}