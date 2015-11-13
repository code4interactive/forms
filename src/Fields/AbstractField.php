<?php

namespace Code4\Forms\Fields;

use Code4\View\Attributes;

abstract class AbstractField implements FieldsInterface {

    protected $name;

    protected $title;

    protected $fieldName;

    protected $id;

    protected $attributes;

    /**
     * List of all properties
     * @var array
     */
    protected $properties = [];

    protected $value;

    protected $rules;
    protected $customRules;

    protected $optionKeys;

    protected $_viewNamespace = 'forms::';
    protected $_view; //Widok do załadowania
    protected $_type; //Typ elementu (np text, password) ten sam widok tylko inny typ

    /**
     * Constructs base element
     * @param $fieldName
     * @param array $data
     */
    public function __construct($fieldName, $data = []) {
        $this->fieldName = $fieldName;
        $this->parseBaseData($data);
    }

    /**
     * Parses base data passed to field. More field specific data has to be parsed in classes extending AbstractField
     * @param array $data
     */
    public function parseBaseData($data) {
        //Attributes
        if (array_key_exists('attributes', $data)) {
            $this->attributes($data['attributes']);
            unset($data['attributes']);
        }

        //Name
        if (array_key_exists('name', $data)) {
            $this->name = $data['name'];
            unset($data['name']);
        } else {
            $this->name = $this->fieldName;
        }

        //Pre set title
        $this->title($this->name);

        //ID
        if (array_key_exists('id', $data)) {
            $this->id = $data['id'];
            unset($data['id']);
        } else {
            $this->id = $this->fieldName;
        }

        //Values
        if (array_key_exists('value', $data)) {
            $this->value($data['value']);
            unset($data['value']);
        }

        //Rules
        if (array_key_exists('rules', $data)) {
            $this->rules($data['rules']);
            unset($data['rules']);
        }


        //Setting remaining properties
        //Title
        if (array_key_exists('title', $data)) {
            $this->title($data['title']);
            unset($data['title']);
        }

        foreach ($data as $propertyName => $value) {
            $this->setProperty($propertyName, $value);
        }

    }

    /**
     * Gets or sets attributes
     * @param null|array $data
     * @return Attributes
     */
    public function attributes($data = null) {
        if ($data !== null) {
            $this->attributes = new Attributes($data);
        }
        return $this->attributes;
    }

    /**
     * Sets or gets value
     * @param $value
     * @param $key
     * @return FieldsInterface|mixed $this
     */
    public function value($value = null, $key = null) {

        if ($value === null) {
            if (is_string($this->value)) {
                return $this->value;
            }
            if (is_bool($this->value)) {
                return $this->value;
            }
            if (is_array($this->value) && count($this->value) == 1) {
                return $this->value[0];
            }
            if (is_array($this->value) && count($this->value) > 1) {
                return $this->value;
            }
            return '';
        }

        // Store value as array if string or numeric
        if (is_string($value) || is_numeric($value) || is_bool($value)) {
            $this->value = [$value];
            return $this;
        }

        //If value is assoc array store only keys
        //Usually key=>value pairs represent value=>description eg. in select fields
        if (is_array($value) && $this->isAssoc($value)) {
            $this->value = [];
            foreach ($value as $k=>$v) {
                $this->value[] = $k;
            }
            return $this;
        }

        //Normal array of values (checkboxes)
        if (is_array($value)) {
            $this->value = $value;
            return $this;
        }

        //Values are in object. To extract them we need property names (keys)
        //$optionKeys is used also to extract value=>description for option field
        //So in config $optionsKeys is an 2 element array
        if (is_object($value)) {
            if (is_null($key) && count($this->optionKeys) == 2) {
                $key = $this->optionKeys[0];
            }

            if (!$key) {
                return $this;
            }

            $this->value = [];
            //iterating over $value object because it may be an collection eg. results from DB
            foreach($value as $objectKey=>$o) {
                if (is_object($o)) {
                    $this->value[] = $o->$key;
                } else {
                    //If passed object is not an iterable collection foreach will iterate over properties
                    if ($objectKey == $key) {
                        $this->value[] = $o;
                        return $this;
                    }
                }
            }
            return $this;
        }
        return $this;
    }

    /**
     * @param null $id
     * @return string|FieldsInterface
     */
    public function id($id = null) {
        if (is_null($id)) {
            return $this->id;
        } else {
            $this->id = $id;
        }
        return $this;
    }

    /**
     * @param null $name
     * @return string|FieldsInterface
     */
    public function name($name = null) {
        if (is_null($name)) {
            return $this->name;
        } else {
            $this->name = $name;
        }
        return $this;
    }

    /**
     * @param null $title
     * @return string|FieldsInterface
     */
    public function title($title = null) {
        if (is_null($title)) {
            return $this->title;
        } else {
            $this->title = $title;
        }
        return $this;
    }

    /**
     * @param null $_type
     * @return string|FieldsInterface
     */
    public function _type($_type = null) {
        if (is_null($_type)) {
            return $this->_type;
        } else {
            $this->_type = $_type;
        }
        return $this;
    }

    /**
     * @param null $rules
     * @return string|FieldsInterface
     */
    public function rules($rules = null) {
        if (is_null($rules)) {
            return $this->rules;
        } else {
            $this->rules = $rules;
        }
        return $this;
    }

    /**
     * Adds rule if it don't exist
     * @param $rule
     * @return $this
     */
    public function addRule($rule) {
        $rules = explode("|", $this->rules);
        if (array_search($rule, $rules) === false) {
            array_push($rules, $rule);
        }
        $this->rules = trim(implode("|", $rules), "|");
        return $this;
    }

    /**
     * Removes rule from field
     * @param $rule
     * @return $this
     */
    public function removeRule($rule) {
        $rules = explode("|", $this->rules);
        if (($index = array_search($rule, $rules)) !== false) {
            unset($rules[$index]);
        }
        $this->rules = trim(implode("|", $rules), "|");
        return $this;
    }

    /**
     * @param null $customRules
     * @return string|FieldsInterface
     */
    public function customRules($customRules = null) {
        if (is_null($customRules)) {
            return $this->customRules;
        } else {
            $this->customRules[] = $customRules;
        }
        return $this;
    }

    /**
     * Renders element
     * @return \Illuminate\View\View
     */
    public function render() {
        return view($this->_viewNamespace.$this->_view, ['el'=>$this])->render();
    }

    /**
     * @return string
     */
    public function __toString() {
        return $this->render();
    }

    /**
     * Sets property
     * @param string $propertyName
     * @param mixed $value
     * @return $this
     */
    public function setProperty($propertyName, $value) {
        if (property_exists($this, $propertyName)) {
            $this->$propertyName = $value;
        } else {
            $this->properties[$propertyName] = $value;
        }
        return $this;
    }

    /**
     * Gets property
     * @param string $propertyName
     * @return mixed
     */
    public function getProperty($propertyName) {
        if (property_exists($this, $propertyName)) {
            return $this->$propertyName;
        } else
        {
            return array_key_exists($propertyName, $this->properties) ? $this->properties[$propertyName] : '';
        }
    }

    /**
     * Magic methods
     * @param $method
     * @param $args
     * @return AbstractField|mixed
     */
    public function __call($method, $args) {
        if (count($args) == 0) {
            return $this->getProperty($method);
        }

        if (count($args) == 1) {
            return $this->setProperty($method, $args[0]);
        }
    }


    /**
     * Checks if passed array is associative or indexed
     * @param $arr
     * @return bool
     */
    private function isAssoc($arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }


}