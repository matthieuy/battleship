<template>
    <table id="playerlist" class="small-12 extendable">
        <thead>
        <tr>
            <th colspan="4">
                Players
                <div class="fa caret"></div>
            </th>
        </tr>
        <tr>
            <th class="center" width="50">Color</th>
            <th width="50"></th>
            <th class="center">Player</th>
            <th class="center" width="100">Team</th>
        </tr>
        </thead>
        <tbody id="sortable-players">
            <tr v-for="player in players" class="player-line" :data-id="player.id" :key="player.id">
                <td class="opentip"
                    :class="{move: isSortable}"
                    :data-tip="(player.ai) ? 'IA' : 'Player'"
                    :style="txtColor(player.color)"
                    >
                    <i class="fa" :class="[player.ai ? 'fa-gamepad' : 'fa-desktop']"></i>
                </td>
                <td></td>
                <td>
                    {{ player.name }}
                    <div class="color-div" v-show="isCreator || player.userId === userId">
                        <input type="color" class="color" title="Change color" :value="'#' + player.color" @change="changeColor($event, player)">
                    </div>
                </td>
                <td>
                    <select :value="player.team" @change="changeTeam($event, player)">
                        <option v-for="n in 12">{{ n }}</option>
                    </select>
                    <span
                        class="delete"
                        title="Remove the player"
                        v-show="isCreator || player.userId === userId"
                        @click="remove($event, player)">&times;</span>
                </td>
            </tr>
            <tr v-show="!loaded">
                <td colspan="4" class="center">Loading...</td>
            </tr>
            <tr v-show="players.length == 0 && loaded">
                <td colspan="4" class="center">No players</td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    import { mapState } from 'vuex'
    import store from '../store/store'
    import * as types from "../store/mutation-types"

    export default {
        computed: {
            ...mapState([
                'players',
                'loaded',
                'isCreator',
                'userId',
            ]),
            isSortable() {
                return this.isCreator && this.players.length > 1
            },
        },
        methods: {
            // Calcul text color for player line
            txtColor(playerColor) {
                let rgb = hexToRgb(playerColor)
                let textColor = 0.213 * rgb[0] + 0.715 * rgb[1] + 0.072 * rgb[2]
                return {
                    backgroundColor: '#' + playerColor,
                    color: (textColor < 127.5) ? '#FFF' : '#000'
                }
            },
            // Change player team
            changeTeam(e, player) {
                this.loaded = false
                store.dispatch(types.ACTION.CHANGE_TEAM, {
                    playerId: player.id,
                    team: e.target.value,
                })
            },
            // Change player color
            changeColor(e, player) {
                this.loaded = false
                store.dispatch(types.ACTION.CHANGE_COLOR, {
                    playerId: player.id,
                    color: e.target.value,
                })
            },
            // Remove a player
            remove(e, player) {
                this.loaded = false
                e.target.innerHTML = '<i class="fa fa-spin fa-spinner"></i>'
                store.dispatch(types.ACTION.REMOVE_PLAYER, player.id).then((obj) => {
                    e.target.innerHTML = '&times;'
                    if (obj.msg) {
                        return Flash.error(obj.msg)
                    }
                })
            }
        },
        watch: {
            // Watcher to enable/disable sortable list
            isSortable(newValue) {
                if (newValue) {
                    $('#playerlist tbody').sortable('enable')
                } else {
                    $('#playerlist tbody').sortable('disable')
                }
            },
        },
        mounted() {
            $('#playerlist tbody').sortable({
                axis: "y",
                forceHelperSize: true,
                forcePlaceholderSize: true,
                placeholder: 'ui-placeholder',
                scroll: false,
                cursor: 'move',
                helper: function(e, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width())
                    })
                    return ui
                },
                handle: 'td.move',
                start: function(e, ui) {
                    $('.ui-placeholder').height($(ui.item).height())
                },
                update: function(e, ui) {
                    store.dispatch(types.ACTION.UPDATE_ORDER, {
                        playerId: ui.item.data('id'),
                        position: ui.item.index('#playerlist tbody tr:visible')
                    })
                    return ui
                },
            })
        },
    }

    /**
     * Convert hexa color to rgb
     * @param hex
     * @returns {Array}
     */
    function hexToRgb(hex) {
        let bigint = parseInt(hex, 16)
        let r = (bigint >> 16) & 255
        let g = (bigint >> 8) & 255
        let b = bigint & 255

        return [r, g, b]
    }
</script>
