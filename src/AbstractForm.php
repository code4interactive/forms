<?php
namespace Code4\Forms;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Validator;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\MessageBag;

class AbstractForm  {

    /**
     * @var Collection;
     */
    protected $fields;

    protected $configPath;

    /**
     * @var array
     */
    protected $config;

    protected $values;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Custom error messages
     * @var array
     */
    protected $messages = [];

    /**
     * @var MessageBag
     */
    protected $messageBag;

    /**
     * Custom rules (callback or method name)
     * @var array
     */
    protected $customRules = [];

    protected $customFieldRules = [];

    public function __construct() {
        $this->fields = new Collection();
        $this->messageBag = new MessageBag();
    }

    /**
     * Reads config from yaml file
     * @param null $configPath
     * @throws \Exception
     */
    public function loadFromConfigYaml($configPath = null) {
        if (is_null($configPath)) {
            $configPath = $this->configPath;
        }
        if (is_null($configPath) || !is_string($configPath) || $configPath == '') {
            throw new \Exception('Config path should be string');
        }
        $configPath = rtrim($configPath, '/');
        $yaml = \File::get($configPath);
        $this->config = Yaml::parse($yaml);
        $this->loadFromConfigArray($this->config);
    }

    /**
     * Loads fields from array
     * @param array $config
     * @throws \Exception
     */
    public function loadFromConfigArray($config) {
        if (!is_array($config)) {
            throw new \Exception('Provided config is not an array');
        }

        foreach($config as $fieldName => $field) {
            //check for field type or default to text
            $type = array_key_exists('type', $field) ? $field['type'] : 'text';

            //Constructs or creates fields
            $this->$type($fieldName, $field);
        }
    }

    /**
     * Gets field from collection
     * @param $fieldName
     * @return Fields\FieldsInterface
     */
    public function get($fieldName) {
        return $this->fields->get($fieldName);
    }

    /**
     * Gets title of field
     * @param $fieldName
     */
    public function title($fieldName) {
        return $this->get($fieldName)->title();
    }

    /**
     * Returns all fields
     * @return Collection
     */
    public function all() {
        return $this->fields;
    }

    /**
     * Sets values for all fields from passed array or object (eg. model).
     * @param array|Arrayable $values
     * @return null
     */
    public function values($values) {
        if (is_object($values) && $values instanceof Arrayable) {
            $values = $values->toArray();
        }
        if (!is_array($values)) {
            return null;
        }
        foreach($this->fields as $fieldName => $field) {
            $name = $field->name();
            if (array_key_exists($name, $values)) {
                $this->fields[$fieldName]->value($values[$name]);
            }
        }
    }

    /**
     * Validates all fields with Laravel and custom rules
     * @param Request $request
     * @param array $passedRules
     * @return bool
     */
    public function validate(Request $request, $passedRules = []) {

        $this->request = $request;

        //Collect rules from fields
        list($rules, $customFieldRules) = $this->collectRules();

        //Combine passed rules with those extracted from fields
        $rules = array_merge($rules, $passedRules);

        //Make validator
        $validator = Validator::make($request->all(), $rules, $this->messages);

        //Set fields names for messages
        $validator->setAttributeNames($this->getAttributeNames());

        //Run validation
        $validator->passes();

        //Get messages
        $this->messageBag = $validator->messages();

        //Do custom validation in fields ($field->customRules property)
        $customFieldRules = array_merge_recursive($customFieldRules, $this->customFieldRules);
        foreach($customFieldRules as $fieldName => $rule) {
            foreach($rule as $cr) {
                if ($message = $this->callCustomRules($cr, $request)) {
                    $this->messageBag->add($fieldName, $message);
                }
            }
        }

        //Do custom validation in form class ($form->customRules property)
        foreach ($this->customRules as $fieldName => $cr) {
            if ($message = $this->callCustomRules($cr, $request)) {
                $this->messageBag->add($fieldName, $message);
            }
        }

        return $this->passed();
    }

    /**
     * Run validate() before this
     * @return bool
     */
    public function passed() {
        return count($this->messageBag) === 0;
    }

    /**
     * Returns messages generated during validation
     * @return MessageBag
     */
    public function messages() {
        return $this->messageBag;
    }

    /**
     * Returns response for browser
     */
    public function response($redirect = null) {
        if ($this->request->ajax() || $this->request->wantsJson()) {
            return new JsonResponse(['formErrors' =>$this->messageBag->toArray()], 422);
        }
        return \Redirect::to($redirect)->withErrors($this->messageBag->toArray());
    }


    /**
     * Calls custom rule which can be an callback or private method
     * @param $rule
     * @param $request
     * @return mixed
     */
    protected function callCustomRules($rule, $request) {
        if (is_callable($rule))
        {
            if ($message = call_user_func($rule, $request))
            {
                return $message;
            }
        }
        else if (method_exists($this, $rule))
        {
            if ($message = call_user_func( array($this, $rule), $request ))
            {
                return $message;
            }
        }
    }

    /**
     * Gets titles of attributes from fields (used in error messages in validation)
     * @return array
     */
    public function getAttributeNames() {
        $attributeNames = [];
        foreach($this->fields as $field) {
            $attributeNames[$field->name()] = $field->title();
        }
        return $attributeNames;
    }


    /**
     * Add custom callable rule to field
     * @param string $fieldName
     * @param $rule
     * @return AbstractForm
     */
    public function customFieldRule($fieldName, $rule) {
        $this->customFieldRules[$fieldName][] = $rule;
        return $this;
    }


    /**
     * Collects rules from all fields
     * @return array
     */
    public function collectRules() {
        $rules = [];
        $customRules = [];
        foreach($this->fields as $field) {

            $fieldRule = $field->getRulesForValidator();
            $rules = array_merge($rules, $fieldRule);

            if ($field->customRules()) {
                $customRules[$field->name()] = $field->customRules();
            }
        }
        return [$rules, $customRules];
    }

    /**
     * Makes new field from passed class
     * @param string $fieldName
     * @param string $fieldClass
     * @param array $args
     * @return Fields\FieldsInterface
     */
    public function make($fieldName, $fieldClass, $args = []) {
        $this->fields->put($fieldName, \FormsFactory::makeField($fieldClass, $fieldName, $args));
        return $this->get($fieldName);
    }

    /**
     * Calls requested element from fields array or creates it
     * @param string $method
     * @param mixed $args
     * @return mixed|Fields\FieldsInterface
     * @throws \Exception
     */
    public function __call($method, $args) {

        if (count($args) > 0) {
            $fieldName = $args[0];

            //Looking for existing field by its name
            if ($this->fields->has($fieldName)) {
                return $this->get($fieldName);
            }

            //No field? Create one!

            //If there is no second argument (no initial data for field)
            $data = array_key_exists(1, $args) ? $args[1] : [];

            //Method used defines field type
            $type = $method;

            return $this->make($fieldName, $type, $data);
        }
    }

}