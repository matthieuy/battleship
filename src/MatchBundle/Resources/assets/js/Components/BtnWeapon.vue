<template>
    <span>
        <div id="btn-weapon" title="Weapon" class="bubble" :class="{disabled: !enabled}" @click="toggleModal()">
            <i class="fa fa-crosshairs"></i>
            <img id="weapon-bubble" src="img/null.png" width="25" height="25">
        </div>
    </span>
</template>
<script>
    import { mapState } from 'vuex'
    import store from '../Stores/GameStore'
    import Favico from '@bower/favico.js/favico.js'

    var BubbleWeapon = null

    export default {
        computed: {
            ...mapState([
                'me',
            ]),
            enabled() {
                return store.getters.options.weapon
            }
        },
        methods: {
            toggleModal() {
                store.commit('TOGGLE_WEAPON_MODAL')
            },
        },
        mounted() {
            BubbleWeapon = new Favico({
                elementId: 'weapon-bubble',
                animation: 'slide'
            })
        },
        watch: {
            'me.score': (score) => {
                if (store.getters.options.weapon) {
                    console.info('[Bubble] Change score :' + score)
                    BubbleWeapon.badge(score)
                }
            },
        },
    }
</script>
