<template>
    <div v-show="chat.modal">
        <div class="modal-bg"></div>
        <div class="modal-wrap">
            <div class="modal-container"v-on:click.stop.prevent="close()">
                <div class="modal-content">
                    <div id="modal-score" v-on:click.stop.prevent="">
                        <h1 class="center">{{ trans('Chat') }}</h1>
                        <div class="clear"></div>

                        <div class="large-12 column">
                            <div class="row">
                                <div class="overflow">
                                    <div class="tab">
                                        <ul>
                                            <li :class="{selected: chat.active_tab == 'general'}" @click="change_tab('general')"><div>{{ trans('General') }}</div></li>
                                            <li v-if="team !== 'false'" :class="{selected: chat.active_tab == 'team'}" @click="change_tab('team')"><div>{{ trans('Team') }}</div></li>
                                            <li :class="{selected: chat.active_tab == tab.id}" v-for="tab in chat.open_tab" @click="change_tab(tab.id)">
                                                <div>{{ tab.name }}<span class="close" v-on:click.stop="close_tab(tab.id)">&times;</span></div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="messages">
                                    </div>

                                    <input id="input-msg" type="text" autocomplete="off" v-model="message" autofocus @keyup="keyup($event)"/>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="large-12 center">
                            <div class="row btn-action">
                                <button class="button success small-10 large-3 round" @click="send()">
                                    <i class="gi gi-square-bottle"></i>
                                    {{ trans('Send') }}
                                </button>
                                <button class="button alert small-10 large-3 round" @click="close()">
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
    import { mapState } from 'vuex'
    import { MUTATION, ACTION } from "@match/js/match/store/mutation-types"
    let store = null

    export default {
        props: {
            team: {type: String, default: 'true' },
        },
        data() {
            return {
                message: '',
                trans() {
                    return Translator.trans(...arguments)
                },
            }
        },
        computed: {
            ...mapState([
                'chat', // Chat module
            ]),
        },
        methods: {
            // Close modal
            close() {
                this.$store.commit(MUTATION.CHAT.MODAL, false)
            },
            // Change active tab
            change_tab(tabName) {
                this.$store.commit(MUTATION.CHAT.CHANGE_TABS, tabName)
            },
            // Close a tab
            close_tab(tabName) {
                this.$store.commit(MUTATION.CHAT.CLOSE_TABS, tabName)
            },
            // Key up in input msg
            keyup(e) {
                if (e.which === 13) {
                    this.send()
                }
            },
            // Send a message
            send() {
                if (this.message == '') {
                    return false
                }

                let self = this
                WS.callRPC('chat/send', {msg: this.message, chan: this.chat.active_tab}, function(obj) {
                    if (obj.success) {
                        self.message = ''
                    }
                })
            },
        },
        watch: {
            ['chat.modal'](open) {
                if (open) {
                    $(window).on('keyup', escapeTouch)
                    $('#input-msg').focus()
                }
            },
        },
        mounted() {
            store = this.$store
        },
    }

    /**
     * Press escape touch : close modal
     * @param e
     */
    function escapeTouch(e) {
        if (e.which == 27) {
            if (store.state.chat.modal) {
                store.commit(MUTATION.CHAT.MODAL, false)
            }
            $(window).off('keyup', escapeTouch)
        }
    }
</script>
<style lang="less">
    #modal-score .overflow {
        padding: 5px;
    }
    .tab {
        ul {
            list-style: none;
            padding: 0 0 0 30px;
            margin: 0;
        }
        li {
            float: left;
            position: relative;
            border-radius: 10px 10px 0 0;
            margin-left: -10px;
            text-shadow: 1px 1px 0 #bbb;
            box-shadow: 0px 0px 10px rgba(0,0,0,.5);
            &:hover {
                z-index: 1;
                div {
                    background: #ccc;
                    color: #000;
                }
            }

            div {
                display: block;
                position: relative;
                min-width: 100px;
                height: 20px;
                padding: 6px 10px 20px 0;
                border-radius: 10px 10px 0 0;
                background: #999;
                color: #444;
                text-align: center;
                text-decoration: none;
                cursor: pointer;
            }

            &.selected {
                z-index: 2;
                div {
                    z-index: 3;
                    background: #646464;
                    color: #000;
                    text-shadow: none;
                    font-weight: 500;
                    cursor: default;
                }
            }

            .close {
                margin-left: 5px;
                font-size: 80%;
                cursor: pointer;
            }
        }
    }
    .messages, #input-msg {
        margin: 0;
        position: relative;
        z-index: 1;
        clear: both;
        min-height: 200px;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0,0,0,.5);
        background: #fff;
    }

    #input-msg {
        padding: 0 5px;
        margin-top: 10px;
        min-height: auto;
    }
</style>
