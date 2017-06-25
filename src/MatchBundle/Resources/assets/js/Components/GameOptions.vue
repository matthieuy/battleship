<template>
    <table id="table-options" class="small-12 extendable">
        <thead>
            <tr>
                <th colspan="2">
                    {{ name }}
                    <div class="fa caret"></div>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ penalty_name }}
                    <span class="fa opentip" :data-tip="tips('penalty')"></span> :
                </td>
                <td>
                    <span @click="changeOption('penalty', 0)" v-show="penalty" :class="{cursor: isCreator}" style="color: green;"><i class="fa" :class="{'fa-check-square-o': !loading.penalty, 'fa-spin fa-spinner': loading.penalty}"></i> {{ enabled }}</span>
                    <span @click="changeOption('penalty', 20)" v-show="!penalty" :class="{cursor: isCreator}" style="color: red;"><i class="fa" :class="{'fa-square-o': !loading.penalty, 'fa-spin fa-spinner': loading.penalty}"></i> {{ disabled }}</span>
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
                {{ weapon_name }}
                <span class="fa opentip" :data-tip="tips('weapon')"></span> :
            </td>
            <td>
                <span @click="changeOption('weapon', false)" v-show="isWeapon" :class="{cursor: isCreator}" style="color: green;"><i class="fa" :class="{'fa-check-square-o': !loading.weapon, 'fa-spin fa-spinner': loading.weapon}"></i> {{ enabled }}</span>
                <span @click="changeOption('weapon', true)" v-show="!isWeapon" :class="{cursor: isCreator}" style="color: red;"><i  class="fa" :class="{'fa-square-o': !loading.weapon, 'fa-spin fa-spinner': loading.weapon}"></i> {{ disabled }} </span>
            </td>
        </tr>
        <tr>
            <td>
                {{ bonus_name }}
                <span class="fa opentip" :data-tip="tips('bonus')"></span> :
            </td>
            <td>
                <span @click="changeOption('bonus', false)" v-show="isBonus" :class="{cursor: isCreator}" style="color: green;"><i class="fa" :class="{'fa-check-square-o': !loading.bonus, 'fa-spin fa-spinner': loading.bonus}"></i> {{ enabled }}</span>
                <span @click="changeOption('bonus', true)" v-show="!isBonus" :class="{cursor: isCreator}" style="color: red;"><i class="fa" :class="{'fa-square-o': !loading.bonus, 'fa-spin fa-spinner': loading.bonus}"></i> {{ disabled }}</span>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    import { mapState } from 'vuex'
    import store from '../Stores/WaitingStore'

    export default {
        data() {
            return {
                loading: {
                    penalty: false,
                    weapon: false,
                    bonus: false,
                }
            }
        },
        props: {
            name: {type: String, default: 'Options'},
            enabled: {type: String, default: 'Enabled'},
            disabled: {type: String, default: 'Disabled'},
            penalty_name: {type: String, default: 'Penalty'},
            penalty_desc: {type: String, default: 'Over a certain time without playing,<br>the player takes a shot on one of his boats.'},
            weapon_name: {type: String, default: 'Weapons'},
            weapon_desc: {type: String, default: 'Players can use their points <br>for use special weapon.<br><br>(see points table)'},
            bonus_name: {type: String, default: 'Bonus'},
            bonus_desc: {type: String, default: 'Players can catch bonus'},
        },
        methods: {
            tips(type) {
                return `<strong>${this[type+'_name']} :</strong>${this[type+'_desc']}`
            },
            changeOption(name, value) {
                if (!this.isCreator) {
                    return;
                }

                this.loading[name] = true
                var that = this

                WS.callRPC('wait/options', {
                    option: name,
                    value: value,
                }, (obj) => {
                    this.loading[name] = false
                })
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
            }
        }
    }
</script>
