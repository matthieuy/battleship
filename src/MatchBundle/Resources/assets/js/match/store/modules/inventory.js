/**
 * Inventory module
 */

export const MUTATION = {
    ROOT: require('../mutation-types').MUTATION,
    BONUS_MODAL: 'BONUS_MODAL',
    SET_LIST: 'SET_LIST_BONUS',
}

export const ACTION = {
    LOAD_BONUS: 'LOAD_BONUS',
    USE_BONUS: 'USE_BONUS',
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
        [MUTATION.ROOT.LOAD](state, obj) {
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
        [MUTATION.SET_LIST](state, obj, rootState) {
            state.list = obj.list
            state.size = obj.size
            console.log(rootState.me)
        },
    },
    actions: {
        // Load list
        [ACTION.LOAD_BONUS](context) {
            WS.callRPC('bonus/load', {}, (obj) => {
                context.commit(MUTATION.SET_LIST, obj)
            })
        },
        // Use bonus
        [ACTION.USE_BONUS](context, bonusId) {
            WS.callRPC('bonus/useit', {id: bonusId}, (obj) => {
                if (obj.msg) {
                    return alert(obj.msg)
                }
            })
        }
    },
    getters: {},
}
