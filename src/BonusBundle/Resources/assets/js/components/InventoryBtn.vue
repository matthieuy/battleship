<template>
    <div :title="trans('Inventory')" class="bubble" :class="{disabled: !inventory.enabled}" @click="toggleModal()">
        <i class="gi gi-backpack"></i>
        <img id="inventory-bubble" src="img/null.png" width="25" height="25">
    </div>
</template>
<script>
    // Import
    import { mapState } from 'vuex'
    import store from "@match/js/match/store/GameStore"
    import { ACTION, MUTATION } from "@match/js/match/store/mutation-types"

    //Bower
    import Favico from '@npm/favico.js/favico.js'
    let BubbleInventory = null

    export default {
        data() {
            return {
                loaded: false,
                trans() {
                    return Translator.trans(...arguments)
                },
            }
        },
        computed: {
            ...mapState([
                'inventory', // Inventory module
            ]),
        },
        methods: {
            toggleModal() {
                if (this.inventory.enabled) {
                    store.commit(MUTATION.INVENTORY.MODAL)
                }
            },
        },
        watch: {
            // Change number of bonus
            ['inventory.nb'](nb) {
                BubbleInventory.badge(nb)
            },
            // Websocket on first load
            ['inventory.enabled'](enable) {
                if (enable && !this.loaded) {
                    this.loaded = true
                    let topicName = 'game/' + document.getElementById('slug').value + '/bonus'
                    WS.subscribeAction(topicName, 'score', (obj) => {
                        store.dispatch(ACTION.AFTER_ROCKET, {score: obj })
                    })
                    WS.addAction(topicName, 'bonus', (obj) => {
                        store.dispatch(ACTION.AFTER_ROCKET, {bonus: obj })
                    })
                }
            },
        },
        mounted() {
            BubbleInventory = new Favico({
                elementId: 'inventory-bubble',
                animated: 'popFade',
                bgColor: '#5825ff',
            })
        },
    }
</script>
