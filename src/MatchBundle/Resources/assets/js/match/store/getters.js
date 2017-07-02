/**
 * Getters for game store
 */

export default {
    // Get player by id
    playerById: (state) => (playerId) => {
        if (state.players[playerId]) {
            return state.players[playerId]
        }
        return null
    },
}
