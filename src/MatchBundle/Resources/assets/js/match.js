require('@app/js/tooltips.js')

// Import
import Vue from "vue"
import store from "./Stores/GameStore"
import Grid from "./Components/Grid.vue"
import BtnWeapon from "./Components/BtnWeapon.vue"
import BtnScore from "./Components/BtnScore.vue"

// App vue
new Vue({
    el: '#vue',
    store,
    components: {
        Grid,
    }
})

// Top Button
new Vue({
    el: '#btn-top',
    store,
    components: {
        BtnScore: BtnScore,
        BtnWeapon: BtnWeapon,
    },
})

// Document.ready
$(() => {
    // Store init
    store.commit('SET_USERID', document.getElementById('user_id').value)

    // Socket
    var slug = document.getElementById('slug').value
    WS.addDefaultData('slug', slug)
    WS.connect()

    // Load game
    store.dispatch('load')
}); // => doc.ready()
