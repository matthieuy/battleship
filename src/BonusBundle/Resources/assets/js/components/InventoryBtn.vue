<template>
    <div :title="trans('Inventory')" class="bubble" :class="{disabled: !inventory.enabled}" @click="toggleModal()">
        <i class="gi gi-backpack"></i>
        <img id="inventory-bubble" src="img/null.png" width="25" height="25">
    </div>
</template>
<script>
  // Import
  import {mapState} from 'vuex'
  import {ACTION, MUTATION} from '@match/js/match/store/mutation-types'
  /* global Translator, WS */

  // Bower
  import Favico from '@npm/favico.js/favico.js'

  let BubbleInventory = null

  export default {
    data () {
      return {
        loaded: false,
        trans () {
          return Translator.trans(...arguments)
        },
      }
    },
    computed: {
      ...mapState([
        'inventory', // Inventory module
      ]),
    },
    methods: {
      toggleModal () {
        if (this.inventory.enabled) {
          this.$store.commit(MUTATION.INVENTORY.MODAL)
        }
      },
    },
    watch: {
      // Change number of bonus
      'inventory.nb': function (nb) {
        BubbleInventory.badge(nb)
      },
      // Websocket on first load
      'inventory.enabled': function (enable) {
        if (enable && !this.loaded) {
          this.loaded = true
          let topicName = 'game/' + document.getElementById('slug').value + '/bonus'
          WS.subscribeAction(topicName, 'score', (obj) => {
            this.$store.dispatch(ACTION.AFTER_ROCKET, {score: obj})
          })
          WS.addAction(topicName, 'bonus', (obj) => {
            this.$store.dispatch(ACTION.AFTER_ROCKET, {bonus: obj})
          })
        }
      },
    },
    mounted () {
      BubbleInventory = new Favico({
        elementId: 'inventory-bubble',
        animated: 'popFade',
        bgColor: '#5825ff',
      })
    },
  }
</script>
