<template>
    <div title="Inventory" class="bubble" :class="{disabled: !inventory.enabled}" @click="toggleModal()">
        <i class="gi gi-backpack"></i>
        <img id="inventory-bubble" src="img/null.png" width="25" height="25">
    </div>
</template>
<script>
    // Import
    import { mapState } from 'vuex'
    import store from "../store/GameStore"
    import { MUTATION } from "../store/modules/inventory"

    //Bower
    import Favico from '@bower/favico.js/favico.js'
    let BubbleInventory = null

    export default {
        computed: {
            ...mapState([
                'inventory', // Inventory module
                'me',
            ]),
        },
        methods: {
            toggleModal() {
                if (this.inventory.enabled) {
                    store.commit(MUTATION.BONUS_MODAL)
                }
            },
        },
        watch: {
            'me.nbBonus': (nb) => {
                BubbleInventory.badge(nb)
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
