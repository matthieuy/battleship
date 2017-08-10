<template>
    <span
            @click="join"
            class="button round small-12 large-2 opentip"
            :class="btnClass"
            :data-tip="tip">
        <i :class="{ 'fa fa-spin fa-circle-o-notch': loading }"></i> {{ name }}
    </span>
</template>
<script>
  import {mapState} from 'vuex'
  import * as types from '../store/mutation-types'
  /* global Translator, Flash */

  export default {
    data () {
      return {
        loading: false,
        disabled: true,
      }
    },
    computed: {
      ...mapState([
        'joined',
        'loaded',
        'game',
        'players',
      ]),
      name () {
        return (this.joined) ? Translator.trans('btn_leave') : Translator.trans('btn_join')
      },
      tip () {
        if (this.joined) {
          return `<strong>${Translator.trans('btn_leave')} :</strong>${Translator.trans('btn_leave_tip')}`
        } else {
          return `<strong>${Translator.trans('btn_join')} :</strong>${Translator.trans('btn_join_tip')}`
        }
      },
      btnClass () {
        this.disabled = !this.loaded || (!this.joined && this.players.length >= this.game.max)
        return {
          alert: this.joined,
          disabled: this.disabled || this.loading,
        }
      },
    },
    methods: {
      // join or leave the game
      join () {
        if (this.disabled) {
          return false
        }

        this.loading = true
        this.loaded = false

        this.$store.dispatch(types.ACTION.JOIN_LEAVE, !this.joined).then((obj) => {
          this.loading = false
          if (obj.success) {
            let mutation = (this.joined) ? types.MUTATION.LEAVE : types.MUTATION.JOIN
            this.$store.commit(mutation)
          }

          if (obj.msg) {
            return Flash.error(obj.msg)
          }
        })
      },
    },
  }
</script>
