require('@app/js/tooltips.js')

// Import
import Vue from "vue"
import store from "./Stores/GameStore"
import Grid from "./Components/Grid.vue"

// App vue
new Vue({
    el: '#vue',
    store,
    components: {
        Grid,
    }
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
