@extends('layouts.sitepage', ['showAds' => false, 'title' => __('views/admin.tools.list.title')])

@section('header-title', __('views/admin.tools.list.header'))

@section('content')
    <h3>{{ __('views/admin.tools.list.header_tools') }}</h3>
    <h4>{{ __('views/admin.tools.list.subheader_import') }}</h4>
    <div class="form-group">
        <a href="{{ route('admin.tools.npcimport') }}">{{ __('views/admin.tools.list.mass_import_npcs') }}</a>
    </div>

    <h4>{{ __('views/admin.tools.list.subheader_dungeonroute') }}</h4>
    <div class="form-group">
        <a href="{{ route('admin.tools.dungeonroute.view') }}">{{ __('views/admin.tools.list.view_dungeonroute_details') }}</a>
    </div>

    <h4>{{ __('views/admin.tools.list.subheader_mdt') }}</h4>
    <div class="form-group">
        <a href="{{ route('admin.tools.mdt.string.view') }}">{{ __('views/admin.tools.list.view_mdt_string_as_dungeonroute') }}</a>
    </div>
    <div class="form-group">
        <a href="{{ route('admin.tools.mdt.string.viewasdungeonroute') }}">{{ __('views/admin.tools.list.view_mdt_string_as_dungeonroute') }}</a>
    </div>
    <div class="form-group">
        <a href="{{ route('admin.tools.mdt.dungeonroute.viewasstring') }}">{{ __('views/admin.tools.list.view_dungeonroute_as_mdt_string') }}</a>
    </div>
    <div class="form-group">
        <a href="{{ route('admin.tools.mdt.diff') }}">{{ __('views/admin.tools.list.view_mdt_diff') }}</a>
    </div>

    <h4>{{ __('views/admin.tools.list.subheader_misc') }}</h4>
    <div class="form-group">
        <a href="{{ route('admin.tools.cache.drop') }}">{{ __('views/admin.tools.list.drop_caches') }}</a>
    </div>
    <div class="form-group">
        <a href="{{ route('admin.tools.exception.select') }}">{{ __('views/admin.tools.list.throw_an_exception') }}</a>
    </div>
    <h3>{{ __('views/admin.tools.list.subheader_actions') }}</h3>
    <div class="form-group">
        <a class="btn btn-primary" href="{{ route('admin.tools.datadump.exportdungeondata') }}">{{ __('views/admin.tools.list.export_dungeon_data') }}</a>
    </div>
    <div class="form-group">
        <a class="btn btn-primary" href="{{ route('admin.tools.datadump.exportreleases') }}">{{ __('views/admin.tools.list.export_releases') }}</a>
    </div>
@endsection