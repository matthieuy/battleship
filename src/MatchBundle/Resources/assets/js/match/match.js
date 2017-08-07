// Require JS libs
require('@app/js/tooltips.js')
require('@npm/jquery-mobile/js/vmouse.js')

// Import
import Vue from "vue"
import store from "./store/GameStore"
import Grid from "./components/Grid.vue"
import ScoreBtn from "./components/ScoreBtn.vue"
import ScoreModal from "./components/ScoreModal.vue"
import WeaponModal from "@bonus/js/components/WeaponModal.vue"
import WeaponBtn from "@bonus/js/components/WeaponBtn.vue"
import InventoryModal from "@bonus/js/components/InventoryModal.vue"
import InventoryBtn from "@bonus/js/components/InventoryBtn.vue"
import ChatBtn from "@chat/js/components/ChatBtn.vue"
import ChatModal from "@chat/js/components/ChatModal.vue"
import * as types from "./store/mutation-types"

// Store init
store.commit(types.MUTATION.SET_USERID, document.getElementById('user-id').value)

// App vue
new Vue({
    el: '#vue',
    store,
    components: {
        Grid,
        WeaponModal,
        ScoreModal,
        InventoryModal,
        ChatModal,
    },
})

// Top button
new Vue({
    el: '#btn-top',
    store,
    components: {
        ScoreBtn,
        WeaponBtn,
        InventoryBtn,
        ChatBtn,
    },
})

// Document.ready
$(() => {
    // Socket
    WS.addDefaultData('slug', document.getElementById('slug').value)
    WS.connect()

    // Load game
    store.dispatch(types.ACTION.LOAD_GAME).then((game) => {
        store.commit(types.MUTATION.LOAD, game)
    })
})
