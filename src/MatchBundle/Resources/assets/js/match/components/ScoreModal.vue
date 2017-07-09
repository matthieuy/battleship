<template>
    <div v-show="score.modal">
        <div class="modal-bg"></div>
        <div class="modal-wrap">
            <div class="modal-container"v-on:click.stop.prevent="close()">
                <div class="modal-content">
                    <div id="modal-score" v-on:click.stop.prevent="">
                        <h1 class="center">Score</h1>
                        <div class="clear"></div>

                        <div class="large-12 column">
                            <div class="row">
                                <div class="overflow">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>Players</th>
                                                <th width="80">Lifes</th>
                                                <th width="100">Torpedo</th>
                                                <th width="100">Destroyer</th>
                                                <th width="100">Cruiser</th>
                                                <th width="110">Aircraft</th>
                                                <th>Bonus</th>
                                            </tr>
                                        </thead>
                                        <!--
                                        <tfoot>
                                            <tr>
                                                <td colspan="6" id="chrono"></td>
                                            </tr>
                                        </tfoot>
                                        -->
                                        <tbody>
                                            <tr v-for="team in teams">
                                                <td>
                                                    <ul>
                                                        <li v-for="player in team.players">
                                                            <span class="name" :class="{dead: player.life <= 0 }">{{ player.name }}</span>
                                                            <span class="lbl" :class="{dead: player.life <= 0, tour: player.tour }">{{ player.position + 1 }}</span>
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
                                                <td>
                                                    <ul>
                                                        <li v-for="player in team.players">{{ player.probability }}%</li>
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
    import { mapState, mapGetters } from 'vuex'
    import store from "../store/GameStore"
    import { MUTATION } from "../store/modules/score"

    export default {
        computed: {
            ...mapState([
                'score',
            ]),
            ...mapGetters([
                'teams',
            ])
        },
        methods: {
            // Close modal
            close() {
                store.commit(MUTATION.SCORE_MODAL, false)
            },
        },
        watch: {
            // Subscribe/Unsubscribe WS when open/close modal
            ['score.modal'](open) {
                let topicName = 'game/' + document.getElementById('slug').value + '/score'
                if (open) {
                    WS.subscribeAction(topicName, 'scores', (obj) => {
                        store.commit(MUTATION.RECEIVE_LIST, obj)
                    })
                    $(window).on('keyup', escapeTouch)
                    $('#container').css({
                        overflow: 'hidden',
                        position: 'fixed',
                    })
                } else {
                    WS.unsubscribe(topicName)
                    $('#container').removeAttr('style')
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
            if (store.state.score.modal) {
                store.commit(MUTATION.SCORE_MODAL, false)
            }
            $(window).off('keyup', escapeTouch)
        }
    }
</script>
<style lang="less">
    #modal-score {
        li {
            margin-bottom: 10px;
            &:last-child {
                margin-bottom: 0;
            }
        }

        table {
            margin: 0 auto 15px auto;
            width: 95%;
            border: 1px solid #000;

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
        .name.dead {
            text-decoration: line-through;
        }

        #chrono {
            text-align: center;
        }
    }
</style>
