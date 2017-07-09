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
    // Get current player life
    life: (state) => {
        return (state.me && state.me.life) ? state.me.life : 0
    },
    // Is user in game
    isUser: (state) => {
        return state.me !== null
    },
}
