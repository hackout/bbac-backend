
export default {
    methods: {
        $status(name, value, key = 'value', label = 'name') {
            if(!this.$page.props[name]) return '-'
            let status = this.$page.props[name].filter(n => (typeof n[key] == 'string' ? n[key] : parseInt(n[key])) == (typeof value == 'string' ? value : parseInt(value)))
            if (status.length == 0) return '-'
            return status[0][label]
        },
        $goTo(name, params = {}) {
            window.location.href = this.$route(name, params)
        },
        $download(route, fileName = null) {
            const a = document.createElement('a');
            a.style.display = 'none';
            a.setAttribute('target', '_blank');
            fileName && a.setAttribute('download', fileName);
            a.href = route;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    },
}