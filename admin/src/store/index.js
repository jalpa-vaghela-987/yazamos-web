import Vue from "vue";
import Vuex from "vuex";
import createPersistedState from "vuex-persistedstate";
import { removeStorage, setStorage } from '@/Util/auth';

Vue.use(Vuex);

const state = {
    user: null,
    loader: false,
    validate: false,
    locale: 'en-US',
    isRTL: false,
    setDefaultLocale: 'en-Us',
    activeTab: 'completed',
    isUnAuthorized: true,
    permissions: [],
    userActivePlan: []
};

const store = new Vuex.Store({
    state,
    getters: {
        user: (state) => {
            return state.user;
        },
        loader: (state) => {
            return state.loader;
        },
        locale: (state) => {
            return state.locale;
        },
        validate: (state) => {
            return state.validate;
        },
        activeTab: (state) => {
            return state.activeTab;
        },
        getUnAuthorized: (state) => {
            return state.isUnAuthorized;
        },
        userActivePlan: (state) => {
            return state.userActivePlan;
        },
        permissions: (state) => {
            return state.permissions;
        },
    },
    actions: {
        user: (context, user) => {
            context.commit('user', user);
        },
        loader: (context, data) => {
            context.commit('LOADER', data);
        },
        setLoader: (context, data) => {
            context.commit('LOADER', data);
        },
        change_setting: (context, data) => {
            context.commit('CHANGE_SETTING', data);
        },
        setValidate: (context, data) => {
            context.commit('VALIDATE', data);
        },
        setActiveTab: (context, data) => {
            context.commit('activeTab', data);
        },
        setUnAuthorized: (context, data) => {
            context.commit('SET_UNAUTHORIZED', data);
        },
        setUserActivePlan: (context, data) => {
            context.commit('userActivePlan', data);
        },
        // setPermissions: (context, data) => {
        //     context.commit('permissions', data);
        // },
        setIsRTL: (context, data) => {
            context.commit('SET_IS_RTL', data);
        },
    },
    mutations: {
        user: (state, user) => {
            state.user = user;
            if (state.user === null) {
                removeStorage('auth');
            }
        },
        LOADER: (state, data) => {
            state.loader = data;
        },
        CHANGE_SETTING(state, payload) {
            setStorage('locale', payload);
            state.locale = payload;
        },
        VALIDATE: (state, data) => {
            state.validate = data;
        },
        activeTab: (state, activeTab) => {
            state.activeTab = activeTab;
        },
        SET_UNAUTHORIZED: (state, data) => {
            state.isUnAuthorized = data;
        },
        userActivePlan: (state, data) => {
            state.userActivePlan = data;
        },
        permissions: (state, data) => {
            state.permissions = data;
        },
        SET_IS_RTL(state, data) {
            state.isRTL = data;
        },
    },
    plugins: [createPersistedState()]
});
export default store;
