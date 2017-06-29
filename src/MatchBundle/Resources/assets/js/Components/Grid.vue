<template>
    <div>
        <div id="status">{{ status }}</div>
        <div id="grid" class="grid" unselectable="on">
            <div class="grid-line" v-for="row in grid">
                <span v-for="box in row" :id="'g_' + box.y + '_' + box.x" :class="box_css(box)" :data-tip="tooltip(box)" @click="shoot(box)">
                    <span v-if="box.explose" class="explose hit animated"></span>
                </span>
            </div>
        </div>
        <div id="rocket"></div>
    </div>
</template>
<script>
    import Vue from "vue"
    import { mapState, mapActions } from 'vuex'
    import store from '../Stores/GameStore'

    // Bower require
    var async = require('@bower/async/dist/async.min.js')
    var Velocity = require('@bower/velocity/velocity.js')

    export default {
        computed: {
            ...mapState([
                'status',
                'size',
                'boxSize',
                'tour',
                'grid',
            ]),
        },
        methods: {
            // Receive data from game
            receive_data: (obj) => {
                async.mapSeries(
                    obj.img,
                    function(img, next) {
                        let $box = $('#g_' + img.y + '_' + img.x)
                        $box.getTop = function() { return this.offset().top }
                        $box.getLeft = function() { return this.offset().left }

                        // Status
                        let shooter = store.getters.playerById(img.shoot)
                        store.commit('SET_STATUS', shooter.name + '\'s shot')

                        // Rocket animate
                        Velocity(document.getElementById('rocket'), {
                            top: $box.getTop() - (store.getters.boxSize / 2),
                            left: $box.getLeft(),
                        }, {
                            duration: 5 * ($box.getTop() + 20),
                            easing: 'linear',
                            begin: function(el) {
                                // Start position of the rocket
                                $(el)
                                    .css({
                                        top: -20,
                                        left: $box.getLeft(),
                                    })
                            },
                            complete: function(el) {
                                $(el).css('top', '-20px')

                                // Update grid
                                store.commit('UPDATE_GRID', img)
                                $box.addClass('animated')

                                // Next img
                                next()
                            },
                        })
                    },
                    function() { // End animate
                        // Update tour
                        if (obj.tour) {
                            store.commit('SET_TOUR', obj.tour)
                        }
                    }
                )
            },
            // Do a shoot
            shoot: (box) => {
                // Data to send by RPC
                let dataSend = {
                    x: box.x,
                    y: box.y,
                }

                // Add weapon
                if (store.state.weapons.selected) {
                    Object.assign(dataSend, {
                        weapon: store.state.weapons.selected,
                        rotate: store.state.weapons.rotate,
                    })
                }

                // RPC
                WS.callRPC('run/shoot', dataSend)
            },
            // Update CSS
            update_grid_css() {
                console.info('[CSS] Upgrade grid style')
                // Grid CSS
                let sizeCss = (store.getters.size * store.getters.boxSize) + 'px'
                $('#grid').css({
                    width: sizeCss,
                    height: sizeCss,
                    minWidth: sizeCss,
                    minHeight: sizeCss,
                })

                // Row CSS
                $('.grid-line').css({
                    width: (store.getters.size * store.getters.boxSize) + 'px',
                    minWidth: store.getters.caseSize * store.getters.size + 'px',
                })
            },
            // calcul css class for the box
            box_css: (box) => {
                let cssClass = ['box'];
                if (typeof box.img !== 'undefined') {
                    if (box.img > 0) {
                        cssClass.push('boat')
                        cssClass.push('img'+box.img)
                        if (box.dead) {
                            cssClass.push('dead')
                        }
                    } else if (box.img == -2) {
                        cssClass.push('explose')
                        cssClass.push('miss')
                    } else if (box.img == -1) {
                        cssClass.push('explose')
                        cssClass.push('hit')
                        cssClass.push('animated')
                    }

                    if (box.player != null) {
                        cssClass.push('player' + box.player)
                    }
                    if (box.player != null || box.shoot != null) {
                        cssClass.push('opentip')
                    }
                }
                if ($('#g_'+box.y+'_'+box.x).hasClass('animated') && cssClass.indexOf('animated') == -1) {
                    cssClass.push('animated')
                }

                return cssClass.join(' ')
            },
            tooltip: (box) => {
                let tooltip = []
                let me = store.getters.me

                // Victim
                if (box.player != null) {
                    if (me && me.position == box.player) {
                        tooltip.push("Your boat")
                    } else {
                        let victim = store.getters.playerById(box.player)
                        if (victim) {
                            tooltip.push("Boat of " + victim.name)
                        }
                    }
                }

                // Shooter
                if (box.shoot != null) {
                    if (me && me.position == box.shoot) {
                        tooltip.push("Your shot")
                    } else {
                        let shooter = store.getters.playerById(box.shoot)
                        if (shooter) {
                            tooltip.push("Shot of " + shooter.name)
                        }
                    }
                }

                return tooltip.length ? tooltip.join('<br>') : null
            },
        },
        watch: {
            // Update status when change tour
            tour: (tour) => {
                let players = [];
                tour.forEach(function(playerId) {
                    players.push(store.getters.playerById(playerId).name)
                })
                store.commit('SET_STATUS', 'Waiting shoot of ' + players.join(', '))
            },
            // Update grid CSS (size, boxSize)
            size: 'update_grid_css',
            boxSize: 'update_grid_css',
        },
        mounted() {
            // Websocket subscribe
            let slug = document.getElementById('slug').value
            let topicName = 'game/' + slug + '/run'
            WS.subscribeAction(topicName, 'data', (obj) => {
                this.receive_data(obj)
            })

            // Disable select
            $('#grid').bind('selectstart', function(){ return false; });
            $(document).bind('ondragstart ondrop', function() { return false; })
        },
    }
</script>
<style lang="less">

</style>
