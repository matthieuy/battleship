<template>
    <div v-show="score.modal">
        <div class="modal-bg"></div>
        <div class="modal-wrap">
            <div class="modal-container"v-on:click.stop.prevent="close()">
                <div class="modal-content">
                    <div id="modal-score" v-on:click.stop.prevent="">
                        <h1 class="center">Score</h1>
                        <div class="clear"></div>

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
    import { mapState } from 'vuex'
    import store from "../store/GameStore"
    import { MUTATION } from "../store/modules/score"

    export default {
        computed: {
            ...mapState([
                'score',
            ]),
        },
        methods: {
            // Close modal
            close() {
                store.commit(MUTATION.SCORE_MODAL, false)
            },
        },
    }
</script>
<style lang="less">
    #modal-score {
        background-color: #FFF;
        max-width: 95%;
        margin: auto;
        vertical-align: middle;
    }
</style>
