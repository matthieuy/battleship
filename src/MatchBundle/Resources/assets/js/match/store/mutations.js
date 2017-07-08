/**
 * Mutations for game
 */

import Vue from "vue"
import * as types from "./mutation-types"

export default {
    // Set the userId
    [types.MUTATION.SET_USERID] (state, userId) {
        state.userId = (userId != 0) ? parseInt(userId) : null
    },

    // First load
    [types.MUTATION.LOAD] (state, obj) {
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
    [types.MUTATION.SET_STATUS] (state, txt) {
        state.status = txt
    },

    // Set tour
    [types.MUTATION.SET_TOUR] (state, tour) {
        state.tour = tour
    },

    // Set gameover
    [types.MUTATION.SET_GAMEOVER] (state) {
        state.gameover = true
    },

    // Update grid (after animate)
    [types.MUTATION.UPDATE_GRID] (state, box) {
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

            // Update inventory
            if (box.bonus && box.bonus[state.me.position]) {
                state.me.nbBonus = box.bonus[state.me.position]
                delete box.bonus
            }
        }

        // Update grid and sink boat
        Vue.set(state.grid[box.y], box.x, box)
        if (box.sink) {
            box.sink.forEach(function(b, i) {
                state.grid[b.y][b.x] = b
            })
        }
    },
}
