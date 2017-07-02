/**
 * Actions for game
 */

import * as types from "./mutation-types"

export default {
    // Load the game
    [types.ACTION.LOAD_GAME](context) {
        return new Promise((resolve, reject) => {
            WS.on('socket/connect',() => {
                WS.callRPC('run/load', {}, resolve)
            })
        })
    },

    // Shoot
    [types.ACTION.SHOOT](context, obj) {
        return new Promise((resolve, reject) => {
            WS.callRPC('run/shoot', obj, resolve)
        })
    },
}
