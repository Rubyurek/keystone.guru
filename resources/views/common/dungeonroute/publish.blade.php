<?php
/** @var $allPublishedStates \Illuminate\Support\Collection|\App\Models\PublishedState[] */
/** @var $dungeonroute \App\Models\DungeonRoute */
$publishStates          = $allPublishedStates->pluck('name');
$publishStatesAvailable = \App\Models\PublishedState::getAvailablePublishedStates($dungeonroute, Auth::user());
?>

@include('common.general.inline', ['path' => 'common/dungeonroute/publish', 'options' => [
    'publishSelector' => '#map_route_publish',
    'publishStates' => $publishStates,
    'publishStatesAvailable' => $publishStatesAvailable,
    'publishStateSelected' => $dungeonroute->publishedstate->name
]])

{!! Form::select('map_route_publish', [], 1, ['id' => 'map_route_publish', 'class' => 'form-control selectpicker', 'size' => count($publishStates)]) !!}
