<?php
/**
 * @var $dungeon \App\Models\Dungeon
 * @var $dungeonroutes \App\Models\DungeonRoute[]|\Illuminate\Support\Collection
 */
$title = isset($title) ? $title : sprintf('%s routes', $dungeon->name);
$affixgroup = isset($affixgroup) ? $affixgroup : null;
?>
@extends('layouts.sitepage', ['rootClass' => 'discover col-xl-10 offset-xl-1', 'breadcrumbsParams' => [$dungeon], 'title' => $title])

@include('common.general.inline', ['path' => 'dungeonroute/discover/discover',
        'options' =>  [
        ]
])

@section('content')
    @include('dungeonroute.discover.wallpaper', ['dungeon' => $dungeon])

    @include('dungeonroute.discover.panel', [
        'title' => $title,
        'dungeonroutes' => $dungeonroutes,
        'affixgroup' => $affixgroup,
    ])

    @include('common.dungeonroute.search.loadmore', ['category' => $category, 'dungeon' => $dungeon, 'routeListContainerSelector' => '#category_route_list'])
@endsection