// Imports
import { ACTION, MUTATION } from "@match/js/match/store/mutation-types"

export default {
    state: {
        unread: 0,
        modal: true,
        active_tab: 'general',
        open_tab: [{id:1, name:'User1'}, {id:25, name:'User2'}]
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
        // Change active tab
        [MUTATION.CHAT.CHANGE_TABS](state, tab) {
            state.active_tab = tab
        },
        // Close tab
        [MUTATION.CHAT.CLOSE_TABS](state, tabId) {
            if (state.active_tab === tabId) {
                state.active_tab = "general"
            }

            let open_tab = []
            state.open_tab.forEach(function(tab, i) {
                if (tab.id !== tabId) {
                    open_tab.push(tab)
                }
            })
            state.open_tab = open_tab
        },
    },
    getters: {},
    actions: {},
}
