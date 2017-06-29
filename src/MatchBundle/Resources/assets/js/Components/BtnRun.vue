<template>
    <span
        v-if="isCreator"
        @click="run"
        class="button success round small-12 large-2 opentip"
        :class="{ disabled: (!loaded || disabled || loading) }"
        :data-tip="tip">
        <i :class="{ 'fa fa-spin fa-circle-o-notch': loading }"></i> {{ name }}
    </span>
</template>

<script>
    import { mapState } from 'vuex'
    import store from '../Stores/WaitingStore'

    export default {
        props: {
            name: {type: String, default: 'Start'},
            desc: {type: String, default: 'Once everyone is ready<br>and configured options: you can start the game!'},
        },
        data() {
            return {
                loading: false,
            }
        },
        computed: {
            ...mapState([
                'isCreator',
                'players',
                'loaded',
            ]),
            tip() {
                return `<strong>${this.name} :</strong>${this.desc}`
            },
            disabled() {
                return (this.players.length < 2) || this.loading
            }
        },
        methods: {
            run() {
                if (!this.loaded || !this.isCreator || this.disabled) {
                    return;
                }

                // Count team
                var teamList = {}
                this.players.forEach((player) => {
                    teamList[player.team] = 1
                })
                var nbTeam = Object.size(teamList)

                // Check team
                if (nbTeam <= 1) {
                    return Flash.error('The game must consist with SEVERAL teams')
                }
                for(var i=1; i<=nbTeam; i++) {
                    if (typeof teamList[i] == 'undefined') {
                        return Flash.error('Nobody in team #'+i+' !')
                    }
                }

                // Any human player ?
                if ($('#playerlist tbody .fa-gamepad').length === 0) {
                    return Flash.error('Game contains only AI!');
                }

                // Confirm ?
                if (!window.confirm('Are you sure to start the game ?')) {
                    return;
                }

                this.loading = true
                WS.callRPC('launch/run', {}, (obj) => {
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
