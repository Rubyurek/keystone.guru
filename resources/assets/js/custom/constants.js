if (typeof Cookies.get('polyline_default_color') === 'undefined') {
    Cookies.set('polyline_default_color', '#9DFF56');
}
if (typeof Cookies.get('polyline_default_weight') === 'undefined') {
    Cookies.set('polyline_default_weight', 3);
}
if (typeof Cookies.get('hidden_map_object_groups') === 'undefined') {
    Cookies.set('hidden_map_object_groups', []);
}

Cookies.defaults = $.extend(Cookies.defaults, {
    polyline_default_color: '#9DFF56',
});

let c = {
    map: {
        admin: {
            mapobject: {
                colors: {
                    unsaved: '#E25D5D',
                    unsavedBorder: '#7C3434',

                    edited: '#E2915D',
                    editedBorder: '#7C5034',

                    saved: '#5DE27F',
                    savedBorder: '#347D47',

                    mouseoverAddEnemy: '#5993D2',
                    mouseoverAddEnemyBorder: '#34577D',
                }
            }
        },
        enemy: {
            /**
             * At whatever zoom the classifications are displayed on the map
             */
            classification_display_zoom: 3,
            /**
             * At whatever zoom the truesight modifier are displayed on the map
             */
            truesight_display_zoom: 3,
            /**
             * At whatever zoom the teeming modifier are displayed on the map
             */
            teeming_display_zoom: 3,
            awakened_display_zoom: 3,
            colors: [
                /*'#C000F0',
                '#E25D5D',
                '#5DE27F'*/
                'green', 'yellow', 'orange', 'red', 'purple'
            ],
            minSize: 12,
            maxSize: 26,
            margin: 2,
            calculateMargin: function (size) {
                let range = c.map.enemy.maxSize - c.map.enemy.minSize;
                let zeroBased = (size - c.map.enemy.minSize);
                return (zeroBased / range) * c.map.enemy.margin;
            },
            calculateSize: function (health, minHealth, maxHealth) {
                // Perhaps some enemies are below minHealth, should not be lower than it, nor higher than max health (bosses)
                health = Math.min(Math.max(health, minHealth), maxHealth);

                // Offset the min health
                health -= minHealth;
                maxHealth -= minHealth;

                // Scale factor
                let scale = getState().getMapZoomLevel() / 2.0;

                let result = (c.map.enemy.minSize + ((health / maxHealth) * (c.map.enemy.maxSize - c.map.enemy.minSize))) * scale;
                // console.log(typeof result, result, typeof Math.floor(result), Math.floor(result));

                // Return the correct size
                return Math.floor(result);
            }
        },
        adminenemy: {
            mdtPolylineOptions: {
                color: '#00FF00',
                weight: 1
            },
            mdtPolylineMismatchOptions: {
                color: '#FFA500',
                weight: 1
            }
        },
        enemypack: {
            colors: {
                unsaved: '#E25D5D',
                unsavedBorder: '#7C3434',

                edited: '#E2915D',
                editedBorder: '#7C5034',

                saved: '#5993D2',
                savedBorder: '#34577D'
            },
            margin: 2,
            arcSegments: function (nr) {
                return Math.max(5, (9 - nr) + (getState().getMapZoomLevel() * 2));
            },
            polygonOptions: {
                color: '#9DFF56',
                weight: 1,
                fillOpacity: 0.3,
                opacity: 1
            },
        },
        enemypatrol: {
            defaultColor: '#E25D5D'
        },
        /* These colors may be overriden by drawcontrols.js */
        path: {
            defaultColor: Cookies.get('polyline_default_color'),
        },
        polyline: {
            defaultColor: Cookies.get('polyline_default_color'),
            defaultWeight: Cookies.get('polyline_default_weight'),
        },
        brushline: {
            /**
             * The minimum distance (squared) that a point must have before it's added to the line from the previous
             * point. This is to prevent points from being too close to eachother and reducing performance, increasing
             * bandwidth and storage in database (though that's not that big of a deal).
             **/
            minDrawDistanceSquared: 3
        },
        killzone: {
            colors: {
                unsavedBorder: '#E25D5D',

                editedBorder: '#E2915D',

                savedBorder: '#5DE27F',

                mouseoverAddObject: '#5993D2',
            },
            polylineOptions: {
                color: Cookies.get('polyline_default_color'),
                weight: 1
            },
            polygonOptions: {
                color: Cookies.get('polyline_default_color'),
                weight: 2,
                fillOpacity: 0.3,
                opacity: 1,
            },
            // Whenever the killzone is selected or focussed by the user to adjust it
            polygonOptionsSelected: {
                delay: 400,
                dashArray: [10, 20],
                pulseColorLight: '#FFF',
                pulseColorDark: '#000',
                hardwareAccelerated: true,
                use: L.polygon
            },
            margin: 2,
            arcSegments: function (nr) {
                return Math.max(5, (9 - nr) + (getState().getMapZoomLevel() * 2));
            }
        },
        placeholderColors: {},
        colorPickerDefaultOptions: {
            theme: 'nano', // 'classic' or 'monolith', or 'nano',

            // Set in state manager when class colors are set
            swatches: [],

            components: {

                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: true,
                    rgba: true,
                    hsla: false,
                    hsva: false,
                    cmyk: false,
                    input: true,
                    clear: false,
                    save: true
                }
            }
        }
    }
};