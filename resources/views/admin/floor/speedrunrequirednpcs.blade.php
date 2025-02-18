<?php
/** @var $dungeon \App\Models\Dungeon */
?>

@section('scripts')
    @parent

    <script type="text/javascript">
        $(function () {
            $('#admin_dungeon_speedrun_required_npcs_table').DataTable({});
        });
    </script>
@endsection

<h4>{{ __('views/admin.floor.edit.speedrun_required_npcs.title') }}</h4>
<div class="float-right">
    <a href="{{ route('admin.dungeonspeedrunrequirednpc.new', ['dungeon' => $dungeon, 'floor' => $floor]) }}"
       class="btn btn-success text-white pull-right" role="button">
        <i class="fas fa-plus"></i> {{ __('views/admin.floor.edit.speedrun_required_npcs.add_npc') }}
    </a>
</div>

<table id="admin_dungeon_speedrun_required_npcs_table" class="tablesorter default_table table-striped">
    <thead>
    <tr>
        <th width="10%">{{ __('views/admin.floor.edit.speedrun_required_npcs.table_header_id') }}</th>
        <th width="70%">{{ __('views/admin.floor.edit.speedrun_required_npcs.table_header_npc') }}</th>
        <th width="10%">{{ __('views/admin.floor.edit.speedrun_required_npcs.table_header_count') }}</th>
        <th width="10%">{{ __('views/admin.floor.edit.speedrun_required_npcs.table_header_actions') }}</th>
    </tr>
    </thead>

    <tbody>
    @foreach ($floor->dungeonspeedrunrequirednpcs as $speedrunRequiredNpc)
        <tr>
            <td>{{ $speedrunRequiredNpc->id }}</td>
            <td>{{ $speedrunRequiredNpc->getDisplayText() }}</td>
            <td>{{ $speedrunRequiredNpc->count }}</td>
            <td>
                <a class="btn btn-danger"
                   href="{{
                        route('admin.dungeonspeedrunrequirednpc.delete', [
                            'dungeon' => $dungeon,
                            'floor' => $floor,
                            'dungeonspeedrunrequirednpc' => $speedrunRequiredNpc->id]
                        )
                        }}">
                    <i class="fas fa-trash"></i>&nbsp;{{ __('views/admin.floor.edit.speedrun_required_npcs.npc_delete') }}
                </a>
            </td>
        </tr>
    @endforeach
    </tbody>

</table>
