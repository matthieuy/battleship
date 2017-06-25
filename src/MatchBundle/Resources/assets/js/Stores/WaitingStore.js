/*
 * Vuex Store for waiting page
 */

// Imports
import Vue from "vue"
import Vuex from "vuex"

Vue.use(Vuex)

const state = {
    joined: false,
    players: [],
    game: {},
    me: null,
    userId: null,
    isCreator: false,
    loaded: false,
}

const mutations = {
    JOIN: (state) => {
        state.joined = true
    },
    LEAVE: (state) => {
        state.joined = false
    },
    SET_PLAYERS: (state, players) => {
        state.players = players
        state.me = null

        players.forEach((player) => {
            if (player.userId == state.userId) {
                state.me = player
            }
        })
        state.joined = (state.me !== null)
        state.loaded = true
    },
    SET_USERID: (state, userId) => {
        state.userId = (userId != 0) ? parseInt(userId) : null
    },
    SET_GAME_INFOS: (state, infos) => {
        state.isCreator = (infos.creatorId === state.userId)
        state.game = infos
    },
}

const getters = {
    joined: (state) => state.joined,
    players: (state) => state.players,
    loaded: (state) => state.loaded,
    game: (state) => state.game,
    isCreator: (state) => state.isCreator,
    userId: (state) => state.userId,
}

const actions = {}

export default new Vuex.Store({
    state,
    mutations,
    getters,
    actions,
    strict: true
})
