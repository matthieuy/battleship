/**
 * Inventory module
 */

export const MUTATION = {
    FIRST_LOAD: require('../mutation-types').MUTATION.LOAD,
    BONUS_MODAL: 'BONUS_MODAL',
    SET_LIST: 'SET_LIST_BONUS',
}

export const ACTION = {
    LOAD_BONUS: 'LOAD_BONUS',
}

export default {
    state: {
        modal: false,
        enabled: false,
        size: 0,
        select: null,
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
        // Set list
        [MUTATION.SET_LIST](state, obj) {
            state.list = obj.list
            state.size = obj.size
        },
    },
    actions: {
        // Load list
        [ACTION.LOAD_BONUS](context) {
            WS.callRPC('bonus/load', {}, (obj) => {
                context.commit(MUTATION.SET_LIST, obj)
            })
        },
    },
    getters: {},
}
