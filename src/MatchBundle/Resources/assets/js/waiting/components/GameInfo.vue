<template>
    <table class="small-12 extendable">
        <thead class="cursor">
            <tr>
                <th colspan="2">
                    {{ trans('Informations') }}
                    <div class="fa caret"></div>
                </th>
            </tr>
        </thead>
        <tbody v-show="loaded">
            <tr>
                <td>{{ trans('Name of the game', {}, 'form') }} :</td>
                <td>{{ game.name }}</td>
            </tr>
            <tr>
                <td>{{ trans('gridsize') }} :</td>
                <td v-if="!isCreator">{{ game.size }} x {{ game.size }}</td>
                <td v-if="isCreator">
                    <select v-model="size">
                        <option value="15">15 x 15 ({{ trans('nb_players', {nb: 2}, 'form') }})</option>
                        <option value="20">20 x 20 ({{ trans('nb_players', {nb: 3}, 'form') }})</option>
                        <option value="25">25 x 25 ({{ trans('nb_players', {nb: '4/5'}, 'form') }})</option>
                        <option value="30">30 x 30 ({{ trans('nb_players', {nb: '6/7'}, 'form') }})</option>
                        <option value="35">35 x 35 ({{ trans('nb_players', {nb: 8}, 'form') }})</option>
                        <option value="40">40 x 40 ({{ trans('nb_players', {nb: 9}, 'form') }})</option>
                        <option value="45">45 x 45 ({{ trans('nb_players', {nb: 10}, 'form') }})</option>
                        <option value="50">50 x 50 ({{ trans('XXL') }})</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>{{ trans('create_date') }} :</td>
                <td>{{ date }}</td>
            </tr>
            <tr>
                <td>{{ trans('Creator') }} :</td>
                <td>
                    {{ game.creatorName }}
                </td>
            </tr>
            <tr>
                <td>{{ trans('Players') }} :</td>
                <td>
                    {{ players.length }} /
                    <span v-if="isCreator">
                        <input type="number" min="2" max="12" v-model="max">
                    </span>
                    <span v-if="!isCreator">{{ game.max }}</span>
                </td>
            </tr>
            <tr>
                <td>{{ trans('Status') }} :</td>
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
        data() {
            return {
                trans() {
                    return Translator.trans(...arguments)
                },
            }
        },
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
