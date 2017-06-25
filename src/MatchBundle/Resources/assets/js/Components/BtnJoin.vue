<template>
    <span
        @click="joinOrLeave"
        class="button round small-12 large-2 opentip"
        :data-tip="tip"
        :class="{ disabled: (!loaded || disabled || loading), alert: joined }">
        <i :class="{ 'fa fa-spin fa-circle-o-notch': loading }"></i> {{ name }}
    </span>
</template>
<script>
    import { mapState } from 'vuex'
    import store from '../Stores/WaitingStore'

    export default {
        props: {
            joinName: { type: String, default: 'Join' },
            joinDesc: { type: String, default: 'Join the game' },
            leaveName: {type: String, default: 'Leave'},
            leaveDesc: {type: String, default: 'Leave the game'},
        },
        data() {
            return {
                loading: false,
            }
        },
        computed: {
            ...mapState([
                'joined',
                'loaded',
                'game',
                'players'
            ]),
            name() {
                return (this.joined) ? this.leaveName : this.joinName
            },
            tip() {
                if (this.joined) {
                    return `<strong>${this.leaveName} :</strong>${this.leaveDesc}`
                } else {
                    return `<strong>${this.joinName} :</strong>${this.joinDesc}`
                }
            },
            disabled() {
                return (!this.joined && this.players.length >= this.game.max) || this.loading
            }
        },
        methods: {
            joinOrLeave() {
                if (!this.loaded || this.disabled) {
                    return;
                }
                this.loading = true
                this.loaded = false

                WS.callRPC('wait/join', {join: !this.joined}, (obj) => {
                    this.loading = false
                    if (obj.success) {
                        let action = (this.joined) ? 'LEAVE' : 'JOIN'
                        store.commit(action)
                    }
                    if (obj.msg) {
                        return Flash.error(obj.msg)
                    }
                })
            }
        }
    }
</script>