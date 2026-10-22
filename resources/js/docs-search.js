// The results that open under the docs search box as people type: the same search as the
// results page (App\Support\DocsSearch), asked for as JSON (DocsSearchController). Arrow keys
// move through them and Enter opens one; Enter with none picked opens the results page.
export default (endpoint, locale) => ({
    query: '',
    results: [],
    more: false,
    open: false,
    active: -1,
    request: null,

    init() {
        this.query = this.$refs.input.value;
    },

    async search() {
        const query = this.query.trim();
        this.request?.abort();

        if (query.length < 2) {
            this.close();
            this.results = [];

            return;
        }

        const request = new AbortController();
        this.request = request;

        try {
            const url = new URL(endpoint, window.location.origin);
            url.search = new URLSearchParams({ q: query, locale });

            const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: request.signal });

            if (! response.ok) {
                return;
            }

            const found = await response.json();
            this.results = found.results;
            this.more = found.more;
            this.active = -1;
            this.open = true;
        } catch (error) {
            if (error.name !== 'AbortError') {
                throw error;
            }
        }
    },

    reopen() {
        this.open = this.query.trim().length >= 2 && this.results.length > 0;
    },

    move(step) {
        if (! this.open || this.results.length === 0) {
            return;
        }

        this.active = (this.active + step + this.results.length) % this.results.length;
    },

    choose(event) {
        if (this.open && this.active >= 0) {
            event.preventDefault();
            window.location.href = this.results[this.active].url;
        }
    },

    close() {
        this.open = false;
        this.active = -1;
    },

    // The results page, for every page the search finds.
    allResultsUrl() {
        return `${this.$root.action}?${new URLSearchParams({ q: this.query.trim() })}`;
    },
});
