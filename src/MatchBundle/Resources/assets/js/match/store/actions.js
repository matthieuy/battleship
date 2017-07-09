/**
 * Actions for game
 */

import { ACTION } from "./mutation-types"

export default {
    // Load the game
    [ACTION.LOAD_GAME](context) {
        return new Promise((resolve, reject) => {
            WS.on('socket/connect',() => {
                WS.callRPC('run/load', {}, resolve)
            })
        })
    },

    // Shoot
    [ACTION.SHOOT](context, obj) {
        return new Promise((resolve, reject) => {
            WS.callRPC('run/shoot', obj, resolve)
        })
    },
}
