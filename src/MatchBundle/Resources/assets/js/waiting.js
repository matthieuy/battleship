require('@app/js/table.js')
require('@app/js/tooltips.js')
require('@bower/jquery-ui/ui/core.js')
require('@bower/jquery-ui/ui/widget.js')
require('@bower/jquery-ui/ui/widgets/mouse.js')
require('@bower/jquery-ui/ui/widgets/sortable.js')

// Imports
import Vue from "vue"
import store from "./Stores/WaitingStore"
import BtnJoin from "./Components/BtnJoin.vue"
import GameInfo from "./Components/GameInfo.vue"
import GameOptions from "./Components/GameOptions.vue"
import PlayersList from "./Components/PlayersList.vue"
import BtnAI from "./Components/BtnAi.vue"
import BtnRun from "./Components/BtnRun.vue"

// App vue
new Vue({
    el: '#vue',
    store,
    components: {
        btnJoin: BtnJoin,
        gameInfo: GameInfo,
        playersList: PlayersList,
        btnAi: BtnAI,
        gameOptions: GameOptions,
        btnRun: BtnRun,
    }
})

// Document.ready
$(() => {
    // Store init
    store.commit('SET_USERID', document.getElementById('user_id').value)
    // Socket
    let slug = document.getElementById('slug').value
    let topicName = 'game/' + slug + '/wait'
    WS.addDefaultData('slug', slug)

    // Socket subscribe
    WS.subscribeAction(topicName, 'players', (players) => store.commit('SET_PLAYERS', players))
    WS.subscribeAction(topicName, 'infos', (infos) => store.commit('SET_GAME_INFOS', infos))
    WS.subscribeAction(topicName, 'reload', () => window.location.reload())

    // Socket connect
    WS.connect()
}); // => doc.ready()