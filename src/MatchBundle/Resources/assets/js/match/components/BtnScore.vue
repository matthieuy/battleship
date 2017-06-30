<template>
    <div title="Score" class="bubble">
        <i class="fa fa-bar-chart-o"></i>
        <img id="life-bubble" src="img/null.png" width="25" height="25">
    </div>
</template>
<script>
    // Import
    import { mapState } from 'vuex'
    import store from "../store/GameStore"
    import Favico from "@bower/favico.js/favico.js"

    let BubbleLife = null

    export default {
        computed: {
            ...mapState([
                'me',
            ])
        },
        watch: {
            // Update bubble when life change
            'me.life': (life) => {
                let color = (life < 10) ? '#FF0000' : (life > 20) ? '#008200' : '#B7B700';
                BubbleLife.badge(life, {bgColor: color })
            },
        },
        // Create bubble on mount
        mounted() {
            BubbleLife = new Favico({
                elementId: 'life-bubble',
                animation: 'slide'
            })
        },
    }
</script>
