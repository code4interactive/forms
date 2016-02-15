@foreach ($el->getGroup() as $name => $value)
    {!! $value->render() !!}
@endforeach