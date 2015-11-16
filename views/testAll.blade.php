{!! $form->get('text') !!}<br>
{!! $form->get('email') !!}<br>
{!! $form->get('checkbox') !!}<br>
{!! $form->get('select') !!}<br>
@foreach($form->get('groupCheckbox')->group() as $checkbox)
    {!! $checkbox !!}
@endforeach

<?php


        dd($form->get('groupCheckbox')->group());