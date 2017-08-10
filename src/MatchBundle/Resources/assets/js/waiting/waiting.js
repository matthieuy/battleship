/* global WS, Translator */

// Imports
import Vue from 'vue'
import store from './store/store'
import * as types from './store/mutation-types'
import BtnAi from './components/BtnAi.vue'
import BtnJoin from './components/BtnJoin.vue'
import BtnRun from './components/BtnRun.vue'
import GameInfo from './components/GameInfo.vue'
import GameOptions from './components/GameOptions.vue'
import PlayersList from './components/PlayersList.vue'
import Console from './components/Console.vue'
import ChatBtn from '@chat/js/components/ChatBtn.vue'
import ChatModal from '@chat/js/components/ChatModal.vue'

// Require JS libs
require('@app/js/table.js')
require('@app/js/tooltips.js')
require('@npm/jquery-ui/ui/core.js')
require('@npm/jquery-ui/ui/widget.js')
require('@npm/jquery-ui/ui/widgets/mouse.js')
require('@npm/jquery-ui/ui/widgets/sortable.js')

// Store init
store.commit(types.MUTATION.SET_USERID, document.getElementById('user-id').value)

// App vue
new Vue({ // eslint-disable-line no-new
  el: '#vue',
  store,
  components: {
    BtnAi,
    BtnJoin,
    BtnRun,
    GameInfo,
    GameOptions,
    PlayersList,
    Console,
    ChatModal,
  },
})

// Top button
new Vue({ // eslint-disable-line no-new
  el: '#btn-top',
  store,
  components: {
    ChatBtn,
  },
})

// Document.ready
/* global $ */
$(() => {
  // Socket
  let slug = document.getElementById('slug').value
  let topicName = `game/${slug}/wait`
  WS.addDefaultData('slug', slug)

  // Socket subscribe
  WS.subscribeAction(topicName, 'players', (players) => store.commit(types.MUTATION.SET_PLAYERS, players))
  WS.addAction(topicName, 'infos', (infos) => store.commit(types.MUTATION.SET_GAMEINFO, infos))
  WS.addAction(topicName, 'reload', function () {
    window.setTimeout(function () {
      window.location.reload()
    }, 5000)
  })

  // Socket connect
  WS.connect()

  // Delete button
  $(document).on('click', '#btn-delete', function () {
    return window.confirm(Translator.trans('ask_delete_game'))
  })
})
