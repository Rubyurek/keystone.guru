<?php
/** @var \Illuminate\Support\Collection $floors */
?>
<div class="row no-gutters">
    <div class="col btn-group dropright">
        <button type="button"
                class="btn btn-accent dropdown-toggle {{ $floors->count() > 1 ? '' : 'disabled' }}"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-dungeon"></i>
            <span class="map_controls_element_label_toggle" style="display: none;">
                {{ __('views/common.maps.controls.elements.floor_switch.switch_floors') }}
            </span>
        </button>
        <div id="map_floor_selection_dropdown" class="dropdown-menu">
            <a class="dropdown-item disabled">
                {{ __('views/common.maps.controls.elements.floor_switch.floors') }}
            </a>
            @foreach($floors as $floor)
                <a class="dropdown-item {{ $floor->id === $selectedFloorId ? 'active' : '' }}"
                   data-value="{{ $floor->id }}">{{ __($floor->name) }}</a>
            @endforeach
        </div>
    </div>
</div>
