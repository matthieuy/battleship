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
    getters: {},
}
