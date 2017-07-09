/**
 * Mutations for game
 */

import Vue from "vue"
import { MUTATION } from "./mutation-types"

export default {
    // Set the userId
    [MUTATION.SET_USERID] (state, userId) {
        state.userId = (userId != 0) ? parseInt(userId) : null
    },

    // First load
    [MUTATION.LOAD] (state, obj) {
        // Get current player
        obj.players.some(function(player) {
            if (player.me) {
                state.me = player
            }
            return (typeof player.me != 'undefined')
        })

        // Create complet empty grid
        let grid = new Array(obj.size)
        for (let y=0; y<obj.size; y++) {
            grid[y] = new Array(obj.size)
            for (let x=0; x<obj.size; x++) {
                grid[y][x] = {x: x, y: y, img: 0}
            }
        }

        // Put box in grid
        obj.grid.forEach(function(b, i) {
            grid[b['y']][b['x']] = b
        })

        // Update state
        state.size = obj.size
        state.tour = obj.tour
        state.players = obj.players
        state.options = obj.options
        state.grid = grid
        state.gameover = (obj.finished === true)
    },

    // Set text status
    [MUTATION.SET_STATUS] (state, txt) {
        state.status = txt
    },

    // Update grid (after each rocket)
    [MUTATION.AFTER_ROCKET] (state, box) {
        // Update grid and sink boat
        if (typeof box.x !== 'undefined') {
            Vue.set(state.grid[box.y], box.x, box)
        }
        if (box.sink) {
            box.sink.forEach(function(b, i) {
                state.grid[b.y][b.x] = b
            })
        }
    },
    [MUTATION.AFTER_ANIMATE] (state, obj) {
        // Finish game
        if (obj.finished) {
            state.gameover = true
        }

        // Update tour
        if (obj.tour) {
            state.tour = obj.tour
        }
    }
}
