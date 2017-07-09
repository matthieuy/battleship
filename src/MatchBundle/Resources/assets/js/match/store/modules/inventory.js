/**
 * Inventory module
 */

import { ACTION, MUTATION } from "../mutation-types"

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
        [MUTATION.LOAD](state, obj) {
            state.enabled = obj.options.bonus
        },
        // Toggle modal
        [MUTATION.INVENTORY.MODAL](state, status) {
            if (typeof status !== "undefined") {
                state.modal = status
            } else {
                state.modal = !state.modal
            }
        },
        // Set list
        [MUTATION.INVENTORY.SET_LIST](state, obj) {
            state.list = obj.list
            state.size = obj.size
        },
    },
    actions: {
        // Load list
        [ACTION.INVENTORY.LOAD](context) {
            WS.callRPC('bonus/load', {}, (obj) => {
                context.commit(MUTATION.INVENTORY.SET_LIST, obj)
            })
        },
        // Use bonus
        [ACTION.INVENTORY.USE](context, bonusId) {
            WS.callRPC('bonus/useit', {id: bonusId}, (obj) => {
                if (obj.msg) {
                    return alert(obj.msg)
                }
            })
        }
    },
    getters: {},
}
