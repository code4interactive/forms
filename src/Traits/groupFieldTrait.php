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
                $newField = \FormsFactory::makeField($this->_type, $fieldName, $fieldValue);
                $this->group->put($fieldName, $newField);
            }
        }
    }

    public function getGroup() {
        return $this->group;
    }

    public function groupChecked($value) {
        foreach($this->group as $fieldName => $field) {
            $field->checked($value);
        }
        return $this;
    }

    /**
     * Fixes rules for group fields
     */
    public function groupRules() {
        foreach($this->group as $element) {

        }
    }

}