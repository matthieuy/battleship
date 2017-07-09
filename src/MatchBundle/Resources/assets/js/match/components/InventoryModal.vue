<template>
    <div v-show="inventory.modal">
        <div class="modal-bg"></div>
        <div class="modal-wrap">
            <div class="modal-container"v-on:click.stop.prevent="close()">
                <div class="modal-content">
                    <div id="modal-bonus" v-on:click.stop.prevent="">
                        <h1 class="center">Inventory</h1>
                        <div class="clear"></div>

                        <div class="row overflow">
                            <div class="large-6 push-3 column">
                                <div class="container-bonus">
                                    <div class="large-4 column" v-for="bonus in inventory.list" @click="highlight(bonus)">
                                        <div class="bonus-box opentip" :class="{selected: (selected == bonus.id) }" :data-tip="'<strong>'+bonus.name+' :</strong> '+bonus.description">
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

                        <div class="large-12 center">
                            <div class="row btn-action">
                                <button class="button success small-12 large-3" :class="{disabled: !selected }" @click="use()">
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
    import store from "../store/GameStore"
    import { MUTATION, ACTION } from "../store/mutation-types"

    export default {
        data() {
            return {
                selected: null,
            }
        },
        computed: {
            ...mapState([
                'inventory',
                'gameover',
            ]),
        },
        methods: {
            // Close modal
            close() {
                store.commit(MUTATION.INVENTORY.MODAL, false)
            },
            use() {
                if (this.selected) {
                    store.dispatch(ACTION.INVENTORY.USE, this.selected)
                    this.selected = null
                }
            },
            // highlight the bonus
            highlight(bonus) {
                if (bonus == null || this.gameover) {
                    this.selected = null;
                } else {
                    this.selected = bonus.id
                }
            },
        },
        watch: {
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
