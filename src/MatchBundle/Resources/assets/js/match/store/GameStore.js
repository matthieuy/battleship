// Imports
import Vue from "vue"
import Vuex from "vuex"
import mutations from "./mutations"
import getters from "./getters"
import actions from "./actions"
import weaponModule from "./modules/weapons"

// Init
Vue.use(Vuex)

// State
const state = {
    userId: null,
    size: null,
    boxSize: 20,
    tour: [],
    players: [],
    options: {},
    me: null,
    grid: [],
    status: 'Loading game...'
}

// Export store
export default new Vuex.Store({
    state,
    mutations,
    getters,
    actions,
    modules: {
        weapon: weaponModule,
    },
    strict: process.env.NODE_ENV !== 'production',
})
