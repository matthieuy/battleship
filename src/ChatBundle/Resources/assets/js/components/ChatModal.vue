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
                                            <li class="selected"><div>{{ trans('General') }}</div></li>
                                            <li><div>{{ trans('Team') }}</div></li>
                                            <li v-for="i in 3"><div>User {{ i }}<span class="close">&times;</span></div></li>
                                        </ul>
                                    </div>
                                    <div class="messages">
                                        dsdssdsdsds
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clear"></div>

                        <div class="large-12 center">
                            <div class="row btn-action">
                                <button class="close button alert small-10 large-3 round" @click="close()">
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
        data() {
            return {
                trans() {
                    return Translator.trans(...arguments)
                },
            }
        },
        computed: {
            ...mapState([
                'chat', // Chat module
            ])
        },
        methods: {
            // Close modal
            close() {
                this.$store.commit(MUTATION.CHAT.MODAL, false)
            },
        },
        watch: {
            ['chat.modal'](open) {
                if (open) {
                    $(window).on('keyup', escapeTouch)
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
            }
        }
    }
    .messages {
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
</style>
