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
    import { mapState } from 'vuex'
    import store from '../store/store'
    import * as types from "../store/mutation-types"

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
                disabled: true,
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
            btnClass() {
                this.disabled = !this.loaded || (!this.joined && this.players.length >= this.game.max)
                return {
                    alert: this.joined,
                    disabled: this.disabled || this.loading
                }
            },
        },
        methods: {
            // join or leave the game
            join() {
                if (this.disabled) {
                    return false;
                }

                this.loading = true
                this.loaded = false

                store.dispatch(types.ACTION.JOIN_LEAVE, !this.joined).then((obj) => {
                    this.loading = false
                    if (obj.success) {
                        let mutation = (this.joined) ? types.MUTATION.LEAVE : types.MUTATION.JOIN
                        store.commit(mutation)
                    }

                    if (obj.msg) {
                        return Flash.error(obj.msg)
                    }
                })
            },
        },
    }
</script>
