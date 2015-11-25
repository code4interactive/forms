<div class="switch" data-field-name="{{$el->getDotNotatedName()}}" style="padding-top: 6px;">
    <div class="onoffswitch">
        <input type="checkbox" name="{{$el->name()}}" value="{{$el->value()}}" {!!$el->checked()!!} {!! $el->attributes() !!}>
        <label class="onoffswitch-label" for="activate">
            <span class="onoffswitch-inner"></span>
            <span class="onoffswitch-switch"></span>
        </label>
    </div>
</div>