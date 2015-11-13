# Menu Package for Laravel

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Circle CI][ico-circle]](https://circleci.com/gh/code4interactive/menu/tree/master)
[![Total Downloads][ico-downloads]][link-downloads]


## Install

Via Composer

``` bash
composer require code4interactive/menu
php artisan vendor:publish --provider="Code4\Menu\MenuServiceProvider"
```

## Usage



Komponent obsługujący formularze pozwala uprościć kod HTML oraz kodowanie związane z repopulacją pól np. przy edycji.

Definiowanie formularza zaczyna się od pliku konfiguracyjnego w formacie YAML. Może od znajdować się w dowolnym miejscu w katalogu app/.

Ścieżka używana w przykładach poniżej: **app/Sciezka/Do/Pliku/editUser.yml**


```
#!yaml
email:
  type: email
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

Następnie w kontrolerze należy utworzyć instancję klasy C4Form wskazując plik konfiguracyjny:


```
#!php
<?php
$form = new C4Form('Sciezka/Do/Pliku/editUser');
//LUB
$form = new C4Form();
$form->load('Sciezka/Do/Pliku/editUser');
```

C4Form jest resolvowany przez IoC Laravela więc można go zainicjalizować przekazując go np. w parametrze metody kontrolera:


```
#!php
<?php
public function edit(C4Form $form)
{
   $form->load('Sciezka/Do/Pliku/editUser');
}
```
Mając instancjonowany obiekt klasy C4Form możemy na nim wykonywać modyfikacje wskazane w opisach elementów. Np. ustalanie opcji elementu select. Pierwsze wywołanie get() na obiekcie powoduje utworzenie i zapamiętanie wewnątrz obiektu instancji żądanego elementu. Np:

```
#!php
<?php
$form = new C4Form();
$form->load('Sciezka/Do/Pliku/editUser');
$form->get('role')->options(['wartosc1'=>'opis 1', 'wartosc2'=>'opis 2']);
```


Kolejnym krokiem jest przekazanie obiektu do widoku a następnie wyświetlanie pól w kodzie HTML:

```
#!html

<div class="form-group">
  <label class="col-lg-2 control-label">Email</label>
  <div class="col-lg-10">{!!$form->get('email')!!}</div>
</div>
```


## Plik konfiguracyjny ##

```
#!yaml
email:                         //ID pola
  id: form-email
  name: form-email
  type: email
  value: test@test.pl
  attributes:
    class: form-control
    placeholder: Adres email
```

ID pola musi być unikalne dla pliku konfiguracyjnego ponieważ służy do odwoływania się do tego pola. Pozostałe ustawienia:

 - id: ID elementu HTML (jeżeli nie zostanie ustawione użyte będzie ID pola)

 - name: Name elementu (jeżeli nie zostanie ustawione użyte będzie ID pola)

 - type: typ elementu html. Lista typów na dole strony.

 - value: domyślna wartość pola (jeżeli jest typu bool - zostanie przekonwertowane przy renderingu pola do (string) czyli "1" dla true i "0" dla false)

 - attributes: Sekcja atrybutów elementu. W ramach tej sekcji można dodawać dowolną ilość atrybutów które zostaną wyświetlone w znaczniku html w formie klucz="wartosc" lub sam "klucz". Np:

```
#!yaml
test:
  type: select
  attributes:
    class: form-control chosen-select
    multiple:
    data-placeholder: Wybierz role ...
```
zostanie wyświetlone jako:

```
#!html
<select id="test" name="test" class="form-control chosen-select" multiple="" data-placeholder="Wybierz role ...">
```





## Typy elementów HTML i ich ustawienia ##

### SELECT ###

```
#!yaml
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
```
#!php
<?php
$form->get('test-select')->options(['wartosc1'=>'opis 1', 'wartosc2'=>'opis 2']);
//LUB
$form->get('test-select')->options($object);
```
W przypadku podania opcji w formie obiektu (np. kolekcji uzyskanej z zapytania do bazy) o tym jakie pola z tej kolekcji zostaną użyte jako wartość i opis elementu <option> decyduje pole *option-keys*.

Np. pobierając z bazy listę ról możemy skorzystać z Eloquent i napisać:
```
#!php
<?php
$roles = \Roles::all();
$form->get('test-select')->options($roles)
```
Ponieważ w konfiguracji zapisaliśmy *option-keys* jako **id** i **name** to skrypt wykona:
```
#!php
<?php
@foreach($roles as $role)
    <option value="{{$role->id}}">{{$role->name}}</option>
@endforeach
```

### - value ###

Wartość pola select podobnie jak opcje można także ustawiać w kontrolerze jak i widoku.
```
#!php
<?php
$form->get('test-select')->value('wartosc1');
//LUB
$form->get('test-select')->value(['wartosc1', 'wartosc2']);
//LUB
$form->get('test-select')->value($object);
```

Bazując na poprzednim przykładzie możemy zaznaczyć które role (bazując na relacjach Eloquent) zostały już przypisane do użytkownika:
```
#!php
<?php
$user = \User::find(1);
$roles = \Roles::all();
$form->get('form-role')->options($roles)->value($user->roles)
```

W tym przykładzie $user->roles zwraca kolekcję (obiekt Collection) ról powiązanych i podobnie jak w przypadku opcji użyte jest pole *option-keys* aby pobrać odpowiednie pole z kolekcji. Ponieważ tutaj interesuje nas wyłącznie wartość nie opis pola użyty zostanie tylko pierwszy element *option-keys* czyli **id**.

Jeżeli z jakiegoś powodu do pobrania wartości potrzebny jest inny klucz można go przekazać w parametrze:
```
#!php
<?php
$form->get('form-role')->options($roles)->value($user->roles, 'id')
```


# Lista elementów #

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

[ico-version]: https://img.shields.io/packagist/v/code4interactive/menu.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/code4interactive/menu/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/g/code4interactive/menu.svg?style=flat-square
[ico-circle]: https://circleci.com/gh/code4interactive/menu/tree/master.svg?style=svg
[ico-downloads]: https://img.shields.io/packagist/dt/code4interactive/menu.svg?style=flat-square
[link-packagist]: https://packagist.org/packages/code4interactive/menu

[link-travis]: https://travis-ci.org/code4interactive/menu
[link-scrutinizer]: https://scrutinizer-ci.com/g/code4interactive/menu/code-structure
[link-downloads]: https://packagist.org/packages/code4interactive/menu
[link-author]: https://github.com/code4interactive
[link-contributors]: ../../contributors

