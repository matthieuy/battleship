<template>
    <span
        v-if="isCreator"
        @click="addAI"
        :data-tip="tip"
        :class="{ disabled: (!loaded || disabled || loading) }"
        class="button round small-12 large-2 opentip">
        <i :class="{ 'fa fa-spin fa-circle-o-notch': loading }"></i> {{ name }}
    </span>
    <!--
    <span id="btn-ai" class="disabled"
    data-tip="<strong>{{ 'ai.add'|trans }} :</strong>{{ 'ai.add_desc'|trans }}"><i></i> {{ 'ai.add'|trans }}</span>
    -->
</template>
<script>
    import { mapState } from 'vuex'
    import store from '../Stores/WaitingStore'

    export default{
        props: {
            name: {type: String, default: 'Add a AI'},
            desc: {type: String, default: 'Add a player managed by the computer.'},
        },
        data() {
            return {
                loading: false,
            }
        },
        computed: {
            ...mapState([
                'loaded',
                'game',
                'players',
                'isCreator'
            ]),
            tip() {
                return `<strong>${this.name} :</strong> ${this.desc}`
            },
            disabled() {
                return (this.players.length >= this.game.max) || this.loading
            }
        },
        methods: {
            // Add a AI
            addAI() {
                if (!this.loaded || !this.isCreator || this.disabled) {
                    return;
                }
                this.loading = true
                this.loaded = false

                WS.callRPC('wait/join', {join: true, ai: true}, (obj) => {
                    this.loading = false
                    if (obj.msg) {
                        return Flash.error(obj.msg)
                    }
                })
            }
        }
    }
</script>
