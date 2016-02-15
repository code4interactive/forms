<div class="checkbox checkbox-primary">
    <input data-field-name="{{$el->getDotNotatedName()}}" type="{{$el->_type()}}" name="{{$el->name()}}" value="{{$el->value()}}" {!!$el->getChecked()!!} {!! $el->attributes() !!}>
    <label>@if($el->fieldLabel()) {!! $el->fieldLabel() !!} @endif</label>
</div>
<div data-field-name="{{$el->getDotNotatedName()}}" data-name="{{$el->name()}}" class="error-container"></div>