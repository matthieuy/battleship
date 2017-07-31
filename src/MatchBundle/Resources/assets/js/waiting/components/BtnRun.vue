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
    import * as types from "../store/mutation-types"

    export default {
        data() {
            return {
                loading: false,
                disabled: true,
                name: Translator.trans('btn_run'),
                tip: `<strong>${Translator.trans('btn_run')} :</strong>${Translator.trans('btn_run_tip').replace('\n', '<br>')}`,
            }
        },
        computed: {
            ...mapState([
                'game',
                'isCreator',
                'players',
                'loaded',
            ]),
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
                    return Flash.error('error_team')
                }
                for(let i=1; i<=nbTeam; i++) {
                    if (typeof teamList[i] === 'undefined') {
                        return Flash.error(Translator.trans('error_empty_team', {team: i}))
                    }
                }

                // Any human player ?
                if ($('#playerlist tbody .fa-gamepad').length === 0) {
                    return Flash.error('error_ai')
                }

                // Confirm ?
                if (!window.confirm(Translator.trans('confirm_start'))) {
                    return false
                }

                this.loading = true
                this.$store.dispatch(types.ACTION.RUN).then((obj) => {
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
