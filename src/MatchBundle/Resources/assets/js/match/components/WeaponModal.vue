<template>
    <div v-show="weapon.modal">
        <div class="modal-bg"></div>
        <div class="modal-wrap">
            <div class="modal-container">
                <div class="modal-content">
                    <div id="modal-weapon">
                        <div class="center">
                            <h1>Weapons</h1>
                            <div v-show="me"><strong>{{ score }}</strong> points</div>
                        </div>
                        <div class="clear"></div>

                        <div class="large-12 column">
                            <div class="row">
                                <div class="large-3 column" v-for="w in weapon.list">
                                    <div class="center weapon" :class="classWeapon(w)" @click="highlight(w)">
                                        <h3>{{ w.name }}</h3>
                                        <em>Price: {{ w.price }} points</em>
                                        <div class="grid weapon-model" :style="styleModel(w)">
                                            <div class="clear row" v-for="row in w.grid">
                                                <span class="box" v-for="box in row" :class="{'explose hit animated': box }"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="clear"></div>
                        <div class="large-12 center">
                            <div class="row btn-action">
                                <button class="button primary small-10 large-3" @click="rotate()">Rotate</button>
                                <button class="button success small-10 large-3" :class="{disabled: !selected}" @click="select()">Select</button>
                                <button class="button alert small-10 large-3" @click="close()">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    // Import
    import { mapState } from 'vuex'
    import store from "../store/GameStore"
    import { ACTION, MUTATION } from "../store/modules/weapons"

    export default {
        data() {
            return {
                selected: null,
            }
        },
        computed: {
            ...mapState([
                'weapon', // Weapon module
                'me',
                'boxSize',
            ]),
            score() {
                return (this.me && this.me.score) ? this.me.score : 0
            }
        },
        methods: {
            // Close modal
            close() {
                store.commit(MUTATION.WEAPON_MODAL, false)
                store.commit(MUTATION.SELECT)
                this.selected = null
            },
            // Select weapon
            select() {
                store.commit(MUTATION.SELECT, this.selected)
                store.commit(MUTATION.WEAPON_MODAL, false)
                this.selected = null
            },
            // Rotate weapons
            rotate() {
                store.commit(MUTATION.ROTATE)
            },
            // CSS class for weapon box
            classWeapon(weapon) {
                return {
                    disabled: weapon.price > this.score,
                    selected: this.selected && weapon.name == this.selected.name,
                }
            },
            // Highlight the weapon (on click)
            highlight(weapon) {
                if (this.score >= weapon.price) {
                    this.selected = weapon
                }
            },
            // Calcul style of model weapon
            styleModel(weapon) {
                return {
                    width: (weapon.grid[0].length * this.boxSize) +'px',
                    marginTop: (6 - weapon.grid.length) * (this.boxSize / 2) + 'px',
                }
            },
        },
        watch: {
            // Load weapons list on the first call
            ['weapon.modal'](open) {
                if (open && !store.state.weapon.loaded) {
                    store.dispatch(ACTION.LOAD, $('input#ajax-weapons').val()).then((list) => {
                        if (list.error) {
                            return Flash.error(list.error)
                        }
                        store.commit(MUTATION.SET_LIST, list)
                    })
                }

                // Bind escape touch
                if (open || store.state.weapon.modal || store.state.weapon.select) {
                    console.log('bind')
                    $(window).on('keyup', escapeTouch)
                }
            },
        },
    }

    /**
     * Press escape touch : close or lease weapon
     * @param e
     */
    function escapeTouch(e) {
        if (e.which == 27) {
            if (store.state.weapon.modal) {
                store.commit(MUTATION.WEAPON_MODAL, false)
            } else {
                store.commit(MUTATION.SELECT)
            }
            $(window).off('keyup', escapeTouch)
        }
    }
</script>

<style lang="less">
    .modal-bg {
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1042;
        overflow: hidden;
        position: absolute;
        background: #0b0b0b;
        opacity: 0.8;
    }
    .modal-wrap {
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1043;
        position: fixed;
        outline: none !important;
        -webkit-backface-visibility: hidden;
        overflow-x: hidden;
        overflow-y: auto;
    }
    .modal-container {
        text-align: center;
        position: absolute;
        width: 100%;
        height: 100%;
        left: 0;
        top: 0;
        padding: 0 8px;
        box-sizing: border-box;
        &:before {
             content: '';
             display: inline-block;
             height: 100%;
             vertical-align: middle;
        }
    }
    .modal-content {
        position: relative;
        display: inline-block;
        vertical-align: middle;
        margin: 0 auto;
        text-align: left;
        z-index: 1045;
        width: 100%;
    }
    #modal-weapon {
        background-color: #FFF;
        max-width: 95%;
        margin: auto;
        vertical-align: middle;
        .btn-action {
            margin-top: 25px;
        }
        .weapon {
            cursor: pointer;
            margin-top: 20px;
            padding: 10px;
            height: 180px;
            background-color: #f2f2f2;
            border: 1px solid #d9d9d9;

            h3 {
                font-size: 1.4em;
            }

            &.selected {
                 outline: #1F7E1F solid 2px;
                 background-color: #b0ff9e;
             }

            &.disabled {
                 border: 2px solid #F00;
                 background-color: #FDE;
                 cursor: default;
             }
        }
        .weapon-model {
            margin: 5px auto;
            .row {
                margin: 0;
                height: 20px;
            }
            span {

            }
        }
    }
</style>
