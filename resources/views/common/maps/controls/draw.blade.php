<?php
/** @var boolean $isAdmin */
/** @var \Illuminate\Support\Collection $floors */
?>
<nav class="route_manipulation_tools h-100 row align-items-center">
    <div class="p-2 bg-header">
        <!-- Draw controls are injected here through drawcontrols.js -->
        <div id="edit_route_draw_container">


        </div>

        <!-- Draw actions are injected here through drawcontrols.js -->
        <div id="edit_route_draw_actions_container">

        </div>

        <!-- Floor switch -->
        <div id="edit_route_draw_map_actions_container">
            @include('common.maps.controls.elements.floorswitch', ['floors' => $floors])

            @include('common.maps.controls.elements.enemyvisualtype')

            @include('common.maps.controls.elements.mapobjectgroupvisibility', ['floors' => $floors])

            @if( $isAdmin )
                @include('common.maps.controls.elements.mdtclones')
            @endif
        </div>
    </div>
</nav>