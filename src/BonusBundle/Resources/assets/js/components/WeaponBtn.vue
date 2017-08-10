<template>
    <div id="btn-weapon" :title="trans('weapon_name')" class="bubble" :class="{disabled: !weapon.enabled}" @click="toggleModal()">
        <i class="gi gi-crossed-sabres" :class="{selected: weapon.select}"></i>
        <img id="weapon-bubble" src="img/null.png" width="25" height="25">
    </div>
</template>
<script>
  // Import
  import {mapState} from 'vuex'
  import {MUTATION} from '@match/js/match/store/mutation-types'
  /* global Translator */

  // Bower
  import Favico from '@npm/favico.js/favico.js'

  let BubbleWeapon = null

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
        'weapon', // Weapon module
      ]),
    },
    methods: {
      toggleModal () {
        this.$store.commit(MUTATION.WEAPON.MODAL)
      },
    },
    watch: {
      'weapon.score': function (score) {
        if (this.$store.state.weapon.enabled) {
          BubbleWeapon.badge(score)
        }
      },
    },
    mounted () {
      BubbleWeapon = new Favico({
        elementId: 'weapon-bubble',
        animation: 'slide',
      })
    },
  }
</script>
