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
                console.log('receive_data', obj)

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
                WS.callRPC('run/shoot', {
                    x: box.x,
                    y: box.y,
                })
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
                    if (me.position == box.player) {
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
                    if (me.position == box.shoot) {
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
    @widthBox: 20px;
    #grid {
        margin: 20px auto;
        background-color: #002a98;
        cursor: pointer;
        -moz-user-select: none;
        -o-user-select: none;
        -khtml-user-select: none;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select : none;
        .grid-line {
            clear: left;
            float: left;
        }
        .box {
            display: block;
            float: left;
            width: @widthBox;
            height: @widthBox;
            padding: 0;
            .explose {
                width: 100%;
                height: 100%;
                display: block;
            }
        }
    }
    .boat { background-image: url('/img/boat20.png'); }
    .explose { background-image: url('/img/explose-20.png'); }
    .miss { background-position: (@widthBox * -11) 0; }
    .miss.animated { animation: explose-ice 2s steps(12) 1; }
    .hit { background-position: (@widthBox * 6) @widthBox; }
    .hit.animated { animation: explose-fire 2s steps(12) infinite; }

    @keyframes explose-ice {
        0% { background-position: 0 0; }
        100% { background-position: (@widthBox * -12) 0; }
    }
    @keyframes explose-fire {
        0% { background-position: 0 @widthBox; }
        100% { background-position: (@widthBox * 12) @widthBox; }
    }

    .img1 { background-position: (@widthBox * 0) (@widthBox * 0); }
    .img2 { background-position: (@widthBox * -1) (@widthBox * 0); }
    .img3 { background-position: (@widthBox * -2) (@widthBox * 0); }
    .img4 { background-position: (@widthBox * -3) (@widthBox * 0); }
    .img5 { background-position: (@widthBox * -4) (@widthBox * 0); }
    .img6 { background-position: (@widthBox * -5) (@widthBox * 0); }
    .img7 { background-position: (@widthBox * -6) (@widthBox * 0); }
    .img8 { background-position: (@widthBox * -7) (@widthBox * 0); }

    .img9 { background-position: (@widthBox * 0) (@widthBox * -1); }
    .img10 { background-position: (@widthBox * -1) (@widthBox * -1); }
    .img11 { background-position: (@widthBox * -2) (@widthBox * -1); }
    .img12 { background-position: (@widthBox * -3) (@widthBox * -1); }
    .img13 { background-position: (@widthBox * -4) (@widthBox * -1); }
    .img14 { background-position: (@widthBox * -5) (@widthBox * -1); }
    .img15 { background-position: (@widthBox * -6) (@widthBox * -1); }
    .img16 { background-position: (@widthBox * -7) (@widthBox * -1); }

    .img17 { background-position: (@widthBox * 0) (@widthBox * -2); }
    .img18 { background-position: (@widthBox * -1) (@widthBox * -2); }
    .img19 { background-position: (@widthBox * -2) (@widthBox * -2); }
    .img20 { background-position: (@widthBox * -3) (@widthBox * -2); }
    .img21 { background-position: (@widthBox * -4) (@widthBox * -2); }
    .img22 { background-position: (@widthBox * -5) (@widthBox * -2); }
    .img23 { background-position: (@widthBox * -6) (@widthBox * -2); }
    .img24 { background-position: (@widthBox * -7) (@widthBox * -2); }

    .img25 { background-position: (@widthBox * 0) (@widthBox * -3); }
    .img26 { background-position: (@widthBox * -1) (@widthBox * -3); }
    .img27 { background-position: (@widthBox * -2) (@widthBox * -3); }
    .img28 { background-position: (@widthBox * -3) (@widthBox * -3); }
    .img29 { background-position: (@widthBox * -4) (@widthBox * -3); }
    .img30 { background-position: (@widthBox * -5) (@widthBox * -3); }
    .img31 { background-position: (@widthBox * -6) (@widthBox * -3); }
    .img32 { background-position: (@widthBox * -7) (@widthBox * -3); }

    .img33 { background-position: (@widthBox * 0) (@widthBox * -4); }
    .img34 { background-position: (@widthBox * -1) (@widthBox * -4); }
    .img35 { background-position: (@widthBox * -2) (@widthBox * -4); }
    .img36 { background-position: (@widthBox * -3) (@widthBox * -4); }
    .img37 { background-position: (@widthBox * -4) (@widthBox * -4); }
    .img38 { background-position: (@widthBox * -5) (@widthBox * -4); }
    .img39 { background-position: (@widthBox * -6) (@widthBox * -4); }
    .img40 { background-position: (@widthBox * -7) (@widthBox * -4); }

    .img41 { background-position: (@widthBox * 0) (@widthBox * -5); }
    .img42 { background-position: (@widthBox * -1) (@widthBox * -5); }
    .img43 { background-position: (@widthBox * -2) (@widthBox * -5); }
    .img44 { background-position: (@widthBox * -3) (@widthBox * -5); }
    .img45 { background-position: (@widthBox * -4) (@widthBox * -5); }
    .img46 { background-position: (@widthBox * -5) (@widthBox * -5); }
    .img47 { background-position: (@widthBox * -6) (@widthBox * -5); }
    .img48 { background-position: (@widthBox * -7) (@widthBox * -5); }

    .img49 { background-position: (@widthBox * 0) (@widthBox * -6); }
    .img50 { background-position: (@widthBox * -1) (@widthBox * -6); }
    .img51 { background-position: (@widthBox * -2) (@widthBox * -6); }
    .img52 { background-position: (@widthBox * -3) (@widthBox * -6); }
    .img53 { background-position: (@widthBox * -4) (@widthBox * -6); }
    .img54 { background-position: (@widthBox * -5) (@widthBox * -6); }
    .img55 { background-position: (@widthBox * -6) (@widthBox * -6); }
    .img56 { background-position: (@widthBox * -7) (@widthBox * -6); }
</style>
