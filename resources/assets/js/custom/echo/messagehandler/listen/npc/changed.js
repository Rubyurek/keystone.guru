class NpcChangedHandler extends MessageHandler {

    constructor(echo) {
        super(echo, '.npc-changed');
    }


    /**
     *
     * @param e {NpcChangedMessage}
     */
    onReceive(e) {
        super.onReceive(e);
        let mapContext = getState().getMapContext();
        let isSameDungeon = e.model.dungeon_id === mapContext.getDungeon().id;

        // Remove any existing NPC
        mapContext.removeRawNpcById(e.model.id);

        // Do not add the npc does not belong to this dungeon
        if (isSameDungeon) {
            // Add the new NPC
            mapContext.addRawNpc(e.model);
        }


        // Redraw all enemies that have this npc so that we're up-to-date
        let enemyMapObjectGroup = this.echo.map.mapObjectGroupManager.getByName(MAP_OBJECT_GROUP_ENEMY);
        for (let key in enemyMapObjectGroup.objects) {
            let enemy = enemyMapObjectGroup.objects[key];
            if (enemy.npc_id === e.model.id) {
                // Re-assign the enemy if it was just updated, unassign it if is no longer available
                enemy.setNpc(isSameDungeon ? e.model : null);
                enemy.visual.refresh();
            }

            enemy.setSynced(true);
        }
    }
}
