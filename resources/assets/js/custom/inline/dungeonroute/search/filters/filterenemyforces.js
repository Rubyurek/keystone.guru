class SearchFilterEnemyForces extends SearchFilter {
    constructor(selector, onChange) {
        super({
            name: 'enemy_forces',
            default: '',
            selector: selector,
            onChange: onChange
        });
    }

    activate() {
        super.activate();

        $(this.options.selector).change(this.options.onChange);
    }

    getValue() {
        return $(this.options.selector).is(':checked') ? 1 : 0;
    }
}