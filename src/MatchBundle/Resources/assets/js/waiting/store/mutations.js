/**
 * Mutations for Waiting vuex store
 */

import * as types from './mutation-types'

export default {
  // Join game
  [types.MUTATION.JOIN] (state) {
    state.joined = true
  },

  // Leave game
  [types.MUTATION.LEAVE] (state) {
    state.joined = false
  },

  // Set players (and me)
  [types.MUTATION.SET_PLAYERS] (state, players) {
    state.players = players
    state.me = null
    players.forEach((player) => {
      if (player.userId === state.userId) {
        state.me = player
      }
    })
    state.joined = (state.me !== null)
    state.loaded = true
  },

  // Set UserID
  [types.MUTATION.SET_USERID] (state, userId) {
    state.userId = (userId !== 0) ? parseInt(userId) : null
  },

  // Set game infos
  [types.MUTATION.SET_GAMEINFO] (state, infos) {
    state.isCreator = (infos.creatorId === state.userId)
    state.game = infos
  },
}
