import { client } from "./Request";
import SecureLS from "secure-ls";
import serverConfig from '../../server.config.json';
import moment from "moment-timezone";
export const DEFAULT_STORAGE = new SecureLS({
    encodingType: 'base64',
    isCompression: false
});
export const STORAGE_PREFIX = serverConfig.storagePrefix;
export function setStorage(key, value) {
    DEFAULT_STORAGE.set(`${ STORAGE_PREFIX }.${ key }`, value);
}
export function getStorage(key) {
    try {
        return DEFAULT_STORAGE.get(`${ STORAGE_PREFIX }.${ key }`) || null;
    } catch (error) {
        return null;
    }
}
export function removeStorage(key) {
    return DEFAULT_STORAGE.remove(`${ STORAGE_PREFIX }.${ key }`);
}
export function hasAuthUser() {
    let user = getStorage("auth");
    user = ( ( user && user !== '' ) ? JSON.parse(user) : {} );
    if ( !user ) {
        return false;
    }
    return !!( user.access_token );
}
export function getAuthUser() {
    let user = getStorage(`auth`);
    user = ( ( user && user !== '' ) ? JSON.parse(user) : {} );
    if ( Object.keys(user).length <= 0 ) {
        removeStorage(`auth`);
        return {};
    }
    return user;
}
export function dateFormat(date = null, format) {
    if ( date ) {
        return moment(date).format(format);
    }
    return date;
}
export function refresh() {
    window.location.reload(true);
}
export function refreshToHome() {
    window.location.reload(true);
}
export function handleSyncRequestLoader({ dispatch }, baseURL) {
    client.interceptors.request.use(
      config => {
          config.baseURL = `${ baseURL }`;
          if ( config.method !== "get" && ( config.data && config.data.ignore_request > 0 ) ) {
              return config;
          } else if ( config.method === "get" && ( config.params && config.params.ignore_request > 0 ) ) {
              return config;
          }
          dispatch('loader', true);
          return config;
      },
      error => {
          return Promise.reject(error);
      }
    );
    client.interceptors.response.use(
      ({ data }) => {
          dispatch('loader', false);
          return Promise.resolve(data);
      },
      error => {
          dispatch('loader', false);
          if ( error.response && error.response.status === 401 ) {
              dispatch('setUnAuthorized', true);
              setTimeout(() => {
                  dispatch('setUnAuthorized', false);
              }, 2000);
          }
          return Promise.reject(error);
      }
    );
}

export function hasRole(role) {
    let user = getStorage(`auth`);

    user = ( ( user && user !== '' ) ? JSON.parse(user) : {} );
    return user?.user?.role == role;
}
