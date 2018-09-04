class KillZoneMapObjectGroup extends MapObjectGroup {
    constructor(map, name, editable){
        super(map, name, editable);

        this.title = 'Hide/show killzone';
        this.fa_class = 'fa-bullseye';
    }

    _createObject(layer){
        console.assert(this instanceof KillZoneMapObjectGroup, 'this is not an KillZoneMapObjectGroup');

        return new KillZone(this.map, layer);
    }


    fetchFromServer(floor) {
        // no super call required
        console.assert(this instanceof KillZoneMapObjectGroup, this, 'this is not a KillZoneMapObjectGroup');

        let self = this;

        $.ajax({
            type: 'GET',
            url: '/ajax/killzones',
            dataType: 'json',
            data: {
                dungeonroute: dungeonRoutePublicKey, // defined in map.blade.php
                floor_id: floor.id
            },
            success: function (json) {
                // Remove any layers that were added before
                self._removeObjectsFromLayer.call(self);

                // Now draw the patrols on the map
                for (let index in json) {
                    if (json.hasOwnProperty(index)) {
                        let remoteKillZone = json[index];

                        let layer = new LeafletEnemyMarker();
                        layer.setLatLng(L.latLng(remoteKillZone.lat, remoteKillZone.lng));

                        let killzone = self.createNew(layer);
                        killzone.id = remoteKillZone.id;
                        // We just downloaded the kill zone, it's synced alright!
                        killzone.setSynced(true);
                    }
                }
            }
        });
    }
}