<template>
    <div :class="{ 'has-error': error }">
        <label for="max-player" class="required">{{ label }} :</label>
        <input v-model="nb_player" id="max-player" type="range" required="required" class="small-12 max-player" min="2" max="12">
        <div class="clear"></div>
    </div>
</template>
<script>
  /* global $ */
  let regNum = new RegExp('^([0-9]*)$', 'g')
  export default {
    props: ['trans'],
    data () {
      return {
        error: false,
        nb_player: 4,
      }
    },
    computed: {
      label () {
        return this.trans.replace('%nb%', this.error ? '' : this.nb_player)
      },
    },
    watch: {
      nb_player (value) {
        this.error = !value.toString().match(regNum) || !value.length
        if (!this.error) {
          $('.hidden-maxplayer').val(this.nb_player)
        }
      },
    },
  }
</script>
<style lang="less">
    input[type=range] {
        border: 1px solid white;
    }
</style>
