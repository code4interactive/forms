<select id="form-{{$el->id()}}" name="{{$el->name()}}" {!!$el->attrib()!!}>
    @foreach($el->getOptions() as $key => $value)
        <option value="{{$key}}" {{$el->getSelected($key)}}>{{$value}}</option>
    @endforeach
</select>