<template>
    <span>
        <div id="btn-weapon" title="Weapon" class="bubble" :class="{disabled: !enabled}">
            <i class="fa fa-crosshairs"></i>
            <img id="weapon-bubble" src="img/null.png" width="25" height="25">
        </div>
    </span>
</template>
<script>
    import { mapState, mapActions } from 'vuex'
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
        mounted() {
            BubbleWeapon = new Favico({
                elementId: 'weapon-bubble',
                animation: 'slide'
            })
        },
        watch: {
            'me.score': (score) => {
                console.info('[Bubble] Change score :' + score)
                BubbleWeapon.badge(score)
            }
        },
    }
</script>
