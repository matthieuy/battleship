<template>
    <div v-show="chat.modal">
        <div class="modal-bg"></div>
        <div class="modal-wrap">
            <div class="modal-container" v-on:click.stop.prevent="close()">
                <div class="modal-content">
                    <div id="modal-chat" v-on:click.stop.prevent="">
                        <h1 class="center">{{ trans('Chat') }}</h1>
                        <div class="clear"></div>

                        <div class="large-12 column">
                            <div class="row">
                                <div class="overflow">
                                    <div class="tab">
                                        <ul>
                                            <li :class="{selected: chat.active_tab == 'general'}" @click="change_tab({id: 'general'})">
                                                <div>{{ trans('General') }} <span class="label" v-show="chat.unread_tab.general > 0">{{ chat.unread_tab.general }}</span></div>
                                            </li>
                                            <li v-if="team !== 'false'" :class="{selected: chat.active_tab == 'team'}" @click="change_tab({id:'team'})">
                                                <div>{{ trans('Team') }} <span class="label" v-show="chat.unread_tab.team > 0">{{ chat.unread_tab.team }}</span></div>
                                            </li>
                                            <li :class="{selected: chat.active_tab == tab.id}" v-for="tab in chat.open_tab" @click="change_tab({id: tab.id})">
                                                <div>{{ tab.name }} <span class="label" v-show="chat.unread_tab[tab.id] > 0">{{ chat.unread_tab[tab.id] }}</span> <span class="close" v-on:click.stop="close_tab(tab.id)">&times;</span></div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="messages" id="messages">
                                        <div class="message" v-for="msg in messages">
                                            <div v-if="!msg.author_id">
                                                <span class="msg-system">{{ trans(msg.text, msg.context) }}</span>
                                            </div>
                                            <div v-if="msg.author_id">
                                                <span class="author" @click="change_tab({id: msg.author_id, name: msg.author_name})">{{ msg.author_name }}</span> :
                                                <span class="msg-content">{{ msg.text }}</span>
                                            </div>
                                        </div>
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
                'userId',
            ]),
            // Get message (for the current tab)
            messages() {
                return this.chat.messages.filter((message) => message.tab === this.chat.active_tab)
            },
        },
        methods: {
            // Close modal
            close() {
                this.$store.commit(MUTATION.CHAT.MODAL, false)
            },
            // Change active tab
            change_tab(tab) {
                if (tab.id !== this.chat.active_tab && tab.id !== this.userId) {
                    this.$store.commit(MUTATION.CHAT.CHANGE_TABS, tab)
                    this.$store.dispatch(ACTION.CHAT.MARK_READ, tab.id)
                }
            },
            // Close a tab
            close_tab(tabId) {
                this.$store.commit(MUTATION.CHAT.CLOSE_TABS, tabId)
            },
            // Key up in input msg
            keyup(e) {
                if (e.which === 13) {
                    this.send()
                }
            },
            // Send a message
            send() {
                if (this.message !== '') {
                    let self = this
                    WS.callRPC('chat/send', {msg: this.message, chan: this.chat.active_tab}, function(obj) {
                        if (obj.success) {
                            self.message = ''
                        }
                    })
                }
            },
        },
        watch: {
            // Update messages list : scroll bottom
            messages() {
                setTimeout(function() {
                let element = document.getElementById('messages')
                element.scrollTop = element.scrollHeight
                }, 50)
            },
            // Open/Close modal
            ['chat.modal'](open) {
                if (open) {
                    let self = this
                    let escapeTouch = function(e) {
                        if (e.which === 27) {
                            if (self.$store.state.chat.modal) {
                                self.$store.commit(MUTATION.CHAT.MODAL, false)
                            }
                            $(window).off('keyup', escapeTouch)
                        }
                    }

                    $(window).on('keyup', escapeTouch)
                    $('#input-msg').focus()
                    $('#container').css({
                        overflow: 'hidden',
                        position: 'fixed',
                    })

                    this.$store.dispatch(ACTION.CHAT.MARK_READ, this.chat.active_tab)
                } else {
                    $('#container').removeAttr('style')
                }
            },
        },
        mounted() {
            if (!this.chat.disabled) {
                let slug = document.getElementById('slug').value
                let lastId = localStorage.getItem('chat_'+slug+'_id') || 0

                // Load local message
                this.$store.dispatch(ACTION.CHAT.LOAD)

                // RPC Call
                WS.callRPC('chat/get', {last: lastId}, (obj) => {
                    this.$store.dispatch(ACTION.CHAT.RECEIVE, obj.messages)
                })

                // Websocket subscribe
                WS.subscribeAction('chat/' + slug, 'message', (message) => {
                    this.$store.dispatch(ACTION.CHAT.RECEIVE, [message])
                })
            }
        },
    }
</script>
<style lang="less">
    #modal-chat .overflow {
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

            .label {
                border-radius: 50%;
                background-color: #656363;
                color: #FFFFFF;
                padding: 1px 5px;
                margin-top: -1px;
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
        padding: 5px 10px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0,0,0,.5);
        background: #fff;
    }

    .messages {
        max-height: 200px;
        overflow-y: auto;
    }

    #input-msg {
        padding: 0 5px;
        margin-top: 10px;
        min-height: 1em;
    }

    .message {
        .msg-system {
            font-style: italic;
            color: #067a1b;
        }

        .author {
            text-decoration: underline;
            cursor: pointer;
        }
    }
</style>
