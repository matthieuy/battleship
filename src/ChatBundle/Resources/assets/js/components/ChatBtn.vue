<template>
    <div :title="trans('Chat')" class="bubble" :class="{disabled: chat.disabled}" @click="toggleModal()">
        <i class="gi gi-conversation"></i>
        <img id="chat-bubble" src="img/null.png" width="25" height="25">
    </div>
</template>
<script>
  // Import
  import {mapState} from 'vuex'
  import {MUTATION} from '@match/js/match/store/mutation-types'
  /* global Translator */

  // Bower
  import Favico from '@npm/favico.js/favico.js'

  let BubbleChat = []

  export default {
    data () {
      return {
        trans () {
          return Translator.trans(...arguments)
        },
      }
    },
    computed: {
      ...mapState([
        'chat', // Chat module
      ]),
    },
    methods: {
      toggleModal () {
        if (!this.chat.disabled) {
          this.$store.commit(MUTATION.CHAT.MODAL)
        }
      },
    },
    watch: {
      // Change number of unread message
      'chat.unread': function (nb) {
        for (let i = 0; i < BubbleChat.length; i++) {
          BubbleChat[i].badge(nb)
        }
      },
    },
    mounted () {
      BubbleChat.push(new Favico({
        elementId: 'chat-bubble',
        animated: 'pop',
        bgColor: '#000fff',
      }))
      BubbleChat.push(new Favico({
        animated: 'pop',
        bgColor: '#000000',
      }))
    },
  }
</script>
