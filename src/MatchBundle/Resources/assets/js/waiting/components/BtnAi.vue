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
    import store from '../store/store'
    import * as types from "../store/mutation-types"

    export default{
        props: {
            name: {type: String, default: 'Add a AI'},
            desc: {type: String, default: 'Add a player managed by the computer.'},
        },
        data() {
            return {
                loading: false,
                disabled: true,
            }
        },
        computed: {
            ...mapState([
                'loaded',
                'game',
                'players',
                'isCreator',
            ]),
            tip() {
                return `<strong>${this.name} :</strong> ${this.desc}`
            },
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

                store.dispatch(types.ACTION.ADD_AI).then((obj) => {
                    this.loading = false
                    if (obj.msg) {
                        return Flash.error(obj.msg)
                    }
                })
            },
        },
    }
</script>
