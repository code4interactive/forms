<?php

namespace Code4\Forms\Fields;

class select extends AbstractField {

    protected $_view = 'select';
    protected $_type = 'select';

    protected $options;
    protected $optionKeys;

    public function __construct($itemId, $config) {

        parent::__construct($itemId, $config);

        if (array_key_exists('options', $config)) {
            $this->options = $config['options'];
        }

        if (array_key_exists('option-keys', $config)) {
            $this->optionKeys = $config['option-keys'];
        }

        if (is_string($this->value)) {
            $this->value = [$this->value];
        }
    }

    /**
     * Sprawdzanie typu przesłanych opcji selecta
     * @param $options
     * @return $this|select
     */
    public function options($options) {

        switch(gettype($options)) {
            case "object":
                return $this->setOptionsFromObject($options);
                break;
            case "array":
                return $this->setOptionsFromArray($options);
                break;
            default:
                return $this;
        }
    }

    /**
     * Ustawiamy opcje selecta z pomocą dostarczonej tablicy
     * @param $array
     * @return $this
     */
    public function setOptionsFromArray($array) {
        $this->options = $array;
        return $this;
    }

    /**
     * Jeżeli przesłany został objekt (np. model Eloquent) to używając optionKeys wybieramy tylko potrzebne dane
     * @param $object
     * @return $this
     * @throws \Exception
     */
    public function setOptionsFromObject($object) {

        if (!is_array($this->optionKeys) || count($this->optionKeys) != 2) {
            throw new \Exception('Błędna opcja optionKeys dla elementu '.$this->id);
        }

        $value = $this->optionKeys[0];
        $text = $this->optionKeys[1];

        $options = [];

        foreach($object as $el) {
            $options[$el->$value] = $el->$text;
        }

        $this->options = $options;

        return $this;
    }

    /**
     * Metoda sprawdza czy w zapisanych wartościach znajduje się szukana
     * @param $val
     * @return string
     */
    public function getSelected($val) {
        if (is_array($this->value)) {
            if (in_array($val, $this->value)) {
                return "selected";
            }
        }
        return '';
    }


    /**
     * Ponieważ atrybut ma taką samą nazwę jak funkcja która ustawia opcje zwracamy je do widoku tą funkcją
     * @return mixed
     */
    public function getOptions() {
        return $this->options;
    }

}