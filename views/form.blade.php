<form action="" method="{{$el->method_simple}}" {!!$el->attrib()!!}>
<input type="hidden" name="_method" value="{{$el->method}}"/>


<input id="form-{{$el->id()}}" type="{{$el->_type()}}" name="{{$el->name()}}" value="{{$el->getValue()}}" {!!$el->attrib()!!}>