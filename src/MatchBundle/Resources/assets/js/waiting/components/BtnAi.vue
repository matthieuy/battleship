<template>
    <span
        v-if="isCreator"
        @click="addAI"
        :data-tip="tip"
        :class="btnClass"
        class="button round small-12 large-2 opentip">
        <i :class="{ 'fa fa-spin fa-circle-o-notch': loading }"></i> {{ name }}
    </span>
</template>
<script>
    import { mapState } from 'vuex'
    import * as types from "../store/mutation-types"

    export default{
        data() {
            return {
                loading: false,
                disabled: true,
                name: Translator.trans('btn_ai'),
                tip: `<strong>${Translator.trans('btn_ai')} :</strong> ${Translator.trans('btn_ai_tip')}`,
            }
        },
        computed: {
            ...mapState([
                'loaded',
                'game',
                'players',
                'isCreator',
            ]),
            btnClass() {
                this.disabled = (!this.isCreator || this.players.length >= this.game.max)
                return {
                    disabled: this.disabled || this.loading || !this.loaded
                }
            },
        },
        methods: {
            // Add a AI
            addAI() {
                if (this.disabled) {
                    return false
                }

                this.loading = true
                this.loaded = false

                this.$store.dispatch(types.ACTION.ADD_AI).then((obj) => {
                    this.loading = false
                    if (obj.msg) {
                        return Flash.error(obj.msg)
                    }
                })
            },
        },
    }
</script>
