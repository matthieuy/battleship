<template>
    <table id="table-options" class="small-12 extendable">
        <thead>
            <tr>
                <th colspan="2">
                    {{ trans('Options') }}
                    <div class="fa caret"></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ trans('Penalty') }}
                    <span class="fa opentip" :data-tip="tips('penalty')"></span> :
                </td>
                <td>
                    <span @click="changeOption('penalty', 0)" v-show="penalty" :class="{cursor: isCreator}" style="color: green;"><i class="fa" :class="{'fa-check-square-o': !loading.penalty, 'fa-spin fa-spinner': loading.penalty}"></i> {{ trans('enabled') }}</span>
                    <span @click="changeOption('penalty', 20)" v-show="!penalty" :class="{cursor: isCreator}" style="color: red;"><i class="fa" :class="{'fa-square-o': !loading.penalty, 'fa-spin fa-spinner': loading.penalty}"></i> {{ trans('disabled') }}</span>
                    <span v-show="penalty">
                        <span v-if="isCreator">
                            <input @change="changeOption('penalty', $event.target.value)" type="number" min="1" max="72" :value="penalty"> hour(s)
                        </span>
                        <span v-else>
                            <strong>{{ penalty }}</strong> hour(s)
                        </span>
                    </span>
                </td>
            </tr>
        <tr>
            <td>
                {{ trans('weapon_name') }}
                <span class="fa opentip" :data-tip="tips('weapon')"></span> :
            </td>
            <td>
                <span @click="changeOption('weapon', false)" v-show="isWeapon" :class="{cursor: isCreator}" style="color: green;"><i class="fa" :class="{'fa-check-square-o': !loading.weapon, 'fa-spin fa-spinner': loading.weapon}"></i> {{ trans('enabled') }}</span>
                <span @click="changeOption('weapon', true)" v-show="!isWeapon" :class="{cursor: isCreator}" style="color: red;"><i  class="fa" :class="{'fa-square-o': !loading.weapon, 'fa-spin fa-spinner': loading.weapon}"></i> {{ trans('disabled') }} </span>
            </td>
        </tr>
        <tr>
            <td>
                {{ trans('bonus_name') }}
                <span class="fa opentip" :data-tip="tips('bonus')"></span> :
            </td>
            <td>
                <span @click="changeOption('bonus', false)" v-show="isBonus" :class="{cursor: isCreator}" style="color: green;"><i class="fa" :class="{'fa-check-square-o': !loading.bonus, 'fa-spin fa-spinner': loading.bonus}"></i> {{ trans('enabled') }}</span>
                <span @click="changeOption('bonus', true)" v-show="!isBonus" :class="{cursor: isCreator}" style="color: red;"><i class="fa" :class="{'fa-square-o': !loading.bonus, 'fa-spin fa-spinner': loading.bonus}"></i> {{ trans('disabled') }}</span>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    import { mapState } from 'vuex'
    import store from '../store/store'
    import * as types from "../store/mutation-types"

    export default {
        data() {
            return {
                loading: {
                    penalty: false,
                    weapon: false,
                    bonus: false,
                },
                trans() {
                    return Translator.trans(...arguments)
                },
                tips(type) {
                    return `<strong>${Translator.trans(type+'_name')} :</strong>${Translator.trans(type+'_desc')}`
                }
            }
        },
        computed: {
            ...mapState([
                'game',
                'isCreator',
            ]),
            isBonus() {
                return (this.game.options) ? this.game.options.bonus : false
            },
            isWeapon() {
                return (this.game.options) ? this.game.options.weapon : false
            },
            penalty() {
                return (this.game.options) ? this.game.options.penalty : 0
            },
        },
        methods: {
            changeOption(name, value) {
                if (!this.isCreator) {
                    return false
                }

                this.loading[name] = true
                store.dispatch(types.ACTION.CHANGE_OPTION, {
                    option: name,
                    value: value,
                }).then((obj) => {
                    this.loading[name] = false
                })
            },
        },
    }
</script>
