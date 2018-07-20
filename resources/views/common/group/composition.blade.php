<?php
/** @var \App\Models\DungeonRoute $model */
$racesClasses = \App\Models\CharacterRace::with(['classes:character_classes.id'])->get();
$classes = \App\Models\CharacterClass::with('iconfile')->get();
?>

@section('head')
    @parent

    <style>
        .class_icon {
            width: 24px;
            border-radius: 12px;
            background-color: #1d3131;
        }
    </style>
@endsection

@section('scripts')
    @parent

    <script>
        let _racesClasses = {!! $racesClasses !!};
        let _classDetails = {!! $classes !!};

        // Defined in dungeonroutesetup.js
        $(function () {
            $("#reload_button").bind('click', function(e){
                e.preventDefault();
                _loadDungeonRouteDefaults();
            });

            // Force population of the race boxes
            _factionChanged();

            _loadDungeonRouteDefaults();
        });

        function _loadDungeonRouteDefaults() {
                    @isset($dungeonroute)

            let faction = '{{ $dungeonroute->faction }}';
            let races = {!! $dungeonroute->races !!};
            let classes = {!! $dungeonroute->classes !!};

            let $faction = $("#faction");
            $faction.val(faction);
            // Have to manually trigger change..
            $faction.trigger('change');

            let $racesSelects = $(".raceselect select");
            let $classSelects = $(".classselect select");

            // For each party member
            for (let i = 0; i < {{ config('mpplnr.party_size') }}; i++) {
                let race = races[i];
                let $raceSelect = $($racesSelects[race.index]);
                $raceSelect.val(race.race_id);
                // Have to manually trigger change..
                $raceSelect.trigger('change');

                let drClass = classes[i];
                let $classSelect = $($classSelects[drClass.index]);
                $classSelect.val(drClass.class_id);
                // Have to manually trigger change..
                $classSelect.trigger('change');
            }

            // Refresh new values and show em properly
            $('.selectpicker').selectpicker('refresh');

            @endisset
        }
    </script>
@endsection

<h2>
    {{ __('Group composition') }}
</h2>
<div class="col-lg-12">
    <div class="col-lg-offset-5 col-lg-2">
        <div class="form-group">
            {!! Form::label('faction', __('Select faction')) !!}
            {{--array_combine because we want keys to be equal to values https://stackoverflow.com/questions/6175548/array-copy-values-to-keys-in-php--}}
            {!! Form::select('faction', array_combine(config('mpplnr.factions'), config('mpplnr.factions')), 0, ['class' => 'form-control selectpicker']) !!}
        </div>
    </div>
    @isset($dungeonroute)
    <div class="col-lg-offset-4 col-lg-1">
        <div class="form-group">
            <button id="reload_button" class="btn btn-warning">
                <i class="fa fa-undo"></i> {{ __('Reset') }}
            </button>
        </div>
    </div>
    @endisset
</div>
<?php for($i = 1; $i <= config('mpplnr.party_size'); $i++){ ?>
<div class="col-lg-2{{ $i === 1 ? ' col-lg-offset-1' : '' }}">
    <div class="form-group">
        {!! Form::label('race[]', __('Party member #' . $i)) !!}
        <select name="race[]" id="race_{{ $i }}" class="form-control selectpicker raceselect" data-id="{{$i}}">

        </select>
    </div>

    <div class="form-group">
        {{--{!! Form::select('class[]', [-1 => __('Class...')], 0,--}}
        {{--['id' => 'class_' . $i, 'class' => 'form-control selectpicker', 'data-id' => $i]) !!}--}}
        <select name="class[]" class="form-control selectpicker classselect" data-id="{{$i}}">

        </select>
    </div>
</div>
<?php } ?>

<div id="template_dropdown_icon" style="display: none;">
        <span>
            <img src="" class="class_icon"/> {text}
        </span>
</div>