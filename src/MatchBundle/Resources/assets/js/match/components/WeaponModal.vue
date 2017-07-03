<template>
    <div v-show="weapon.modal">
        <div class="modal-bg"></div>
        <div class="modal-wrap">
            <div class="modal-container"v-on:click.stop.prevent="close()">
                <div class="modal-content">
                    <div id="modal-weapon" v-on:click.stop.prevent="">
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
                'gameover',
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
                if (this.selected) {
                    store.commit(MUTATION.SELECT, this.selected)
                    store.commit(MUTATION.WEAPON_MODAL, false)
                    this.selected = null
                }
            },
            // Rotate weapons
            rotate() {
                store.commit(MUTATION.SELECT)
                store.commit(MUTATION.ROTATE)
            },
            // CSS class for weapon box
            classWeapon(weapon) {
                return {
                    disabled: weapon.price > this.score || this.gameover || (this.me && this.me.life <= 0),
                    selected: this.selected && weapon.name == this.selected.name,
                }
            },
            // Highlight the weapon (on click)
            highlight(weapon) {
                if (this.score >= weapon.price && !this.gameover && (this.me && this.me.life > 0)) {
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
                    $(window).on('keyup', escapeTouch)
                }
            },
            // on select : add helper on the grid
            ['weapon.select'](weapon) {
                if (this.gameover || !this.me || (this.me && this.me.life <= 0)) {
                    return false;
                }

                weapon = store.getters.getWeapon(weapon)
                if (weapon) {
                    // Get weapon grid
                    let weaponBox = weapon.grid
                    let weaponSize = [weaponBox.length, weaponBox[0].length]
                    let weaponCenter = [Math.floor(weaponSize[0] / 2), Math.floor(weaponSize[1] / 2)]

                    // Get list of box to shoot
                    let boxes = []
                    for (let y=0; y<weaponSize[0]; y++) {
                        for (let x=0; x<weaponSize[1]; x++) {
                            if (weaponBox[y][x]) {
                                boxes.push([
                                    x - weaponCenter[1],
                                    y - weaponCenter[0],
                                ])
                            }
                        }
                    }

                    // Add .target in box to shoot
                    $('#grid')
                        .off('mouseover mouseout', 'span')
                        .on('mouseover', 'span', function() {
                            let coord = $(this).attr('id').split('_').map((i) => parseInt(i))
                            coord.shift()

                            boxes.forEach(function(box) {
                                let id = '#g_' + (box[1] + coord[0]) + '_' + (box[0] + coord[1])
                                if ($(id).length === 1) {
                                    $(id).addClass('target')
                                }
                            })
                        })
                        .on('mouseout', 'span', function() {
                            $('#grid .target').removeClass('target')
                        })
                } else {
                    // Unbind helper
                    $('#grid .target').removeClass('target')
                    $('#grid').off('mouseover mouseout', 'span')
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
    #grid span.target {
        background-color: rgba(255, 0, 0, 0.75);
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
