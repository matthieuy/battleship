<template>
    <div v-show="inventory.modal">
        <div class="modal-bg"></div>
        <div class="modal-wrap">
            <div class="modal-container"v-on:click.stop.prevent="close()">
                <div class="modal-content">
                    <div id="modal-bonus" v-on:click.stop.prevent="">
                        <h1 class="center">Inventory</h1>
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
    import { MUTATION } from "../store/modules/inventory"

    export default {
        computed: {
            ...mapState([
                'inventory',
            ]),
        },
        methods: {
            // Close modal
            close() {
                store.commit(MUTATION.BONUS_MODAL, false)
            },
        },
        watch: {
            // Load inventory on open modal
            ['inventory.modal'](open) {
                // Bind escape touch
                if (open || store.state.inventory.modal) {
                    $(window).on('keyup', escapeTouch)
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
            if (store.state.inventory.modal) {
                store.commit(MUTATION.BONUS_MODAL, false)
            }
            $(window).off('keyup', escapeTouch)
        }
    }
</script>
