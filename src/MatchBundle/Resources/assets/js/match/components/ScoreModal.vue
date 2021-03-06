<template>
    <div v-show="score.modal">
        <div class="modal-bg"></div>
        <div class="modal-wrap">
            <div class="modal-container"v-on:click.stop.prevent="close()">
                <div class="modal-content">
                    <div id="modal-score" v-on:click.stop.prevent="">
                        <h1 class="center">{{ trans('Score') }}</h1>
                        <div class="clear"></div>

                        <div class="large-12 column">
                            <div class="row">
                                <div class="overflow">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th width="270">{{ trans('Players') }}</th>
                                                <th>{{ trans('Lifes') }}</th>
                                                <th>{{ trans('Torpedo') }}</th>
                                                <th>{{ trans('Destroyer') }}</th>
                                                <th>{{ trans('Cruiser') }}</th>
                                                <th>{{ trans('Aircraft') }}</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <td colspan="6" id="chrono">
                                                    <span v-show="score.penalty">{{ trans('penalty_in') }}</span>
                                                    <span v-show="!score.penalty">{{ trans('shoot_on') }} :</span>
                                                    <span :title="datePenalty"></span>
                                                </td>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                            <tr v-for="team in teams">
                                                <td>
                                                    <ul>
                                                        <li class="player-line" v-for="player in team.players">
                                                            <span class="name" :class="{dead: player.life <= 0 }">{{ player.name }}</span>
                                                            <span class="lbl" :class="{dead: player.life <= 0, tour: player.tour }">{{ player.position + 1 }}</span>
                                                            <div class="avatar-content" :style="'border-color: #'+player.color+';'"><img class="avatar" :src="'/user/'+player.userId+'-50.png'"></div>
                                                        </li>
                                                    </ul>
                                                </td>
                                                <td>
                                                    <ul>
                                                        <li v-for="player in team.players">{{ player.life }}</li>
                                                    </ul>
                                                </td>
                                                <td>
                                                    <ul>
                                                        <li v-for="player in team.players">{{ player.boats[2] }}</li>
                                                    </ul>
                                                </td>
                                                <td>
                                                    <ul>
                                                        <li v-for="player in team.players">{{ player.boats[3] }}</li>
                                                    </ul>
                                                </td>
                                                <td>
                                                    <ul>
                                                        <li v-for="player in team.players">{{ player.boats[4] }}</li>
                                                    </ul>
                                                </td>
                                                <td>
                                                    <ul>
                                                        <li v-for="player in team.players">{{ player.boats[5] }}</li>
                                                    </ul>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="large-12 center">
                            <div class="row btn-action">
                                <button class="close button alert small-10 large-3" @click="close()">
                                    <i class="fa fa-close"></i>
                                    {{ trans('Close') }}
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
    import { mapState, mapGetters } from 'vuex'
    import { MUTATION } from "../store/mutation-types"

    require('@app/js/jquery.timeago.js')

    export default {
        props: {
            time: {type: String},
        },
        data() {
            return {
                latency: 0,
                trans() {
                    return Translator.trans(...arguments)
                },
            }
        },
        computed: {
            ...mapState([
                'score',
            ]),
            ...mapGetters([
                'teams',
            ]),
            datePenalty() {
                let date = new Date((this.score.chrono + this.latency) * 1000)
                $('#chrono span').timeago('updateFromDOM')
                return date.toISOString()
            },
        },
        methods: {
            // Close modal
            close() {
                this.$store.commit(MUTATION.SCORE.MODAL, false)
            },
        },
        watch: {
            // Subscribe/Unsubscribe WS when open/close modal
            ['score.modal'](open) {
                let topicName = 'game/' + document.getElementById('slug').value + '/score'
                if (open) {
                    let self = this
                    WS.subscribeAction(topicName, 'scores', (obj) => {
                        self.$store.commit(MUTATION.SCORE.SET_LIST, obj)
                    })

                    $('#container').css({
                        overflow: 'hidden',
                        position: 'fixed',
                    })
                    $('#chrono span').timeago('updateFromDOM')

                    let escapeTouch = function(e) {
                        if (e.which === 27) {
                            if (self.$store.state.score.modal) {
                                self.$store.commit(MUTATION.SCORE.MODAL, false)
                            }
                            $(window).off('keyup', escapeTouch)
                        }
                    }
                    $(window).on('keyup', escapeTouch)
                } else {
                    WS.unsubscribe(topicName)
                    $('#container').removeAttr('style')
                    $('#chrono span').timeago('dispose')
                }
            },
            // Update chrono
            ['score.chrono'](chrono) {
                $('#chrono span').timeago('updateFromDOM')
            },
        },
        mounted() {
            this.latency = parseInt(parseInt(this.time) - (Date.now() / 1000))
        },
    }
</script>
<style lang="less">
    @avatar-size: 50px;
    #modal-score {
        li {
            margin-bottom: 10px;
            line-height: @avatar-size;
            &:last-child {
                margin-bottom: 0;
            }
        }

        table {
            margin: 0 auto 15px auto;
            width: 95%;
            border: 1px solid #000;
            min-width: 700px;

            th {
                text-align: center;
                display: table-cell;
                background-color: #DDD;
                padding: 10px;
                border: 1px solid #000;
                font-weight: bold;

                &:first-child {
                    min-width: 150px;
                }
            }

            td {
                background-color: #FFF;
                border: 1px solid #000;
                text-align: center;
                padding: 10px;

                &:first-child {
                    text-align: right;
                    padding-right: 10px;
                }
            }
        }

        .lbl {
            background-color: #DDD;
            color: #000;
            padding: 1px 5px;
            border-radius: 10px;

            &.tour {
                color: #FFF;
                background-color: #5da423;
            }
            &.dead {
                color: #FFF;
                background-color: #c60f13;
            }
        }
        .name {
            float: left;
        }
        .name.dead {
            text-decoration: line-through;
        }

        #chrono {
            text-align: center;
        }

        .avatar-content {
            display: inline-block;
            width: @avatar-size;
            height: @avatar-size;
            border-radius: @avatar-size/2;
            vertical-align: middle;
            margin-left: 10px;
            border: 3px solid #FFF;
        }
        .avatar {
            width: 100%;
            height: 100%;
            border-radius: @avatar-size/2;
        }
    }
</style>
