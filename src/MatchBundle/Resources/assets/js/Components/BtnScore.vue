<template>
    <span>
        <div title="Score" class="bubble">
            <i class="fa fa-bar-chart-o"></i>
            <img id="life-bubble" src="img/null.png" width="25" height="25">
        </div>
    </span>
</template>
<script>
    import { mapState } from 'vuex'
    import store from '../Stores/GameStore'
    import Favico from '@bower/favico.js/favico.js'

    var BubbleLife = null

    export default {
        computed: {
            ...mapState([
                'me',
            ]),
        },
        mounted() {
            BubbleLife = new Favico({
                elementId: 'life-bubble',
                animation: 'slide'
            })
        },
        watch: {
            'me.life': (life) => {
                console.info('[Bubble] Change life :' + life)
                let color = (life < 10) ? '#FF0000' : (life > 20) ? '#008200' : '#B7B700';
                BubbleLife.badge(life, {bgColor: color })
            }
        }
    }
</script>
