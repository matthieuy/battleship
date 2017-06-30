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

/*
const mutations = {
    ROTATE_WEAPON: (state) => {
        let rotate = state.weapons.rotate + 1
        if (rotate > 3) {
            rotate = rotate % 4
        }
        state.weapons.rotate = rotate
        console.info('[Weapons] Rotate '+ rotate*90 + 'deg')

        state.weapons.list.forEach(function(weapon, iW) {
            if (!weapon.rotate) {
                return true
            }

            let grid = weapon.grid

            // Create new grid
            let newGrid = []
            newGrid.length = grid[0].length
            for (let i=0; i<newGrid.length; i++) {
                newGrid[i] = []
                newGrid[i].length = grid.length
            }

            // Rotate
            for (let i=0; i<grid.length; i++) {
                for (let j=0; j<grid[i].length; j++) {
                    newGrid[j][grid.length - i - 1] = grid[i][j]
                }
            }

            // Apply
            weapon.grid = newGrid
        })
    },
}

const getters = {
    box: (state) => (x, y) => {
        if (typeof state.grid[y] !== 'undefined' && typeof state.grid[y][x] !== 'undefined') {
            return state.grid[y][x];
        }
        return null;
    },
}
//*/
