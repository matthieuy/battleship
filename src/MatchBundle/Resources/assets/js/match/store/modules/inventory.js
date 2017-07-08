/**
 * Inventory module
 */

export const MUTATION = {
    FIRST_LOAD: require('../mutation-types').MUTATION.LOAD,
}

export default {
    state: {
        enabled: false,
    },
    mutations: {
        // On first load game
        [MUTATION.FIRST_LOAD](state, obj) {
            state.enabled = obj.options.bonus
        },
    },
    actions: {},
    getters: {},
}
