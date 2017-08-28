// Imports
import {ACTION, MUTATION} from '@match/js/match/store/mutation-types'
import db from '../database'

export default {
  state: {
    disabled: (typeof localStorage === 'undefined' || typeof indexedDB === 'undefined'),
    unread: 0,
    unread_tab: {
      general: 0,
      team: 0,
    },
    modal: false,
    active_tab: 'general',
    open_tab: [],
    messages: [],
    localkey: '',
  },
  mutations: {
    // Toggle modal
    [MUTATION.CHAT.MODAL] (state, status) {
      if (typeof status !== 'undefined') {
        state.modal = status
      } else {
        state.modal = !state.modal
      }
    },
    // Change active tab
    [MUTATION.CHAT.CHANGE_TABS] (state, tab) {
      if (tab.name && !state.open_tab.filter((t) => t.id === tab.id).length) {
        state.open_tab.push(tab)
      }
      state.active_tab = tab.id
    },
    // Close tab
    [MUTATION.CHAT.CLOSE_TABS] (state, tabId) {
      if (state.active_tab === tabId) {
        state.active_tab = 'general'
      }

      let openTab = []
      state.open_tab.forEach(function (tab, i) {
        if (tab.id !== tabId) {
          openTab.push(tab)
        }
      })
      state.open_tab = openTab
    },
    // Add message
    [MUTATION.CHAT.ADD_MESSAGE] (state, message) {
      state.messages.push(message)
      if (message.unread) {
        // Increase unread number
        state.unread++
        if (typeof state.unread_tab[message.tab] === 'undefined') {
          state.unread_tab = Object.assign({}, state.unread_tab, {[message.tab]: 0})
        }
        state.unread_tab[message.tab]++

        // Open tab
        if (message.tab !== 'general' && message.tab !== 'team' && !state.open_tab.filter((t) => t.id === message.tab).length) {
          state.open_tab.push({
            id: message.tab,
            name: message.author_name,
          })
        }
      }
    },
    // User id listener : set the localkey
    [MUTATION.SET_USERID] (state, userId) {
      state.localkey = 'chat_' + document.getElementById('game-id').value + '_' + userId + '_id'
    },
    // Mark message read (internal use only)
    markReadChat (state, obj) {
      state.unread_tab[obj.tab] = 0
      state.unread = 0
      for (let i in state.unread_tab) {
        state.unread += state.unread_tab[i]
      }
    },
  },
  getters: {},
  actions: {
    // Load chat : add message from indexedDB
    [ACTION.CHAT.LOAD] (context) {
      db.messages
        .where('game')
        .equals(Number(document.getElementById('game-id').value))
        .filter(function (message) {
          return typeof message.user_id === 'undefined' || message.user_id === context.rootState.userId
        })
        .sortBy('timestamp', function (messages) {
          messages.forEach(function (message) {
            context.commit(MUTATION.CHAT.ADD_MESSAGE, message)
          })
        })
    },
    // Receive message
    [ACTION.CHAT.RECEIVE] (context, messages) {
      let localkey = context.state.localkey
      let lastId = localStorage.getItem(localkey) || 0

      messages.forEach(function (message) {
        // Message infos
        let infos = {
          id_message: message.id,
          game: message.game,
          timestamp: message.timestamp,
          text: message.text,
          tab: 'general',
        }

        // Author
        let isSender = false
        if (typeof message.author !== 'undefined') {
          infos = Object.assign(infos, {
            author_id: message.author.id,
            author_name: message.author.name,
          })
          isSender = (context.rootState.userId === message.author.id)
        } else if (typeof message.context !== 'undefined') {
          infos.context = message.context
        }

        // Channel and recipient
        if (typeof message.channel !== 'undefined') {
          infos.tab = (message.channel === 1) ? 'team' : (isSender) ? message.recipient : message.author.id
          infos.user_id = context.rootState.userId
        }

        // Unread
        if (!isSender && message.author && (!context.state.modal || (context.state.modal && context.state.active_tab !== infos.tab))) {
          infos.unread = 1
        }

        // Add message into DB and chatbox
        db.messages.add(infos)
        context.commit(MUTATION.CHAT.ADD_MESSAGE, infos)

        // Update last ID
        if (message.id > lastId) {
          lastId = message.id
          localStorage.setItem(localkey, lastId)
        }
      })
    },
    // Mark as read
    [ACTION.CHAT.MARK_READ] (context, tab) {
      db.messages
        .filter(function (message) {
          return (message.user_id === context.rootState.userId || message.user_id === null) && message.game === Number(document.getElementById('game-id').value) && message.tab === tab && message.unread
        })
        .modify(function (value) {
          delete value.unread
        })
        .then(function (nb) {
          context.commit('markReadChat', {
            nb: nb,
            tab: tab,
          })
        })
    },
  },
}
