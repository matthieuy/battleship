<template>
    <span
        v-if="isCreator"
        @click="run"
        :data-tip="tip"
        :class="btnClass"
        class="button success round small-12 large-2 opentip">
        <i :class="{ 'fa fa-spin fa-circle-o-notch': loading }"></i> {{ name }}
    </span>
</template>

<script>
    import { mapState } from 'vuex'
    import store from '../store/store'
    import * as types from "../store/mutation-types"

    export default {
        props: {
            name: {type: String, default: 'Start'},
            desc: {type: String, default: 'Once everyone is ready<br>and configured options: you can start the game!'},
        },
        data() {
            return {
                loading: false,
                disabled: true,
            }
        },
        computed: {
            ...mapState([
                'game',
                'isCreator',
                'players',
                'loaded',
            ]),
            tip() {
                return `<strong>${this.name} :</strong>${this.desc}`
            },
            btnClass() {
                this.disabled = this.players.length < 2 || this.game.max < this.players.length || this.loading
                return {
                    disabled: this.disabled || this.loading || !this.loaded
                }
            },
        },
        methods: {
            run() {
                if (this.disabled) {
                    return false
                }

                // Count team
                let teamList = {}
                this.players.forEach((player) => {
                    teamList[player.team] = 1
                })
                let nbTeam = Object.size(teamList)

                // Check team
                if (nbTeam <= 1) {
                    return Flash.error('The game must consist with SEVERAL teams')
                }
                for(let i=1; i<=nbTeam; i++) {
                    if (typeof teamList[i] == 'undefined') {
                        return Flash.error('Nobody in team #'+i+' !')
                    }
                }

                // Any human player ?
                if ($('#playerlist tbody .fa-gamepad').length === 0) {
                    return Flash.error('Game contains only AI!')
                }

                // Confirm ?
                if (!window.confirm('Are you sure to start the game ?')) {
                    return false
                }

                this.loading = true
                store.dispatch(types.ACTION.RUN).then((obj) => {
                    this.loading = false
                })
            }
        }
    }

    Object.size = function(obj) {
        let size = 0, key
        for (key in obj) {
            if (obj.hasOwnProperty(key)) {
                size++
            }
        }
        return size
    }
</script>
