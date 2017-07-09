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
    },

    INVENTORY: {
        MODAL: 'BONUS_MODAL',
        SET_LIST: 'SET_LIST_BONUS',
    },

    SCORE: {
        MODAL: 'SCORE_MODAL',
        SET_LIST: 'SET_LIST',
    },
}

// Actions
export const ACTION = {
    LOAD_GAME: 'LOAD_GAME',
    SHOOT: 'SHOOT',
    BEFORE_SHOOT: 'BEFORE_SHOOT',

    WEAPON: {
        LOAD: 'LOAD_WEAPON',
    },

    INVENTORY: {
        LOAD: 'LOAD_BONUS',
        USE: 'USE_BONUS',
    },
}
