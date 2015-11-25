<?php
namespace Code4\Forms\Traits;

trait checkedTrait
{
    protected $checked = false;

    /**
     * Sprawdza czy pole powinno być zaznaczone
     * @param string|array $value
     * @param string $oKey
     * @param string $oName ????
     * @return $this
     */
    public function checked($value = null, $oKey = null, $oName = null) {

        if (is_null($value)) {
            return $this->getChecked();
        }

        //Checkboxy $this->value powinny mieć wyłącznie jako "string"
        if (is_array($this->value)) {
            $this->value = $this->value[0];
        }

        $this->checked = false;

        //Sprawdzamy typ przekazanych danych.
        if (is_string($value)) {
            $this->checked = $this->value == $value;
            return $this;
        }

        if (is_array($value)) {
            if (isAssoc($value)) {
                //W przypadku tablicy asocjacyjnej ($key=>$val) jeśli $val jest typu bool to znaczy że $key == $this->value
                if (is_bool(array_values($value)[0])) {
                    if (array_key_exists($this->value, $value)) {
                        $this->checked = $value[$this->value];
                        return $this;
                    }
                } else {
                    //W przeciwnym przypdaku uznajemy że tablica przechowuje pary klucz => wartosc odpowiadające $this->name => $this->value
                    if (array_key_exists($this->name, $value)) {
                        $this->checked = $value[$this->name] == $this->value;
                        return $this;
                    }
                }
            } else {
                $this->checked = in_array($this->value, $value);
                return $this;
            }

            return $this;
        }

        //Jeżeli jest obiektem to obowiązkowo należy podać drugi parametr jakim jest nazwa pola
        if (is_object($value) && $oKey && $oName) {
            foreach($value as $v)
            {
                if (property_exists($v, $oKey))
                {
                    $prop = $v->$oKey;
                    if (is_bool($prop))
                    {
                        $this->checked = $prop;
                    } else
                    {
                        $this->checked = $prop == $this->value;
                    }
                }
            }
            return $this;
        }


        if (is_object($value) && $oKey) {
            if (property_exists($value, $oKey))
            {
                $prop = $value->$oKey;
                if (is_bool($prop))
                {
                    $this->checked = $prop;
                } else
                {
                    $this->checked = $prop == $this->value;
                }
            }
        }

        if (is_bool($value)) {
            $this->checked = $value;
            return $this;
        }
        return $this;
    }

    /**
     * Pobiera atrybut checked dla html
     * @return string
     */
    public function getChecked() {
        return $this->checked ? 'checked' : '';
    }

    /**
     * Dla pól checkbox value powinno ustawiać checked na true lub false a nie zastepowac warość
     * @param null $value
     * @param null $key
     * @return null|string
     */
    public function value($value = null, $key = null) {
        if (is_null($value)) {
            return parent::value($value, $key);
        }
        $this->checked($value);
    }

    /**
     * Jeżeli chcemy zastąpić wartość
     * @param null $value
     * @param null $key
     */
    public function setValue($value = null, $key = null) {
        parent::value($value, $key);
    }

}