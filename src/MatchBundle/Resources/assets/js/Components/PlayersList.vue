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
        <tbody>
            <tr v-for="player in players" class="player-line" :data-id="player.id" :key="player.id">
                <td class="opentip move"
                    :data-tip="(player.ai) ? 'IA' : 'Player'"
                    :style="txtColor(player.color)">
                    <i class="fa" :class="{ 'fa-desktop': player.ai, 'fa-gamepad': !player.ai }"></i>
                </td>
                <td></td>
                <td>
                    {{ player.name }}
                    <div class="color-div" v-show="isEditable(player)">
                        <input @change="changeColor($event, player)" type="color" class="color" title="Change color" :value="'#' + player.color">
                    </div>
                </td>
                <td>
                    <select :value="player.team" @change="changeTeam($event, player)">
                        <option v-for="n in 12">{{ n }}</option>
                    </select>
                    <span
                        class="delete"
                        title="Remove the player"
                        v-show="isEditable(player)"
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
    import store from '../Stores/WaitingStore'

    export default {
        computed: {
            ...mapState([
                'players',
                'loaded',
                'isCreator',
            ]),
        },
        watch: {
            // Watch creator to enable/disable sortable list
            isCreator(newValue) {
                if (newValue) {
                    $('#playerlist tbody').sortable('enable')
                } else {
                    $('#playerlist tbody').sortable('disable')
                }
            },
            // Watch players list to enable/disable sortable list
            players(newPlayersList) {
                if (this.isCreator && newPlayersList.length > 1) {
                    $('#playerlist tbody').sortable('enable')
                } else {
                    $('#playerlist tbody').sortable('disable')
                }
            }
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
            // Is this player line is editable
            isEditable(player) {
                return (store.state.isCreator || player.userId === store.state.userId)
            },
            // Change team
            changeTeam(e, player) {
                this.loaded = false
                WS.callRPC('wait/team', {
                    playerId: player.id,
                    team: e.target.value
                })
            },
            // Change color
            changeColor(e, player) {
                this.loaded = false
                WS.callRPC('wait/color', {
                    playerId: player.id,
                    color: e.target.value
                })
            },
            // Remove a player
            remove(e, player) {
                e.target.innerHTML = '<i class="fa fa-spin fa-spinner"></i>'
                this.loaded = false
                WS.callRPC('wait/join', {join: false, id: player.id}, function(obj) {
                    e.target.innerHTML = '&times;'
                    if (obj.msg) {
                        return Flash.error(obj.msg)
                    }
                })
            },
        },
        mounted() {
            $('#playerlist tbody').sortable({
                axis: "y",
                forceHelperSize: true,
                forcePlaceholderSize: true,
                placeholder: 'ui-placeholder',
                helper: function(e, ui) {
                    ui.children().each(function() {
                        $(this).width($(this).width())
                    });
                    return ui
                },
                handle: 'td.move',
                start: function(e, ui) {
                    $('.ui-placeholder').height($(ui.item).height());
                },
                update: function(e, ui) {
                    WS.callRPC('wait/position', {
                        playerId: ui.item.data('id'),
                        position: ui.item.index('#playerlist tbody tr:visible')
                    })
                    return ui
                },
                scroll: false,
                cursor: 'move'
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