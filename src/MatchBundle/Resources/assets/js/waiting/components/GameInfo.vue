<template>
    <table class="small-12 extendable">
        <thead class="cursor">
            <tr>
                <th colspan="2">
                    Informations
                    <div class="fa caret"></div>
                </th>
            </tr>
        </thead>
        <tbody v-show="loaded">
            <tr>
                <td>Name of game :</td>
                <td>{{ game.name }}</td>
            </tr>
            <tr>
                <td>Grid size :</td>
                <td v-if="!isCreator">{{ game.size }} x {{ game.size }}</td>
                <td v-if="isCreator">
                    <select v-model="size">
                        <option value="15">15 x 15 (2 players)</option>
                        <option value="20">20 x 20 (3 players)</option>
                        <option value="25">25 x 25 (4/5 players)</option>
                        <option value="30">30 x 30 (6/7 players)</option>
                        <option value="35">35 x 35 (8 players)</option>
                        <option value="40">40 x 40 (9 players)</option>
                        <option value="45">45 x 45 (10 players)</option>
                        <option value="50">50 x 50 (XXL)</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Create date :</td>
                <td>{{ date }}</td>
            </tr>
            <tr>
                <td>Creator :</td>
                <td>
                    {{ game.creatorName }}
                </td>
            </tr>
            <tr>
                <td>Players :</td>
                <td>
                    {{ players.length }} /
                    <span v-if="isCreator">
                        <input type="number" min="2" max="12" v-model="max">
                    </span>
                    <span v-if="!isCreator">{{ game.max }}</span>
                </td>
            </tr>
            <tr>
                <td>Status :</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</template>

<script>
    // Imports
    import { mapState } from 'vuex'
    import store from '../store/store'
    import * as types from "../store/mutation-types"
    import moment from 'moment'

    export default {
        computed: {
            ...mapState([
                'game',
                'players',
                'isCreator',
                'loaded',
            ]),
            // Get create date
            date() {
                return moment.unix(this.game.date).format('LLL')
            },
            // Get/Set grid size
            size: {
                get() {
                    return this.game.size
                },
                set(size) {
                    if (this.isCreator) {
                        size = Math.min(50, Math.max(15, size))
                        store.dispatch(types.ACTION.CHANGE_SIZE, size)
                    }
                }
            },
            // Get/set max players limit
            max: {
                get() {
                    return this.game.max
                },
                set(max) {
                    if (this.isCreator) {
                        max = Math.min(12, Math.max(2, max))
                        store.dispatch(types.ACTION.CHANGE_MAX, max)
                    }
                }
            },
        },
    }
</script>
