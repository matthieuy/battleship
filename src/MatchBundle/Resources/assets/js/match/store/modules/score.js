/**
 * Score module vuex store
 */

import { ACTION, MUTATION } from "../mutation-types"

export default {
    state: {
        modal: false,
        list: [],
        life: 0,
    },
    mutations: {
        // On first load game
        [MUTATION.LOAD](state, obj) {
            // Score
            obj.players.some(function(player) {
                if (player.me) {
                    state.life = player.life
                }
                return (typeof player.me != 'undefined')
            })
        },
        // Toggle modal
        [MUTATION.SCORE.MODAL](state, status) {
            if (typeof status !== "undefined") {
                state.modal = status
            } else {
                state.modal = !state.modal
            }
        },
        // Receive list from WS
        [MUTATION.SCORE.SET_LIST](state, list) {
            state.list = list
        },
    },
    actions: {
        // After each rocket
        [ACTION.AFTER_ROCKET](context, box) {
            // Update life
            if (box.life && box.life[context.rootState.me.position]) {
                context.commit(MUTATION.SCORE.SET_LIFE, box.life[context.rootState.me.position])
                delete box.life
            }
        },
    },
    getters: {
        // Get teams (order by team life)
        teams: (state) => {
            let list = [];
            state.list.forEach(function(player) {
                // Create index if don't exist
                if (typeof list[player.team] == 'undefined') {
                    list[player.team] = {
                        team: player.team,
                        players: [],
                        life: 0,
                    }
                }
                // Add player to team and sort player's life
                list[player.team].players.push(player)
                list[player.team].life += player.life
                list[player.team].players.sort((a, b) => a.life < b.life)
            })

            // Team 0 don't exist
            list.shift()

            // Sort team's life
            list.sort((a, b) => a.life < b.life)

            return list
        },
    },
}
