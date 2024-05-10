import axios from 'axios';
import { ElNotification } from 'element-plus';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.timeout = 50000;

const HTTP_STATUS_NOT_FOUND = 404;
const HTTP_STATUS_INTERNAL_SERVER_ERROR = 500;
const errorText = '网络请求错误';

// Reusable Axios instance
const axiosInstance = axios.create();

axiosInstance.interceptors.response.use(
	(response) => response,
	(error) => {
		const status = error.response ? error.response.status : 0;

		if (status === HTTP_STATUS_NOT_FOUND) {
			ElNotification.error({ title: errorText, message: error.response.data.message });
		} else if (status === HTTP_STATUS_INTERNAL_SERVER_ERROR) {
			ElNotification.error({
				title: errorText,
				message: error.response.data.message || 'Status:500, Request server not responding!',
			});
		} else {
			ElNotification.error({
				title: errorText,
				message: error.message || `Status:${status}, Unknown`,
			});
		}

		return Promise.reject(error.response);
	}
);

const http = {
	get: (url, params = {}, config = {}) => axiosInstance.get(url, { params, ...config }).then((response) => response.data),
	post: (url, data = {}, config = {}) => axiosInstance.post(url, data, config).then((response) => response.data),
	put: (url, data = {}, config = {}) => axiosInstance.put(url, data, config).then((response) => response.data),
	patch: (url, data = {}, config = {}) => axiosInstance.patch(url, data, config).then((response) => response.data),
	delete: (url, data = {}, config = {}) => axiosInstance.delete(url, { data, ...config }).then((response) => response.data),
	jsonp: (url, name = 'jsonp') => new Promise((resolve) => {
		const script = document.createElement('script');
		const _id = `jsonp${Math.ceil(Math.random() * 1000000)}`;
		script.id = _id;
		script.type = 'text/javascript';
		script.src = url;

		window[name] = (response) => {
			resolve(response);
			document.getElementsByTagName('head')[0].removeChild(script);

			try {
				delete window[name];
			} catch (e) {
				window[name] = undefined;
			}
		};

		document.getElementsByTagName('head')[0].appendChild(script);
	}),
};

export default http;
