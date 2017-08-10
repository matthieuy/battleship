<template>
    <div>
        <div id="status">{{ status }}</div>
        <div id="grid-container">
            <div id="grid" class="grid" unselectable="on" contextmenu="grid-menu">
                <div class="grid-line" v-for="row in grid">
                <span
                        v-for="box in row"
                        @click="click(box)"
                        :id="'g_' + box.y + '_' + box.x"
                        class="box"
                        :class="cssBox(box)"
                        :data-tip="tooltip(box)">
                    <span v-if="box.explose" class="explose hit animated"></span>
                </span>
                </div>
            </div>
        </div>
        <span>
            <div class="rocket" v-for="player in players" :id="'rocket'+player.position"></div>
        </span>
        <menu type="context" id="grid-menu">
            <menuitem @click="contextmenu('score')" :label="trans('Score')" icon="img/icons/score.png"></menuitem>
            <menuitem @click="contextmenu('weapon')" :label="trans('weapon_name')" icon="img/icons/weapons.png"></menuitem>
            <menuitem @click="contextmenu('inventory')" :label="trans('Inventory')" icon="img/icons/inventory.png"></menuitem>
            <menuitem @click="contextmenu('chat')" :label="trans('Chat')" icon="img/icons/chat.png"></menuitem>
        </menu>
    </div>
</template>
<script>
  // Import
  import {mapState, mapGetters} from 'vuex'
  import {ACTION, MUTATION} from '../store/mutation-types'
  /* global WS, $, Translator */

  // Bower require
  let async = require('@npm/async/dist/async.min.js')
  let Velocity = require('@npm/velocity-animate/velocity.js')

  // Mobile shoot
  let pressTimer = null
  let longPress = false
  let mobile = window.isMobile()

  export default {
    data () {
      return {
        trans () {
          return Translator.trans(...arguments)
        },
      }
    },
    computed: {
      // State from store
      ...mapState([
        'status',
        'players',
        'grid',
        'boxSize',
        'me',
        'tour',
        'gameover',
      ]),
      // getters from store
      ...mapGetters([
        'playerById',
      ]),
    },
    methods: {
      // Receive data from game
      receive (obj) {
        let self = this
        async.mapSeries(
          obj.img,
          function (img, next) {
            let $box = $('#g_' + img.y + '_' + img.x)
            $box.getTop = function () {
              return this.offset().top + document.querySelector('#container').scrollTop
            }
            $box.getLeft = function () {
              return this.offset().left + document.querySelector('#container').scrollLeft
            }

            // Status
            let shooter = self.playerById(img.shoot)
            if (typeof img.player !== 'undefined' && img.player === img.shoot) {
              self.$store.commit(MUTATION.SET_STATUS, Translator.trans('system.penalty', {username: shooter.name}))
            } else {
              self.$store.commit(MUTATION.SET_STATUS, Translator.trans('shoot_of', {name: shooter.name}))
            }

            // Rocket animate
            Velocity(document.getElementById('rocket' + img.shoot), {
              top: $box.getTop() - (self.boxSize / 2),
              left: $box.getLeft() + (self.boxSize / 4),
            }, {
              duration: 5 * ($box.getTop() + 20),
              easing: 'linear',
              begin: function (rocket) {
                // Start position of the rocket
                $(rocket).css({
                  top: -20,
                  left: $box.getLeft() + (self.boxSize / 4),
                })
              },
              complete: function (rocket) {
                $(rocket).css('top', '-40px')

                // Update grid
                $box.addClass('animated')
                self.$store.commit(MUTATION.AFTER_ROCKET, img)
                self.$store.dispatch(ACTION.AFTER_ROCKET, img)

                // Next img
                next()
              },
            })
          },
          function () { // End of animate
            self.$store.commit(MUTATION.AFTER_ANIMATE, obj)
          }
        )
      },
      // On click : shoot if not mobile
      click (box) {
        if (!mobile) {
          this.shoot(box)
        }
      },
      // Do a shoot
      shoot (box) {
        if (this.gameover || !this.me || (this.me && this.me.life <= 0)) {
          return false
        }

        // Data to send by RPC
        let dataSend = {
          x: box.x,
          y: box.y,
        }

        // Add weapon
        let self = this
        self.$store.dispatch(ACTION.BEFORE_SHOOT, dataSend).then((dataSend) => {
          // Send data
          self.$store.dispatch(ACTION.SHOOT, dataSend)
        })
      },
      // CSS for box
      cssBox (box) {
        let css = {
          explose: box.img < 0,
          miss: box.img === -2,
          dead: box.dead,
          hit: box.img === -1,
          opentip: (typeof box.player !== 'undefined' || typeof box.shoot !== 'undefined'),
          animated: box.img === -1,
          boat: box.img > 0,
        }
        css['img' + box.img] = box.img > 0
        if (box.player !== null) {
          css['player' + box.player] = true
        }

        // Replace animate on grid
        if ($('#g_' + box.y + '_' + box.x).hasClass('animated')) {
          css.animated = true
        }

        return css
      },
      // Text in tooltip
      tooltip (box) {
        let tooltip = []

        // Victim or same team
        if (box.player !== null) {
          if (this.me && this.me.position === box.player) {
            tooltip.push(Translator.trans('your_boat'))
          } else {
            let victim = this.playerById(box.player)
            if (victim) {
              tooltip.push(Translator.trans('boat_of', {name: victim.name}))
            }
          }
        }

        // Shooter
        if (box.shoot !== null) {
          if (this.me && this.me.position === box.shoot) {
            tooltip.push(Translator.trans('your_shot'))
          } else {
            let shooter = this.playerById(box.shoot)
            if (shooter) {
              tooltip.push(Translator.trans('shoot_of', {name: shooter.name}))
            }
          }
        }
        return tooltip.length ? tooltip.join('<br>') : null
      },
      contextmenu (item) {
        switch (item) {
          case 'score':
            this.$store.commit(MUTATION.SCORE.MODAL, true)
            break
          case 'weapon':
            this.$store.commit(MUTATION.WEAPON.MODAL, true)
            break
          case 'inventory':
            this.$store.commit(MUTATION.INVENTORY.MODAL, true)
            break
          case 'chat':
            this.$store.commit(MUTATION.CHAT.MODAL, true)
            break
        }
      },
    },
    watch: {
      // Tour => update status
      tour (tour) {
        let players = []
        let self = this
        tour.forEach(function (playerId) {
          players.push(self.playerById(playerId).name)
        })
        if (this.gameover) {
          this.$store.commit(MUTATION.SET_STATUS, Translator.trans('winner_list', {list: players.join(', ')}))
        } else {
          this.$store.commit(MUTATION.SET_STATUS, Translator.trans('waiting_list', {list: players.join(', ')}))
        }
      },
      gameover (gameover) {
        if (gameover) {
          $('#grid').off('vmouseup vmousedown vmousemove')
        }
      },
    },
    mounted () {
      // Disable select
      $(document).bind('selectstart', function () {
        return false
      })
      $(document).bind('ondragstart ondrop', function () {
        return false
      })

      // Websocket subscribe
      let topicName = 'game/' + document.getElementById('slug').value + '/run'
      WS.subscribeAction(topicName, 'data', (obj) => {
        this.receive(obj)
      })

      // Mobile shoot
      let self = this
      $('#grid')
      // On mouse move : reset long press
        .on('vmousemove', () => {
          if (mobile) {
            clearTimeout(pressTimer)
            pressTimer = null
            longPress = false
            $('#grid').removeAttr('style')
          }
        })
        // On mouse down : prepare longpress
        .on('vmousedown', () => {
          if (mobile) {
            pressTimer = setTimeout(function () {
              longPress = true
              if (pressTimer) {
                $('#grid').css({backgroundColor: '#BD2626'})
              }
            }, 1000)
          }
        })
        // On mouse up : if long press => shoot
        .on('vmouseup', function (e) {
          if (mobile) {
            clearTimeout(pressTimer)
            pressTimer = null
            if (longPress) {
              longPress = false
              let coord = e.target.getAttribute('id').split('_')
              self.shoot({
                x: coord[2],
                y: coord[1],
              })
            }
          }
        })

      // Show/Hide boat
      $(window).on('keyup', hideShowBoats)
    },
  }

  /**
   * Show / Hide boat
   */
  function hideShowBoats (e) {
    if (e.which === 72) { // H key
      if ($('.hide').length) {
        $('.hide').removeClass('hide').addClass('boat')
      } else {
        $('.boat:not(.dead,.animated)').removeClass('boat').addClass('hide')
      }
    }
  }
</script>
