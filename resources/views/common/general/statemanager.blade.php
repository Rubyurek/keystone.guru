<script>
    /** Instance that handles the interal state for the dungeon map */
    let _stateManager;

    document.addEventListener("DOMContentLoaded", function () {
        _stateManager = new StateManager();
    });

    /**
     * Get the current state manager of the app.
     * @return StateManager
     **/
    function getState() {
        return _stateManager;
    }
</script>