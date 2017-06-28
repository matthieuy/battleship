// Imports
import Vue from "vue"
import Vuex from "vuex"

Vue.use(Vuex)

const state = {
    userId: null,
    size: null,
    boxSize: 20,
    tour: [],
    status: 'Loading game...',
    players: [],
    me: null,
    grid: [],
    options: {},
    weapons: {
        modalOpen: false,
    },
}

const mutations = {
    // Set the userId
    SET_USERID: (state, userId) => {
        state.userId = (userId != 0) ? parseInt(userId) : null
    },
    // On first load : change state from RPC response
    FIRST_LOAD: (state, obj) => {
        state.size = obj.size
        state.tour = obj.tour
        state.players = obj.players
        state.options = obj.options

        // Get player
        obj.players.some(function(player) {
            if (player.me) {
                state.me = player
            }
            return (typeof player.me != 'undefined')
        })

        // Create complet empty grid
        let grid = new Array(state.size)
        for (let y=0; y<state.size; y++) {
            grid[y] = new Array(state.size)
            for (let x=0; x<state.size; x++) {
                grid[y][x] = {x: x, y: y, img: 0}
            }
        }

        // Put box in grid
        obj.grid.forEach(function(b, i) {
            grid[b['y']][b['x']] = b
        })
        state.grid = grid
    },
    SET_STATUS: (state, status) => {
        state.status = status
    },
    SET_TOUR: (state, tour) => {
        state.tour = tour
    },
    UPDATE_GRID: (state, box) => {
        // Update bubble
        if (state.me) {
            // Update life
            if (box.life && box.life[state.me.position]) {
                state.me.life = box.life[state.me.position]
                delete box.life
            }
            // Update score
            if (box.score && box.score[state.me.position]) {
                state.me.score = box.score[state.me.position]
                delete box.score
            }
        }

        // Update grid and sink boat
        state.grid[box.y][box.x] = box
        if (box.sink) {
            box.sink.forEach(function(b, i) {
                state.grid[b.y][b.x] = b
            })
        }
    },
    TOGGLE_WEAPON_MODAL: (state, status) => {
        if (typeof status !== 'undefined') {
            state.weapons.modalOpen = status
        } else {
            state.weapons.modalOpen = !state.weapons.modalOpen
        }
    }
}

const getters = {
    size: (state) => state.size,
    boxSize: (state) => state.boxSize,
    box: (state) => (x, y) => {
        if (typeof state.grid[y] !== 'undefined' && typeof state.grid[y][x] !== 'undefined') {
            return state.grid[y][x];
        }
        return null;
    },
    playerById: (state) => (playerId) => {
        if (state.players[playerId]) {
            return state.players[playerId]
        }
        return null
    },
    me: (state) => state.me,
    options: (state) => state.options,
    weapons: (state) => state.weapons,
}

const actions = {
    // Load the game
    load(context) {
        WS.on('socket/connect',() => {
            WS.callRPC('run/load', {}, (obj) => {
                context.commit('FIRST_LOAD', obj)
            })
        })
    },
}

export default new Vuex.Store({
    state,
    mutations,
    getters,
    actions,
    strict: true
})
