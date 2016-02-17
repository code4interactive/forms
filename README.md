# Form Package for Laravel

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Circle CI][ico-circle]](https://circleci.com/gh/code4interactive/forms/tree/master)
[![Total Downloads][ico-downloads]][link-downloads]


## Install

Via Composer

``` bash
composer require code4interactive/forms
```

## Usage


Komponent obsługujący formularze pozwala uprościć kod HTML oraz kodowanie związane z repopulacją pól np. przy edycji.

Definiowanie formularza zaczyna się od pliku konfiguracyjnego w formacie YAML.

Ścieżka używana w przykładach poniżej: **app/Sciezka/Do/Pliku/editUser.yml**


```#!yaml
email:
  type: email
  roles: required
  attributes:
    class: form-control
    placeholder: Adres email

password:
  type: password
  attributes:
    class: form-control
    placeholder: Hasło
    autocomplete: off

role:
  name: role[]
  type: select
  attributes:
    class: form-control chosen-select m-b
    multiple:
    data-placeholder: Wybierz role ...
  options:
    el1: opis1
    el2: opis2
    el3: opis3
  option-keys: [id, name]
```

### Obiekt ###

Formularz tworzymy przez stworzenie własnej klasy dziedziczącej po AbstractForm. 
Pozwala to na konfigurację dodatkowych opcji związanych nazewnictwem pól, konfiguracją własnych walidatorów itp. 


``` php

<?php

use Code4\Forms\AbstractForm;

class CreateUserForm extends AbstractForm {

    public function __construct() {
        parent::__construct();
        $this->loadFromConfigYaml('Sciezka/Do/Pliku/editUser.yml');
    }

}

```

Dla prostych formularzy, możemy utworzyć 'inline' obiekt z AbstractForm:

``` php

<?php
  $form = new AbstractForm();
  $form->loadFromConfigYaml('Sciezka/Do/Pliku/editUser.yml');
  
  if (!$form->validate($request)) {
    return $form->response();
  }
  
```


Dla bardzo prostych formularzy z polami utworzonymi recznie w widoku nie trzeba tworzyć pliku konfiguracyjnego:

``` php

<?php
  $form = new AbstractForm();
  if (!$form->validate($request, ['email' => 'required', 'password' => 'required'])) {
    return $form->response();
  }
  
```



### Widok ###
``` html

<div class="form-group">
  <label class="col-lg-2 control-label">Email</label>
  <div class="col-lg-10">{!!$form->get('email')!!}</div>
</div>
```


## Plik konfiguracyjny ##

``` yaml
email:
  id: form-email
  name: form-email
  label: Podaj e-mail
  title: E-mail
  fieldLabel: tekst
  type: email
  value: test@test.pl
  rules: required|min:10
  attributes:
    class: form-control
    placeholder: Adres email
```

ID pola musi być unikalne dla pliku konfiguracyjnego ponieważ służy do odwoływania się do tego pola. Pozostałe ustawienia:

 - id: ID elementu HTML 

 - name: Name elementu (jeżeli nie zostanie ustawione użyte będzie ID pola)

 - type: typ elementu html. Lista typów na dole strony.

 - value: domyślna wartość pola (jeżeli jest typu bool - zostanie przekonwertowane przy renderingu pola do (string) czyli "1" dla true i "0" dla false)

 - attributes: Sekcja atrybutów elementu. W ramach tej sekcji można dodawać dowolną ilość atrybutów które zostaną wyświetlone w znaczniku html w formie klucz="wartosc" lub sam "klucz". Np:
 
 - label: Nie obowiązkowe. Przy automatycznym generowaniu formularzy można wykożystać do wyświetlania opisu pola
  
 - title: Nie obowiązkowe. Nazwa pola przekazywana walidatorowi do wyświeltania komunikatów błędów
  
 - fieldLabel: Nie obowiązkowe. Wykorzystywane przy wyświetlaniu dodatkowych opisów obok pól typu checkbox 

``` yaml
test:
  type: select
  attributes:
    class: form-control chosen-select
    multiple:
    data-placeholder: Wybierz role ...
```
zostanie wyświetlone jako:

```html
<select name="test" class="form-control chosen-select" multiple="" data-placeholder="Wybierz role ...">
```





## Typy elementów HTML i ich ustawienia ##

### SELECT ###

``` yaml
test-select:
  type: select
  attributes:
    class: form-control chosen-select m-b
    multiple:
    data-placeholder: Wybierz role ...
  options:
    el1: opis1
    el2: opis2
    el3: opis3
  option-keys: [id, name]
  value: [el1,el2]
```

**Dodatkowe atrybuty:**
- options: lista opcji w formacie "wartość: opis"

- options-keys: para kluczy (koniecznie tablica 2-elementowa) wg. których mają być przeszukiwane wartości opcji jeżeli zostały przesłane w formie obiektu (opis poniżej)

- value: domyślna wartość elementu pola. W przypadku multiple zapisana w postaci tablicy jednowymiarowej.

### - options ###
Jeżeli opcje pola generowane są skryptem można je ustawić zarówno w kontrolerze jak i w widoku.
``` php
<?php
$form->get('test-select')->options(['wartosc1'=>'opis 1', 'wartosc2'=>'opis 2']);
//LUB
$form->get('test-select')->options($object);
```
W przypadku podania opcji w formie obiektu (np. kolekcji uzyskanej z zapytania do bazy) o tym jakie pola z tej kolekcji zostaną użyte jako wartość i opis elementu <option> decyduje pole *option-keys*.

Np. pobierając z bazy listę ról możemy skorzystać z Eloquent i napisać:
``` php
<?php
$roles = \Roles::all();
$form->get('test-select')->options($roles)
```
Ponieważ w konfiguracji zapisaliśmy *option-keys* jako **id** i **name** to skrypt wykona:
```php
<?php
@foreach($roles as $role)
    <option value="{{$role->id}}">{{$role->name}}</option>
@endforeach
```

### - value ###

Wartość pola select podobnie jak opcje można także ustawiać w kontrolerze jak i widoku.
``` php
<?php
$form->get('test-select')->value('wartosc1');
//LUB
$form->get('test-select')->value(['wartosc1', 'wartosc2']);
//LUB
$form->get('test-select')->value($object);
```

Bazując na poprzednim przykładzie możemy zaznaczyć które role (bazując na relacjach Eloquent) zostały już przypisane do użytkownika:
```php
<?php
$user = \User::find(1);
$roles = \Roles::all();
$form->get('form-role')->options($roles)->value($user->roles)
```

W tym przykładzie $user->roles zwraca kolekcję (obiekt Collection) ról powiązanych i podobnie jak w przypadku opcji użyte jest pole *option-keys* aby pobrać odpowiednie pole z kolekcji. Ponieważ tutaj interesuje nas wyłącznie wartość nie opis pola użyty zostanie tylko pierwszy element *option-keys* czyli **id**.

Jeżeli z jakiegoś powodu do pobrania wartości potrzebny jest inny klucz można go przekazać w parametrze:
``` php
<?php
$form->get('form-role')->options($roles)->value($user->roles, 'id')
```

# Lista elementów #

```yaml

field1: 
    type: password
field5:
    type: onOffSwitch
    title: Field title
field10:
    type: separator
field11:
    type: header
    title: Header
field12:
    type: htmlTag
    content: Tag content
    tag: div
    attributes:
        class: text-center

```

* [OnOffSwitch](C4Form/OnOffSwitch)

## Testing

``` bash
composer test
```

## Credits

- [:author_name][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/code4interactive/forms.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/code4interactive/forms/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/code4interactive/forms.svg?style=flat-square
[ico-circle]: https://circleci.com/gh/code4interactive/forms/tree/master.svg?style=svg
[ico-downloads]: https://img.shields.io/packagist/dt/code4interactive/forms.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/code4interactive/forms

[link-travis]: https://travis-ci.org/code4interactive/forms
[link-scrutinizer]: https://scrutinizer-ci.com/g/code4interactive/forms/code-structure
[link-downloads]: https://packagist.org/packages/code4interactive/forms
[link-author]: https://github.com/code4interactive
[link-contributors]: ../../contributors

