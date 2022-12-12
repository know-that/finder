const api = axios.create({
    timeout: 60000
})
api.interceptors.response.use(
    (response) => {
        return response
    },
    (error) => {
        return Promise.reject(error)
    }
)

new Vue({
    el: '#app',
    data: () => ({
        active: [],
        avatar: null,
        open: [],
        users: [],
        props: {
            label: 'name',
            children: 'children',
            isLeaf: 'isLeaf'
        },
        items: [],
        file: {},
        loading: false,
        visible: false,
        error: {}
    }),
    methods: {
        /**
         * 目录树加载
         * @param node
         * @param resolve
         * @returns {Promise<void>}
         */
        async loadNode(node, resolve) {
            let data = []
            let path = node.level === 0 ? '/' : node.data.path
            let result = await this.getCatalogues(path)
            result.forEach(item => {
                data.push({
                    name: item.name,
                    path: item.path,
                    relative_path: item.relative_path,
                    isLeaf: item.type !== 'dir',
                    type: item.type
                })
            })
            resolve(data)
        },

        /**
         * 目录数据
         * @param path
         * @returns {Promise<AxiosResponse<any>>}
         */
        async getCatalogues(path = '/') {
            return await api.get('/know-that/finder/catalogues', {
                params: {
                    path
                }
            })
            .then(res => {
                return res.data
            })
        },

        /**
         * 文件内容
         * @param params
         */
        async getFile(params) {
            if (params.type !== 'file') {
                return;
            }
            this.loading = true
            await api.get('/know-that/finder/contents', {
                params: {
                    path: params.relative_path,
                    name: params.name
                }
            })
            .then(res => {
                this.file = res.data
            })
            .catch(res => {
                this.file = {}
                this.error = res
            })

            this.loading = false
        }
    }
})
