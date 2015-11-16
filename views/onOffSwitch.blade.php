<div class="switch">
    <div class="onoffswitch">
        <input type="checkbox" name="{{$el->name()}}" {!!$el->getChecked()!!} {!! $el->attrib() !!}>
        <label class="onoffswitch-label" for="activate">
            <span class="onoffswitch-inner"></span>
            <span class="onoffswitch-switch"></span>
        </label>
    </div>
</div>