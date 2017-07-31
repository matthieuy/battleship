/**
 * Mutations and actions list for game vuex store
 */

// Mutations
export const MUTATION = {
    SET_USERID: 'SET_USERID',
    LOAD: 'FIRST_LOAD',
    SET_STATUS: 'SET_STATUS',
    AFTER_ROCKET: 'AFTER_ROCKET',
    AFTER_ANIMATE: 'AFTER_ANIMATE',

    WEAPON: {
        MODAL: 'WEAPON_MODAL',
        SET_LIST: 'SET_WEAPON_LIST',
        SELECT: 'SELECT_WEAPON',
        ROTATE: 'ROTATE_WEAPON',
        SET_SCORE: 'SET_SCORE_WEAPON',
    },

    INVENTORY: {
        MODAL: 'BONUS_MODAL',
        SET_LIST: 'SET_LIST_BONUS',
        SET_NB: 'SET_NB_BONUS',
    },

    SCORE: {
        MODAL: 'SCORE_MODAL',
        SET_LIST: 'SET_LIST',
        SET_LIFE: 'SET_LIFE',
    },

    CHAT: {
        MODAL: 'CHAT_MODAL',
        CHANGE_TABS: 'CHAT_TABS',
        CLOSE_TABS: 'CHAT_CLOSE_TABS',
        RECEIVE: 'CHAT_RECEIVE_MSG',
        ADD_MESSAGE: 'CHAT_ADD_MESSAGE',
    },
}

// Actions
export const ACTION = {
    LOAD_GAME: 'LOAD_GAME',
    SHOOT: 'SHOOT',
    BEFORE_SHOOT: 'BEFORE_SHOOT',
    AFTER_ROCKET: 'AFTER_ROCKET',

    WEAPON: {
        LOAD: 'LOAD_WEAPON',
    },

    INVENTORY: {
        LOAD: 'LOAD_BONUS',
        USE: 'USE_BONUS',
    },

    CHAT: {
        LOAD: 'CHAT_LOAD',
    },
}
