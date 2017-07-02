// Require JS libs
require('@app/js/tooltips.js')

// Import
import Vue from "vue"
import store from "./store/GameStore"
import Grid from "./components/Grid.vue"
import BtnScore from "./components/BtnScore.vue"
import WeaponModal from "./components/WeaponModal.vue"
import WeaponBtn from "./components/WeaponBtn.vue"
import * as types from "./store/mutation-types"

// App vue
new Vue({
    el: '#vue',
    store,
    components: {
        Grid,
        WeaponModal,
    },
})

// Top button
new Vue({
    el: '#btn-top',
    store,
    components: {
        BtnScore,
        WeaponBtn,
    },
})

// Document.ready
$(() => {
    // Store init
    store.commit(types.MUTATION.SET_USERID, document.getElementById('user-id').value)

    // Socket
    WS.addDefaultData('slug', document.getElementById('slug').value)
    WS.connect()

    // Load game
    store.dispatch(types.ACTION.LOAD_GAME).then((game) => {
        store.commit(types.MUTATION.LOAD, game)
    })
})
