<?php
/**
 * @var \App\User $user
 * @var \App\Logic\MapContext\MapContext $mapContext
 * @var \App\Models\Dungeon $dungeon
 * @var \App\Models\Mapping\MappingVersion $mappingVersion
 * @var \App\Models\DungeonRoute|null $dungeonroute
 * @var \App\Models\LiveSession|null $livesession
 * @var array $show
 * @var bool $adFree
 */

$user             = Auth::user();
$isAdmin          = isset($admin) && $admin;
$embed            = isset($embed) && $embed;
$edit             = isset($edit) && $edit;
$mapClasses       = $mapClasses ?? '';
$dungeonroute     = $dungeonroute ?? null;
$livesession      = $livesession ?? null;
$show['controls'] = $show['controls'] ?? [];

// Set the key to 'sandbox' if sandbox mode is enabled
$sandboxMode                      = isset($sandboxMode) && $sandboxMode;
$enemyVisualType                  = $_COOKIE['enemy_display_type'] ?? 'enemy_portrait';
$unkilledEnemyOpacity             = $_COOKIE['map_unkilled_enemy_opacity'] ?? '50';
$unkilledImportantEnemyOpacity    = $_COOKIE['map_unkilled_important_enemy_opacity'] ?? '80';
$defaultEnemyAggressivenessBorder = (int)($_COOKIE['map_enemy_aggressiveness_border'] ?? 0);

// Allow echo to be overridden
$echo           = $echo ?? Auth::check() && !$sandboxMode;
$zoomToContents = $zoomToContents ?? false;

// Show ads or not
$showAds = $showAds ?? true;
// If this is an embedded route, do not show ads
if ($embed || optional($dungeonroute)->demo === 1) {
    $showAds = false;
}
// No UI on the map
$noUI            = isset($noUI) && $noUI;
$gestureHandling = isset($gestureHandling) && $gestureHandling;
// Default zoom for the map
$defaultZoom = $defaultZoom ?? 2;
// By default hidden elements
$hiddenMapObjectGroups = $hiddenMapObjectGroups ?? [];
// Show the attribution
$showAttribution = isset($showAttribution) && !$showAttribution ? false : true;

// Additional options to pass to the map when we're in an admin environment
$adminOptions = [];
if ($isAdmin) {
    $adminOptions = [
        // Display options for changing Teeming status for map objects
        'teemingOptions' => [
            ['key' => '', 'description' => __('views/common.maps.map.no_teeming')],
            ['key' => \App\Models\Enemy::TEEMING_VISIBLE, 'description' => __('views/common.maps.map.visible_teeming')],
            ['key' => \App\Models\Enemy::TEEMING_HIDDEN, 'description' => __('views/common.maps.map.hidden_teeming')],
        ],
        // Display options for changing Faction status for map objects
        'factions'       => [
            ['key' => 'any', 'description' => __('views/common.maps.map.any')],
            ['key' => \App\Models\Faction::FACTION_ALLIANCE, 'description' => __('factions.alliance')],
            ['key' => \App\Models\Faction::FACTION_HORDE, 'description' => __('factions.horde')],
        ],
    ];
}
?>
@include('common.general.inline', ['path' => 'common/maps/map', 'options' => array_merge([
    'embed' => $embed,
    'edit' => $edit,
    'readonly' => false, // May be set to true in the code though - but set a default here
    'sandbox' => $sandboxMode,
    'defaultEnemyVisualType' => $enemyVisualType,
    'defaultUnkilledEnemyOpacity' => $unkilledEnemyOpacity,
    'defaultUnkilledImportantEnemyOpacity' => $unkilledImportantEnemyOpacity,
    'defaultEnemyAggressivenessBorder' => $defaultEnemyAggressivenessBorder,
    'noUI' => $noUI,
    'gestureHandling' => $gestureHandling,
    'zoomToContents' => $zoomToContents,
    'hiddenMapObjectGroups' => $hiddenMapObjectGroups,
    'defaultZoom' => $defaultZoom,
    'showAttribution' => $showAttribution,
    'dungeonroute' => $dungeonroute ?? null,
    // @TODO Temp fix
    'npcsMinHealth' => $mapContext['npcsMinHealth'],
    'npcsMaxHealth' => $mapContext['npcsMaxHealth'],
], $adminOptions)])

@section('scripts')
    {{-- Make sure we don't override the scripts of the page this thing is included in --}}
    @parent

    @include('common.handlebars.groupsetup')

    @include('common.general.statemanager', [
        'echo' => $echo,
        'patreonBenefits' => Auth::check() ? $user->getPatreonBenefits() : collect(),
        'userData' => $user,
        'mapContext' => $mapContext,
    ])
    <script>
        var dungeonMap;

        $(function () {
            let code = _inlineManager.getInlineCode('common/maps/map');

            // Expose the dungeon map in a global variable
            dungeonMap = code.getDungeonMap();
        });
    </script>

    @if($dungeon->isFactionSelectionRequired())
        <script id="map_faction_display_controls_template" type="text/x-handlebars-template">
        <div id="map_faction_display_controls" class="leaflet-draw-section">
            <div class="leaflet-draw-toolbar leaflet-bar leaflet-draw-toolbar-top">
            <?php
                                                                                                             $i = 0;
                                                                                                         foreach (\App\Models\Faction::where('key', '<>', \App\Models\Faction::FACTION_UNSPECIFIED)->get() as $faction) {
                                                                                                             ?>
            <a class="map_faction_display_control map_controls_custom" href="#"
               data-faction="{{ strtolower($faction->key) }}"
                       title="{{ __($faction->name) }}">
                        <i class="{{ $i === 0 ? 'fas' : 'far' }} fa-circle radiobutton"
                           style="width: 15px"></i>
                        <img src="{{ $faction->iconfile->icon_url }}" class="select_icon faction_icon"
                             data-toggle="tooltip" title="{{ __($faction->name) }}"/>
                </a>
                <?php
                                                                                                                                                                                                                                                                                                                                                                         $i++;
                                                                                                                                                                                                                                                                                                                                                                     } ?>
            </div>
            <ul class="leaflet-draw-actions"></ul>
        </div>



        </script>
    @endif
@endsection

@if(!$noUI)
    @if(isset($show['header']) && $show['header'])
        @include('common.maps.controls.header', [
            'title' => optional($dungeonroute)->title,
            'echo' => $echo,
            'edit' => $edit,
            'dungeonroute' => $dungeonroute,
            'livesession' => $livesession
        ])
    @endif

    @if(isset($show['controls']['draw']) && $show['controls']['draw'])
        @include('common.maps.controls.draw', [
            'isAdmin' => $isAdmin,
            'floors' => $dungeon->floors,
            'selectedFloorId' => $floorId,
        ])
    @elseif(isset($show['controls']['view']) && $show['controls']['view'])
        @include('common.maps.controls.view', [
            'isAdmin' => $isAdmin,
            'floors' => $dungeon->floors,
            'selectedFloorId' => $floorId,
            'dungeonroute' => $dungeonroute,
        ])
    @endif

    @if(isset($show['controls']['pulls']) && $show['controls']['pulls'])
        @include('common.maps.controls.pulls', [
            'edit' => $edit,
            'defaultState' => $show['controls']['pullsDefaultState'] ?? null,
            'hideOnMove' => $show['controls']['pullsHideOnMove'] ?? null,
            'embed' => $embed,
            'dungeonroute' => $dungeonroute,
        ])
    @endif

    @if(isset($show['controls']['enemyinfo']) && $show['controls']['enemyinfo'])
        @include('common.maps.controls.enemyinfo')
    @endif
@endif


<div id="map" class="virtual-tour-element {{$mapClasses}}" data-position="auto">

</div>

@if(!$noUI)

    @if(!$adFree && $showAds)
        @if($isMobile)
            @include('common.thirdparty.adunit', ['id' => 'map_footer', 'type' => 'footer'])
        @endif
    @endif
    <footer class="fixed-bottom container p-0" style="width: 728px">
        <div id="snackbar_container">

        </div>
        @if(!$adFree && $showAds)
            @include('common.thirdparty.adunit', ['id' => 'map_footer', 'type' => 'footer', 'class' => 'map_ad_background', 'map' => true])
        @endif
    </footer>



    @isset($dungeonroute)
        @component('common.general.modal', ['id' => 'userreport_dungeonroute_modal'])
            @include('common.modal.userreport.dungeonroute', ['dungeonroute' => $dungeonroute])
        @endcomponent

        @component('common.general.modal', ['id' => 'userreport_enemy_modal'])
            @include('common.modal.userreport.enemy')
        @endcomponent
    @endisset





    @if(isset($show['controls']['pulls']) && $show['controls']['pulls'])
        @component('common.general.modal', ['id' => 'map_settings_modal', 'size' => 'xl'])
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active"
                       id="map_settings_tab" data-toggle="tab" href="#map-settings" role="tab"
                       aria-controls="map_settings" aria-selected="false">
                        {{ __('views/common.maps.map.map_settings') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pull_settings_tab" data-toggle="tab" href="#pull-settings"
                       role="tab"
                       aria-controls="pull_settings" aria-selected="false">
                        {{ __('views/common.maps.map.pull_settings') }}
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div id="map-settings" class="tab-pane fade show active mt-3"
                     role="tabpanel" aria-labelledby="map_settings_tab">
                    @include('common.forms.mapsettings', ['dungeonroute' => $dungeonroute, 'edit' => $edit])
                </div>
                <div id="pull-settings" class="tab-pane fade mt-3" role="tabpanel"
                     aria-labelledby="pull_settings_tab">
                    @include('common.forms.pullsettings', ['dungeonroute' => $dungeonroute, 'edit' => $edit])
                </div>
            </div>

        @endcomponent
    @endif
@endif
