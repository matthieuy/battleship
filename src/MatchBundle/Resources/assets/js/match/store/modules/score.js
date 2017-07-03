/**
 * Score module vuex store
 */

export const MUTATION = {
    SCORE_MODAL: 'SCORE_MODAL',
}

export default {
    state: {
        modal: false,
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
    },
    actions: {},
    getters: {},
}
