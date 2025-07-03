import axios from 'axios';
import moment from 'moment-timezone';
import serverConfig from "../../server.config.json";
import { getAuthUser, hasAuthUser, redirectLogin, removeStorage } from "./auth";
import router from '@/router'; // or wherever your Vue router is defined

/**
 * Create an Axios Client with defaults
 */
let axiosInstance = axios.create();
axiosInstance.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axiosInstance.defaults.headers.common['Content-Type'] = 'application/json';
axiosInstance.defaults.headers.common['Content-Language'] = localStorage.getItem(`${serverConfig.storagePrefix}.settings.locale`) || 'en-US';
export const client = axiosInstance;
/**
 * Request Wrapper with default success/error actions
 */
export const request = function (options) {
    if (hasAuthUser() && getAuthUser()) {
        const user = getAuthUser();
        axiosInstance.defaults.headers.common['Authorization'] = `Bearer ${user.access_token}`;
        axiosInstance.defaults.headers.common['Content-Timezone'] = moment.tz.guess();
    }
    const onSuccess = function (response) {
        return response;
    };
    const onError = function (error) {
        if (error.response) {
            if (error.response.status === 401) {
                // Only remove auth and redirect if not already on login page
                const currentRoute = router.currentRoute;
                if (currentRoute && currentRoute.path !== '/admin/login' && currentRoute.path !== '/login') {
                    removeStorage('auth');
                    router.push('/admin/login');
                }
            }
        }
        return Promise.reject(error.response || error.message);
    };

    return axiosInstance(options)
        .then(onSuccess)
        .catch(onError);
};