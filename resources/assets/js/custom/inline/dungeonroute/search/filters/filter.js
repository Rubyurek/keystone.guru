class SearchFilter {
    constructor(options) {
        console.assert(options.hasOwnProperty('name'), 'Filter options must have a name set', this);
        console.assert(options.hasOwnProperty('default'), 'Filter options must have a default set', this);
        console.assert(options.hasOwnProperty('selector'), 'Filter options must have a selector set', this);
        console.assert(options.hasOwnProperty('onChange'), 'Filter options must have a onChange callback set', this);

        this.options = options;
    }

    activate() {

    }

    getFilterHeaderHtml() {

    }

    getValue() {

    }
}