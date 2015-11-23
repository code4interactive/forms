<select data-field-name="{{$el->getDotNotatedName()}}" name="{{ $el->name() }}" {!! $el->attributes() !!}>
    @foreach($el->getOptions() as $key => $value)
        <option value="{{$key}}" {{$el->getSelected($key)}}>{{$value}}</option>
    @endforeach
</select>