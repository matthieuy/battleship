<template>
    <div :class="{ 'has-error': error }">
        <label for="max-player" class="required">{{ label }} :</label>
        <input v-model="nb_player" id="max-player" type="range" required="required" class="small-12 max-player" min="2" max="12">
        <div class="clear"></div>
    </div>
</template>
<script>
    let regNum = new RegExp('^([0-9]*)$', 'g')
    export default {
        props: ['trans'],
        data() {
            return {
                label: '',
                error: false,
                nb_player: 4
            }
        },
        computed: {
            label() {
                return this.trans.replace('%d', this.error ? '' : this.nb_player)
            }
        },
        watch: {
            nb_player(value) {
                this.error = !this.nb_player.match(regNum) || !value.length
                if (!this.error) {
                    $('.hidden-maxplayer').val(this.nb_player)
                }
            }
        }
    }
</script>