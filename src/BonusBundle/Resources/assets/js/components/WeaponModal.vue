<template>
    <div v-show="weapon.modal">
        <div class="modal-bg"></div>
        <div class="modal-wrap">
            <div class="modal-container" v-on:click.stop.prevent="close()">
                <div class="modal-content">
                    <div id="modal-weapon" v-on:click.stop.prevent="">
                        <div class="center">
                            <h1>{{ trans('weapon_name') }}</h1>
                            <div v-show="isUser"><strong>{{ transChoice('points_plurial', score, {nb: score}) }}</strong></div>
                        </div>
                        <div class="clear"></div>

                        <div class="large-12 column">
                            <div class="row">
                                <div class="large-3 column" v-for="w in weapon.list">
                                    <div class="center weapon" :class="classWeapon(w)" @click="highlight(w)">
                                        <h3>{{ trans(w.name) }}</h3>
                                        <em>{{ trans('Price') }} : {{ transChoice('points_plurial', w.price, {nb: w.price}) }}</em>
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
                                <button class="button primary small-10 large-3" @click="rotate()">{{ trans('Rotate') }}
                                </button>
                                <button class="button success small-10 large-3" :class="{disabled: !selected}" @click="select()">
                                    {{ trans('Select') }}
                                </button>
                                <button class="button alert small-10 large-3" @click="close()">{{ trans('Close') }}
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
  // Import
  import {mapState, mapGetters} from 'vuex'
  import {ACTION, MUTATION} from '@match/js/match/store/mutation-types'
  /* global $, Flash, Translator */

  export default {
    data () {
      return {
        selected: null,
        trans () {
          return Translator.trans(...arguments)
        },
        transChoice () {
          return Translator.transChoice(...arguments)
        },
      }
    },
    computed: {
      ...mapState([
        'weapon', // Weapon module
        'boxSize',
        'gameover',
      ]),
      ...mapGetters([
        'life',
        'isUser',
      ]),
      score () {
        return this.weapon.score
      },
    },
    methods: {
      // Close modal
      close () {
        this.$store.commit(MUTATION.WEAPON.MODAL, false)
        this.$store.commit(MUTATION.WEAPON.SELECT)
        this.selected = null
      },
      // Select weapon
      select () {
        if (this.selected) {
          this.$store.commit(MUTATION.WEAPON.SELECT, this.selected)
          this.$store.commit(MUTATION.WEAPON.MODAL, false)
          this.selected = null
        }
      },
      // Rotate weapons
      rotate () {
        this.$store.commit(MUTATION.WEAPON.SELECT)
        this.$store.commit(MUTATION.WEAPON.ROTATE)
      },
      // CSS class for weapon box
      classWeapon (weapon) {
        return {
          disabled: weapon.price > this.score || this.gameover || this.life <= 0,
          selected: this.selected && weapon.name === this.selected.name,
        }
      },
      // Highlight the weapon (on click)
      highlight (weapon) {
        if (this.score >= weapon.price && !this.gameover && this.life > 0) {
          this.selected = weapon
        }
      },
      // Calcul style of model weapon
      styleModel (weapon) {
        return {
          width: (weapon.grid[0].length * 20) + 'px',
          marginTop: (6 - weapon.grid.length) * 10 + 'px',
        }
      },
    },
    watch: {
      // Load weapons list on the first call
      'weapon.modal': function (open) {
        let self = this
        if (open && !this.$store.state.weapon.loaded) {
          this.$store.dispatch(ACTION.WEAPON.LOAD, $('input#ajax-weapons').val()).then((list) => {
            if (list.error) {
              return Flash.error(list.error)
            }
            self.$store.commit(MUTATION.WEAPON.SET_LIST, list)
          })
        }

        // Fix scroll
        if (open) {
          $('#container').css({
            overflow: 'hidden',
            position: 'fixed',
          })
        } else {
          $('#container').removeAttr('style')
        }

        // Bind escape touch
        if (open || this.$store.state.weapon.modal || this.$store.state.weapon.select) {
          let escapeTouch = function (e) {
            if (e.which === 27) {
              if (self.$store.state.weapon.modal) {
                self.$store.commit(MUTATION.WEAPON.MODAL, false)
              } else {
                self.$store.commit(MUTATION.WEAPON.SELECT)
              }
              $(window).off('keyup', escapeTouch)
            }
          }
          $(window).on('keyup', escapeTouch)
        }
      },
      // on select : add helper on the grid
      'weapon.select': function (weapon) {
        if (this.gameover || this.life <= 0) {
          return false
        }

        weapon = this.$store.getters.getWeapon(weapon)
        if (weapon) {
          // Get weapon grid
          let weaponBox = weapon.grid
          let weaponSize = [weaponBox.length, weaponBox[0].length]
          let weaponCenter = [Math.floor(weaponSize[0] / 2), Math.floor(weaponSize[1] / 2)]

          // Get list of box to shoot
          let boxes = []
          for (let y = 0; y < weaponSize[0]; y++) {
            for (let x = 0; x < weaponSize[1]; x++) {
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
            .on('mouseover', 'span', function () {
              let el = ($(this).hasClass('box')) ? $(this) : $(this).parent('.box')
              let coord = el.attr('id').split('_').map((i) => parseInt(i))
              coord.shift()

              boxes.forEach(function (box) {
                let id = '#g_' + (box[1] + coord[0]) + '_' + (box[0] + coord[1])
                if ($(id).length === 1) {
                  $(id).addClass('target')
                }
              })
            })
            .on('mouseout', 'span', function () {
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
</script>

<style lang="less">
    #grid span.target {
        background-color: rgba(255, 0, 0, 0.75);
    }

    #modal-weapon {
        .grid .box {
            width: 20px;
            height: 20px;
        }
        .hit {
            background-position: 120px 20px;
            background-size: 240px 40px;
        }
        .hit.animated {
            animation: explose-fire-modal 2s steps(12) infinite;
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

    @keyframes explose-fire-modal {
        0% {
            background-position: 0 20px;
        }
        100% {
            background-position: 240px 20px;
        }
    }
</style>
