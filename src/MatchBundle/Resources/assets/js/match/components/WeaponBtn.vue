<template>
    <div id="btn-weapon" title="Weapon" class="bubble" :class="{disabled: !weapon.enabled}" @click="toggleModal()">
        <i class="gi gi-crossed-sabres" :class="{selected: weapon.select}"></i>
        <img id="weapon-bubble" src="img/null.png" width="25" height="25">
    </div>
</template>
<script>
    // Import
    import { mapState } from 'vuex'
    import store from "../store/GameStore"
    import { MUTATION } from "../store/modules/weapons"

    //Bower
    import Favico from '@bower/favico.js/favico.js'
    let BubbleWeapon = null

    export default {
        computed: {
            ...mapState([
                'weapon', // Weapon module
                'me',
            ]),
        },
        methods: {
            toggleModal() {
                store.commit(MUTATION.WEAPON_MODAL)
            },
        },
        watch: {
            'me.score': (score) => {
                if (store.state.weapon.enabled) {
                    BubbleWeapon.badge(score)
                }
            },
        },
        mounted() {
            BubbleWeapon = new Favico({
                elementId: 'weapon-bubble',
                animation: 'slide'
            })
        },
    }
</script>
