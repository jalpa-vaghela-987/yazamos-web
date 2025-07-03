import { request } from "@/Util/Request";
import qs from "qs";
import { mapGetters } from "vuex";
const QUERY = {
    page: 1,
    perPage: 10,
    search: '',
    order: 'desc',
    orderBy: ''
};
export default {
    props: {
        filters: {
            type: Object,
            default: () => ( {} )
        },
        listUrl: {
            required: true
        },
        columns: {
            required: true
        },
    },
    data() {
        return {
            dataSources: [],
            pageSelection: [10, 50, 100],
            query: { ...QUERY },
            pagination: [],
            sortBy: true,
            operation: null
        };
    },
    mounted() {
        this.replaceQuery(this.query);
    },
    methods: {
        handleRefreshList() {
            this.getList();
        },
        handleSearch() {
            this.query = { ...QUERY, search: this.query.search };
            this.query.page = 1;
            this.replaceQuery(this.query);
        },
        refreshTableData() {
            this.query = { ...QUERY };
            this.replaceQuery(this.query);
        },
        replaceQuery(params) {
            const currentPath = this.$route.path;
            const query = { ...this.$route.query, ...params };
            window.history.replaceState({ path: currentPath, query }, '', `${currentPath}?${qs.stringify(query)}`);
            this.getList();
        },
        async getList(filters = null) {
            try {
                let params = this.query;
                const updateFilters = filters ? filters : ( this.filters ? this.filters : null );
                params = {
                    ...params,
                    "filters": updateFilters
                };
                const response = await request({
                    method: 'get',
                    url: this.listUrl,
                    params: params,
                    paramsSerializer: ( (params) => qs.stringify(params) ),
                });
                const { data, meta } = response;
                this.dataSources = data;
                this.pagination = meta;
            } catch (error) {
                this.notifyErrorMessage(error.data.message)
            }
        },
    },
    watch: {
        'query.page': function (val) {
            this.query.page = val;
            this.replaceQuery(this.query);
        },
        'query.perPage': function (val) {
            this.query.perPage = val;
            this.replaceQuery(this.query);
        },
        'query.orderBy': function (val) {
            this.replaceQuery(this.query);
        },
        'sortBy': function (val) {
            this.query.order = val ? 'desc' : 'asc';
            this.replaceQuery(this.query);
        }
    },
    computed: {
        ...mapGetters(['loader'])
    }
};
