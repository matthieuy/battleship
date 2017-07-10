// Imports
import Vue from "vue"
import Vuex from "vuex"
import mutations from "./mutations"
import getters from "./getters"
import actions from "./actions"
import weaponModule from "./modules/weapons"
import scoreModule from "./modules/score"
import inventoryModule from "./modules/inventory"

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
    status: 'Loading game...',
    gameover: false,
}

// Export store
export default new Vuex.Store({
    state,
    mutations,
    getters,
    actions,
    modules: {
        weapon: weaponModule,
        score: scoreModule,
        inventory: inventoryModule,
    },
    strict: process.env.NODE_ENV !== 'production',
})
