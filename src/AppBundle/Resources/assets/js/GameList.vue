<template>
    <div class="table-list">
        <h3 class="center" v-show="!loaded">{{ trans('Loading list...') }}</h3>
        <table class="large-7 large-centered small-11" v-show="waiting.length">
            <thead>
            <tr>
                <th colspan="2">{{ trans('Waiting games') }}</th>
            </tr>
            <tr>
                <th>{{ trans('Games') }}</th>
                <th class="right">{{ trans('Players') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="game in waiting" @click="open(game)">
                <td>
                    <div class="name">{{ game.name }}</div>
                    <div class="infos">
                        <span class="date">{{ trans('create_on') }} <span class="datetime" :title="convertTimestamp(game.date)"></span> {{ trans('by_author', {author: game.creatorName}) }}</span>
                    </div>
                </td>
                <td :style="{color: (game.nb >= game.max) ? '#FF0000' : '#009933'}">
                    <div class="right">{{ game.nb }} / {{ game.max }}</div>
                </td>
            </tr>
            </tbody>
        </table>

        <table class="large-7 large-centered small-11" v-show="running.length">
            <thead>
            <tr>
                <th colspan="2">{{ trans('Games in progress') }}</th>
            </tr>
            <tr>
                <th>{{ trans('Games') }}</th>
                <th class="right">{{ trans('Tour') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="game in running" @click="open(game)">
                <td>
                    <div class="name">{{ game.name }}</div>
                    <div class="infos">
                        <div class="date">{{ trans('start_on')}} <span class="datetime" :title="convertTimestamp(game.date)"></span>
                            {{ trans('by_author', {author: game.creatorName}) }}
                        </div>
                        {{ trans('shoot_on') }} <span class="datetime" :title="convertTimestamp(game.last)"></span>
                    </div>
                </td>
                <td>
                    <div class="right" v-for="player in game.tour" :class="{tour: player.id == userid }">{{ player.name }}</div>
                </td>
            </tr>
            </tbody>
        </table>

        <table class="large-7 large-centered small-11" v-show="end.length">
            <thead>
            <tr>
                <th colspan="2">{{ trans('Games over') }}</th>
            </tr>
            <tr>
                <th>{{ trans('Games') }}</th>
                <th class="right">{{ trans('Winners') }}</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="game in end" @click="open(game)">
                <td>
                    <div class="name">{{ game.name }}</div>
                    <div class="infos">
                            <span class="date">
                                <div>{{ trans('start_on') }} <span class="datetime" :title="convertTimestamp(game.date)"></span></div>
                                <div>{{ trans('finish_on') }} <span class="datetime" :title="convertTimestamp(game.enddate)"></span></div>
                            </span>
                    </div>
                </td>
                <td>
                    <div class="right" v-for="playerName in game.tour">{{ playerName }}</div>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="center" v-show="loaded && !list.length">
            <strong>{{ trans('no_game') }}</strong>
        </div>
    </div>
</template>
<script>
  import Routing from '@app/js/routing'
  require('@app/js/timeago.js')
  /* global Translator, WS, $ */

  export default {
    props: {
      userid: {type: String},
    },
    data () {
      return {
        list: [],
        loaded: false,
        trans () {
          return Translator.trans(...arguments)
        },
      }
    },
    computed: {
      // Get waiting game list
      waiting () {
        return this.list.filter((game) => {
          return game.status === 0
        })
      },
      // Get running game list
      running () {
        return this.list.filter((game) => {
          return game.status === 1
        })
      },
      // Get game over list
      end () {
        return this.list.filter((game) => {
          return game.status === 2
        })
      },
    },
    methods: {
      // Receive game list
      receiveList (list) {
        this.list = list
        this.loaded = true
      },
      // Open game
      open (game) {
        document.location.href = Routing.generate('match.display', { slug: game.slug })
      },
      // Convert timestamp to ISO date
      convertTimestamp (timestamp) {
        let date = new Date(timestamp * 1000)
        return date.toISOString()
      },
    },
    mounted () {
      // Socket
      WS.subscribeAction('homepage', 'games', (obj) => {
        this.receiveList(obj)
      })
      WS.connect()
    },
    // On update : reset timeago
    updated () {
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
            animation-timing-function: cubic-bezier(1.0, 0, 0, 1.0);

            -webkit-animation-name: blinker;
            -webkit-animation-duration: 1s;
            -webkit-animation-iteration-count: infinite;
            -webkit-animation-direction: alternate;
            -webkit-animation-timing-function: cubic-bezier(1.0, 0, 0, 1.0);
        }
    }

    .right {
        text-align: right;
    }

    @-webkit-keyframes blinker {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }

    @keyframes blinker {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
</style>
