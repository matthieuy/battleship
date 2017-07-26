// Imports
import Vue from "vue"
import Vuex from "vuex"
import mutations from "./mutations"
import getters from "./getters"
import actions from "./actions"
import weaponModule from "@bonus/js/store-modules/weapons"
import inventoryModule from "@bonus/js/store-modules/inventory"
import scoreModule from "./modules/score"

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
    status: Translator.trans('loading'),
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
