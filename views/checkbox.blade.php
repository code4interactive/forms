<input data-field-name="{{$el->getDotNotatedName()}}" type="{{$el->_type()}}" name="{{$el->name()}}" value="{{$el->getValue()}}" {!!$el->getChecked()!!} {!! $el->attributes() !!}> @if($el->fieldLabel()) {!! $el->fieldLabel() !!} @endif
<div data-field-name="{{$el->getDotNotatedName()}}" data-name="{{$el->name()}}" class="error-container"></div>