/**
 * Score module vuex store
 */

export const MUTATION = {
    SCORE_MODAL: 'SCORE_MODAL',
    RECEIVE_LIST: 'RECEIVE_LIST',
}

export default {
    state: {
        modal: false,
        list: [],
    },
    mutations: {
        // Toggle modal
        [MUTATION.SCORE_MODAL](state, status) {
            if (typeof status !== "undefined") {
                state.modal = status
            } else {
                state.modal = !state.modal
            }
        },
        // Receive list from WS
        [MUTATION.RECEIVE_LIST](state, list) {
            state.list = list
        },
    },
    actions: {},
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
