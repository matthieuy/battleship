<template>
    <div>
        <div id="status">{{ status }}</div>
        <div id="grid" class="grid" unselectable="on">
            <div class="grid-line" v-for="row in grid">
                <span
                    v-for="box in row"
                    @click="click(box)"
                    @mousemove="mousemove()"
                    @mousedown="mousedown()"
                    @mouseup="mouseup(box)"
                    :id="'g_' + box.y + '_' + box.x"
                    class="box"
                    :class="cssBox(box)"
                    :data-tip="tooltip(box)">
                    <span v-if="box.explose" class="explose hit animated"></span>
                </span>
            </div>
        </div>
        <span>
            <div class="rocket" v-for="player in players" :id="'rocket'+player.position"></div>
        </span>
    </div>
</template>
<script>
    // Import
    import Vue from "vue"
    import { mapState, mapGetters } from 'vuex'
    import { ACTION, MUTATION } from "../store/mutation-types"
    import store from "../store/GameStore"

    // Bower require
    let async = require('@bower/async/dist/async.min.js')
    let Velocity = require('@bower/velocity/velocity.js')

    // Mobile shoot
    let pressTimer = null
    let longPress = false
    let mobile = window.isMobile()

    export default {
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
            receive(obj) {
                let self = this
                async.mapSeries(
                    obj.img,
                    function(img, next) {
                        let $box = $('#g_' + img.y + '_' + img.x)
                        $box.getTop = function() { return this.offset().top }
                        $box.getLeft = function() { return this.offset().left }

                        // Status
                        let shooter = self.playerById(img.shoot)
                        store.commit(MUTATION.SET_STATUS, shooter.name + "'s shot")

                        // Rocket animate
                        Velocity(document.getElementById('rocket'+img.shoot), {
                            top: $box.getTop() - (self.boxSize / 2),
                            left: $box.getLeft() + (self.boxSize / 4),
                        }, {
                            duration: 5 * ($box.getTop() + 20),
                            easing: 'linear',
                            begin: function(rocket) {
                                // Start position of the rocket
                                $(rocket).css({
                                    top: -20,
                                    left: $box.getLeft() + (self.boxSize / 2),
                                })
                            },
                            complete: function(rocket) {
                                $(rocket).css('top', '-40px')

                                // Update grid
                                $box.addClass('animated')
                                store.commit(MUTATION.AFTER_ROCKET, img)
                                store.dispatch(ACTION.AFTER_ROCKET, img)

                                // Next img
                                next()
                            }
                        })
                    },
                    function() { // End of animate
                        store.commit(MUTATION.AFTER_ANIMATE, obj)
                    }
                )
            },
            // On click : shoot if not mobile
            click(box) {
                if (!mobile) {
                    this.shoot(box)
                }
            },
            // On mouse move : reset long press
            mousemove() {
                if (mobile) {
                    clearTimeout(pressTimer)
                    pressTimer = null
                    longPress = false
                    $('#grid').removeAttr('style')
                }
            },
            // On mouse down : prepare longpress
            mousedown() {
                if (mobile) {
                    pressTimer = setTimeout(function() {
                        longPress = true
                        if (pressTimer) {
                            $('#grid').css({ backgroundColor: '#BD2626' })
                        }
                    }, 1000)
                }
            },
            // On mouse up : if long press => shoot
            mouseup(box) {
                if (mobile) {
                    clearTimeout(pressTimer)
                    pressTimer = null
                    if (longPress) {
                        longPress = false
                        this.shoot(box)
                    }
                }
            },
            // Do a shoot
            shoot(box) {
                if (this.gameover || !this.me || (this.me && this.me.life <= 0)) {
                    return false;
                }

                // Data to send by RPC
                let dataSend = {
                    x: box.x,
                    y: box.y,
                }

                // Add weapon
                store.dispatch(ACTION.BEFORE_SHOOT, dataSend).then((dataSend) => {
                    // Send data
                    store.dispatch(ACTION.SHOOT, dataSend)
                })
            },
            // CSS for box
            cssBox(box) {
                let css = {
                    explose: box.img < 0,
                    miss: box.img == -2,
                    dead: box.dead,
                    hit: box.img == -1,
                    opentip: box.player != null || box.shoot != null,
                    animated: box.img == -1,
                    boat: box.img > 0,
                }
                css['img' + box.img] = box.img > 0
                if (box.player != null) {
                    css['player' + box.player] = true
                }

                // Replace animate on grid
                if ($('#g_'+box.y+'_'+box.x).hasClass('animated')) {
                    css.animated = true
                }

                return css
            },
            // Text in tooltip
            tooltip(box) {
                let tooltip = []

                // Victim or same team
                if (box.player != null) {
                    if (this.me && this.me.position == box.player) {
                        tooltip.push("Your boat")
                    } else {
                        let victim = this.playerById(box.player)
                        if (victim) {
                            tooltip.push("Boat of " + victim.name)
                        }
                    }
                }

                // Shooter
                if (box.shoot != null) {
                    if (this.me && this.me.position == box.shoot) {
                        tooltip.push("Your shot")
                    } else {
                        let shooter = this.playerById(box.shoot)
                        if (shooter) {
                            tooltip.push("Shot of " + shooter.name)
                        }
                    }
                }
                return tooltip.length ? tooltip.join('<br>') : null
            },
        },
        watch: {
            // Tour => update status
            tour(tour) {
                let players = []
                let self = this
                tour.forEach(function(playerId) {
                    players.push(self.playerById(playerId).name)
                })
                if (this.gameover) {
                    store.commit(MUTATION.SET_STATUS, "Winner : " + players.join(', '))
                } else {
                    store.commit(MUTATION.SET_STATUS, "Waiting shoot of " + players.join(', '))
                }
            },
        },
        mounted() {
            // Disable select
            $('#grid').bind('selectstart', function(){ return false; });
            $(document).bind('ondragstart ondrop', function() { return false; })

            // Websocket subscribe
            let topicName = 'game/' + document.getElementById('slug').value + '/run'
            WS.subscribeAction(topicName, 'data', (obj) => {
                this.receive(obj)
            })

            // Show/Hide boat
            $(window).on('keyup', hideShowBoats)
        },
    }

    /**
     * Show / Hide boat
     */
    function hideShowBoats(e) {
        if (e.which == 72) { // H key
            if ($('.hide').length) {
                $('.hide').removeClass('hide').addClass('boat')
            } else {
                $('.boat:not(.dead,.animated)').removeClass('boat').addClass('hide')
            }
        }

    }
</script>
