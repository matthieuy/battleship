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
