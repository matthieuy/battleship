<template>
    <div v-show="inventory.modal">
        <div class="modal-bg"></div>
        <div class="modal-wrap">
            <div class="modal-container"v-on:click.stop.prevent="close()">
                <div class="modal-content">
                    <div id="modal-bonus" v-on:click.stop.prevent="">
                        <h1 class="center">Inventory</h1>
                        <div class="clear"></div>

                        <div class="row">
                            <div class="large-6 push-3 column">
                                <div class="container-bonus">
                                    <div class="large-4 column" v-for="bonus in inventory.list" @click="highlight(bonus)">
                                        <div class="bonus-box opentip" :class="{selected: (selected && selected.id == bonus.id), use: bonus.use }" :data-tip="'<strong>'+bonus.name+' :</strong> '+bonus.description">
                                            <img :src="'img/bonus/'+bonus.uniq+'.png'" width="80">
                                            <span class="label" v-show="bonus.options.label">{{ bonus.options.label }}</span>
                                        </div>
                                    </div>
                                    <div class="large-4 column hide-mobile" v-for="i in (inventory.size - inventory.list.length)" @click="highlight(null)">
                                        <div class="bonus-box empty">
                                            <img src="img/null.png" width="80">
                                        </div>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="row center" v-show="showSelectPlayer">
                            <div class="large-4 push-4">
                                <label for="player">Select target :</label>
                                <select id="player" v-model="selectPlayer">
                                    <option v-for="player in playersList" :value="player.position">{{ player.name }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="large-12 center">
                            <div class="row btn-action">
                                <button class="button success small-12 large-3" :class="{disabled: !selected || (selected.options.select && !selectPlayer) }" @click="use()">
                                    <i class="gi gi-round-star"></i>
                                    Use
                                </button>
                                <button class="close button alert small-10 large-3" @click="close()">
                                    <i class="fa fa-close"></i>
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import { mapState } from 'vuex'
    import store from "@match/js/match/store/GameStore"
    import { MUTATION, ACTION } from "@match/js/match/store/mutation-types"

    export default {
        data() {
            return {
                selected: null,
                showSelectPlayer: false,
                selectPlayer: null,
                playersList: [],
            }
        },
        computed: {
            ...mapState([
                'inventory',
                'players',
                'gameover',
            ]),
        },
        methods: {
            // Close modal
            close() {
                store.commit(MUTATION.INVENTORY.MODAL, false)
            },
            // Use bonus
            use() {
                if (!this.selected || (this.selected.options.select && !this.selectPlayer)) {
                    return false;
                } else if (this.selected.options.select && this.selectPlayer) {
                    this.selected.options.player = this.selectPlayer
                }

                store.dispatch(ACTION.INVENTORY.USE, this.selected)
                this.raz()
            },
            // highlight the bonus
            highlight(bonus) {
                if (bonus == null || bonus.use || this.gameover) {
                    this.raz()
                } else {
                    this.selected = bonus
                    this.showSelectPlayer = bonus.options.select
                }
            },
            // RAZ select
            raz() {
                this.selected = null;
                this.showSelectPlayer = false
                this.selectPlayer = null
            },
        },
        watch: {
            // Select a bonus : update players list
            ['selected'](bonus) {
                // No bonus
                if (!bonus) {
                    return this.playersList = [];
                }

                switch (bonus.options.select) {
                    // All players (except himself)
                    case 'all':
                        this.playersList = this.players.filter((player) => !player.me)
                        break

                    // Players in same team
                    case 'friends':
                        this.playersList = this.players.filter((player) => player.team == this.inventory.team && !player.me)
                        break

                    // Enemy
                    case 'enemy':
                        this.playersList = this.players.filter((player) => player.team != this.inventory.team)
                        break

                    default:
                        this.playersList = []
                }
            },
            // Load inventory on open modal
            ['inventory.modal'](open) {
                if (open) {
                    store.dispatch(ACTION.INVENTORY.LOAD)
                    $('#container').css({
                        overflow: 'hidden',
                        position: 'fixed',
                    })
                } else {
                    $('#container').removeAttr('style')
                }

                // Bind escape touch
                if (open || store.state.inventory.modal) {
                    $(window).on('keyup', escapeTouch)
                }
            },
        },
    }

    /**
     * Press escape touch : close modal
     * @param e
     */
    function escapeTouch(e) {
        if (e.which == 27) {
            if (store.state.inventory.modal) {
                store.commit(MUTATION.INVENTORY.MODAL, false)
            }
            $(window).off('keyup', escapeTouch)
        }
    }
</script>
<style lang="less">
    .container-bonus {
        border-radius: 20px;
        background-color: #e7e7e7;
    }
    .bonus-box {
        text-align: center;
        background-color: #939393;
        margin: 15px 0;
        border-radius: 10px;
        box-shadow: 2px 2px #535353cc;
    
        &.selected {
            box-shadow: 2px 2px #000;
            background-color: #646464;
        }
        &.use {
             background-color: #690101;
             cursor: default;
        }

        .label {
            position: absolute;
            background-color: #000;
            color: #939393;
            border-radius: 10px;
            padding: 3px;
            margin: 50px -10px;
            font-family: Arial,sans-serif;
            font-weight: bold;
            font-size: 80%;
        }
    }
</style>