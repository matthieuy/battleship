/*
 * Vuex Store for waiting page
 */

// Imports
import Vue from "vue"
import Vuex from "vuex"
import mutations from "./mutations"
import actions from "./actions"

// Init
Vue.use(Vuex)

// State
const state = {
    players: [],
    game: {},
    joined: false,
    userId: null,
    me: null,
    isCreator: false,
    loaded: false,
}

// Export store
export default new Vuex.Store({
    state,
    mutations,
    getters: {},
    actions,
    strict: process.env.NODE_ENV !== 'production',
})

