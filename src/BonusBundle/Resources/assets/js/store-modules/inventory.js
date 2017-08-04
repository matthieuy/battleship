/**
 * Inventory module
 */

import { ACTION, MUTATION } from "@match/js/match/store/mutation-types"

export default {
    state: {
        modal: false,
        enabled: false,
        size: 0,
        select: null,
        nb: 0,
        team: null,
        list: [],
    },
    mutations: {
        // On first load game
        [MUTATION.LOAD](state, obj) {
            state.enabled = obj.options.bonus
            obj.players.some(function(player) {
                if (player.me) {
                    state.nb = player.nbBonus
                    state.team = player.team
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
    },
    actions: {
        // Load list
        [ACTION.INVENTORY.LOAD](context) {
            WS.callRPC('bonus/load', {}, (obj) => {
                context.commit(MUTATION.INVENTORY.SET_LIST, obj)
            })
        },
        // After each rocket
        [ACTION.AFTER_ROCKET](context, box) {
            // Update inventory
            if (typeof box.bonus !== 'undefined' && typeof box.bonus[context.rootState.me.position] !== 'undefined') {
                context.commit(MUTATION.INVENTORY.SET_NB, box.bonus[context.rootState.me.position])
                delete box.life
            }
        },
        // Use bonus
        [ACTION.INVENTORY.USE](context, bonus) {
            // Get data to send
            let dataSend = { id: bonus.id }
            if (bonus.options.player) {
                Object.assign(dataSend, {
                    player: bonus.options.player,
                })
            }

            // send RPC
            WS.callRPC('bonus/useit', dataSend, (obj) => {
                if (obj.msg) {
                    return alert(obj.msg)
                }
                context.commit(MUTATION.INVENTORY.MODAL, false)
            })
        },
    },
    getters: {},
}
