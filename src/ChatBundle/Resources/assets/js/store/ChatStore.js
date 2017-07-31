// Imports
import { ACTION, MUTATION } from "@match/js/match/store/mutation-types"
import db from "../database"

export default {
    state: {
        disabled: (typeof localStorage === 'undefined' || typeof indexedDB === 'undefined'),
        unread: 0,
        modal: false,
        active_tab: 'general',
        open_tab: [],
        messages: [],
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
            if (tab.name && !state.open_tab.filter((t) => t.id === tab.id).length) {
                state.open_tab.push(tab)
            }
            state.active_tab = tab.id
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
        // Receive message
        [MUTATION.CHAT.RECEIVE](state, messages) {
            let localKey = 'chat_' + document.getElementById('slug').value + '_id'
            let lastId = localStorage.getItem(localKey) || 0

            messages.forEach(function(message) {
                // Message infos
                let infos = {
                    id_message: message.id,
                    game:  message.game,
                    timestamp: message.timestamp,
                    text: message.text,
                }

                // Author
                if (typeof message.author !== 'undefined') {
                    infos = Object.assign(infos, {
                        author_id: message.author.id,
                        author_name: message.author.name,
                    })
                } else if (typeof message.context !== 'undefined') {
                    infos = Object.assign(infos, { context: message.context })
                }

                // Channel and recipient
                if (typeof message.channel !== 'undefined') {
                    infos = Object.assign(infos, {
                        channel: message.channel,
                        recipient: message.recipient,
                    })
                }

                // Add message into DB
                db.messages.add(infos)
                state.messages.push(infos)

                // Update last ID
                if (message.id > lastId) {
                    lastId = message.id
                    localStorage.setItem(localKey, lastId)
                }
            })
        },
        // Add message
        [MUTATION.CHAT.ADD_MESSAGE](state, message) {
            state.messages.push(message)
        },
    },
    getters: {},
    actions: {
        // Load chat
        [ACTION.CHAT.LOAD](context) {
            db.messages
                .where('game')
                .equals(Number(document.getElementById('game-id').value))
                .sortBy('timestamp', function(messages) {
                    messages.forEach(function(message) {
                        context.commit(MUTATION.CHAT.ADD_MESSAGE, message)
                    })
                })

        },
    },
}
