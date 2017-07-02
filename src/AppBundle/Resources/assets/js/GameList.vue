<template>
    <div class="table-list">
        <h3 class="center" v-show="!loaded">Loading list...</h3>
        <table class="large-6 large-centered small-11" v-show="waiting.length">
            <thead>
                <tr>
                    <th colspan="2">Game don't start</th>
                </tr>
                <tr>
                    <th>Game</th>
                    <th width="100">Players</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="game in waiting" @click="open(game)">
                    <td>
                        <div class="name">{{ game.name }}</div>
                        <div class="infos">
                            <span class="date">Create <span class="datetime" :title="convertTimestamp(game.date)"></span> by {{ game.creatorName }}</span>
                        </div>
                    </td>
                    <td :style="{color: (game.nb >= game.max) ? '#FF0000' : '#009933'}">
                        {{ game.nb }} / {{ game.max }}
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="large-6 large-centered small-11" v-show="running.length">
            <thead>
                <tr>
                    <th colspan="2">Games in progress</th>
                </tr>
                <tr>
                    <th>Games</th>
                    <th width="100">Tour</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="game in running" @click="open(game)">
                    <td>
                        <div class="name">{{ game.name }}</div>
                        <div class="infos">
                            <div class="date">Start <span class="datetime" :title="convertTimestamp(game.date)"></span> by {{ game.creatorName }}</div>
                            Last shoot <span class="datetime" :title="convertTimestamp(game.last)"></span>
                        </div>
                    </td>
                    <td>
                        <div v-for="player in game.tour" :class="{tour: player.id == userid }">{{ player.name}}</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="large-6 large-centered small-11" v-show="end.length">
            <thead>
                <tr>
                    <th colspan="2">Game over</th>
                </tr>
                <tr>
                    <th>Game</th>
                    <th width="100">Winner</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="game in end" @click="open(game)">
                    <td>
                        <div class="name">{{ game.name }}</div>
                        <div class="infos">
                            <span class="date">
                                Start <span class="datetime" :title="convertTimestamp(game.date)"></span> and
                                finish <span class="datetime" :title="convertTimestamp(game.enddate)"></span>
                            </span>
                        </div>
                    </td>
                    <td>
                        <div v-for="playerName in game.tour">{{ playerName }}</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <strong v-show="loaded && !list.length">Any game</strong>
    </div>
</template>
<script>
    require('@app/js/jquery.timeago.js')

    export default {
        props: {
            userid: {type: String},
            url: {type: String},
        },
        data() {
            return {
                list: [],
                loaded: false,
            }
        },
        computed: {
            // Get waiting game list
            waiting() {
                return this.list.filter((game) => {
                    return game.status == 0
                })
            },
            // Get running game list
            running() {
                return this.list.filter((game) => {
                    return game.status == 1
                })
            },
            // Get game over list
            end() {
                return this.list.filter((game) => {
                    return game.status == 2
                })
            },
        },
        methods: {
            // Receive game list
            receiveList(list) {
                this.list = list
                this.loaded = true
            },
            // Open game
            open(game) {
                document.location.href = this.url.replace('SLUG', game.slug)
            },
            // Convert timestamp to ISO date
            convertTimestamp(timestamp) {
                let date = new Date(timestamp * 1000);
                return date.toISOString()
            },
        },
        mounted() {
            // Socket
            WS.subscribeAction('homepage', 'games', (obj) => {
                this.receiveList(obj)
            })
            WS.connect()
        },
        // On update : reset timeago
        updated() {
            $('.datetime').timeago('dispose')
            $('.datetime').timeago()
        },
    }
</script>
<style lang="less">
    .table-list {
        margin-bottom: 40px;
        td {
            text-align: center;
            vertical-align: middle;
            cursor: pointer;
        }
        .name {
            font-size: 1.4em;
            font-weight: bold;
        }
        .date {
            font-style: italic;
            display: block;
        }
        .tour {
            font-weight: bold;
            color: #F00;
            text-decoration: blink;
            animation-name: blinker;
            animation-duration: 1s;
            animation-iteration-count: infinite;
            animation-direction: alternate;
            animation-timing-function: cubic-bezier(1.0,0,0,1.0);

            -webkit-animation-name: blinker;
            -webkit-animation-duration: 1s;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-direction: alternate;
            -webkit-animation-timing-function: cubic-bezier(1.0,0,0,1.0);
        }
    }

    @-webkit-keyframes blinker {
        from { opacity: 1; }
        to { opacity: 0; }
    }

    @keyframes blinker {
        from { opacity: 1; }
        to { opacity: 0; }
    }
</style>
