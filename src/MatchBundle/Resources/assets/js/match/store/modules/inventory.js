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
        nb: 0,
        list: [],
    },
    mutations: {
        // On first load game
        [MUTATION.LOAD](state, obj) {
            state.enabled = obj.options.bonus
            obj.players.some(function(player) {
                if (player.me) {
                    state.nb = player.nbBonus
                }
                return (typeof player.me != 'undefined')
            })
        },
        // Toggle modal
        [MUTATION.INVENTORY.MODAL](state, status) {
            if (typeof status !== "undefined") {
                state.modal = status
            } else {
                state.modal = !state.modal
            }
        },
        // Set number
        [MUTATION.INVENTORY.SET_NB](state, nb) {
            state.nb = nb
        },
        // Set list
        [MUTATION.INVENTORY.SET_LIST](state, obj) {
            state.list = obj.list
            state.size = obj.size
            state.nb = obj.list.length
        },
        // After each rocket
        [ACTION.AFTER_ROCKET](context, box) {
            // Update inventory
            if (box.bonus && box.bonus[context.rootState.me.position]) {
                context.commit(MUTATION.INVENTORY.SET_NB, box.bonus[context.rootState.me.position])
                delete box.life
            }
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
        },
    },
    getters: {},
}
