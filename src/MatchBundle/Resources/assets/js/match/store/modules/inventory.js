/**
 * Inventory module
 */

export const MUTATION = {
    FIRST_LOAD: require('../mutation-types').MUTATION.LOAD,
    BONUS_MODAL: 'BONUS_MODAL',
}

export default {
    state: {
        modal: false,
        enabled: false,
        list: [],
    },
    mutations: {
        // On first load game
        [MUTATION.FIRST_LOAD](state, obj) {
            state.enabled = obj.options.bonus
        },
        // Toggle modal
        [MUTATION.BONUS_MODAL](state, status) {
            if (typeof status !== "undefined") {
                state.modal = status
            } else {
                state.modal = !state.modal
            }
        },
    },
    actions: {},
    getters: {},
}
