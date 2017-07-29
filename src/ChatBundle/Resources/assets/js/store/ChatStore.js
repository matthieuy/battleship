// Imports
import { ACTION, MUTATION } from "@match/js/match/store/mutation-types"

export default {
    state: {
        unread: 0,
        modal: true,
    },
    mutations: {
        // Toggle modal
        [MUTATION.CHAT.MODAL](state, status) {
            if (typeof status !== "undefined") {
                state.modal = status
            } else {
                state.modal = !state.modal
            }
        },
    },
    getters: {},
    actions: {},
}
