{!! $form->get('text') !!}<br>
{!! $form->get('email') !!}<br>
{!! $form->get('checkbox') !!}<br>
{!! $form->get('select') !!}<br>
@foreach($form->get('groupCheckbox')->group() as $checkbox)
    {!! $checkbox !!}
@endforeach

@foreach($form->get('groupRadio')->group() as $radio)
    {!! $radio !!}
@endforeach

<?php


        dd($form->get('groupCheckbox')->group());