import * as types from './mutation-types'
/* global WS */

export default {
  // Change grid size
  [types.ACTION.CHANGE_SIZE] (context, size) {
    WS.callRPC('wait/size', {type: 'size', size: size})
  },

  // Change player's color
  [types.ACTION.CHANGE_COLOR] (context, obj) {
    WS.callRPC('wait/color', obj)
  },

  // Change player's team
  [types.ACTION.CHANGE_TEAM] (context, obj) {
    WS.callRPC('wait/team', obj)
  },

  // Remove a player
  [types.ACTION.REMOVE_PLAYER] (context, idPlayer) {
    return new Promise((resolve, reject) => {
      WS.callRPC('wait/join', {
        join: false,
        id: idPlayer,
      }, resolve)
    })
  },

  // Change order
  [types.ACTION.UPDATE_ORDER] (context, obj) {
    WS.callRPC('wait/position', obj)
  },

  // Change max players
  [types.ACTION.CHANGE_MAX] (state, max) {
    WS.callRPC('wait/size', {type: 'max', size: max})
  },

  // Join or leave the gage
  [types.ACTION.JOIN_LEAVE] (state, join) {
    return new Promise((resolve, reject) => {
      WS.callRPC('wait/join', {join: join}, resolve)
    })
  },

  // Add a AI
  [types.ACTION.ADD_AI] (state) {
    return new Promise((resolve, reject) => {
      WS.callRPC('wait/join', {join: true, ai: true}, resolve)
    })
  },

  // Change options
  [types.ACTION.CHANGE_OPTION] (state, obj) {
    return new Promise((resolve, reject) => {
      WS.callRPC('wait/options', obj, resolve)
    })
  },

  // Run
  [types.ACTION.RUN] (state) {
    return new Promise((resolve, reject) => {
      WS.callRPC('launch/run', {}, resolve)
    })
  },
}
