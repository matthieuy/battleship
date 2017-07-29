<template>
    <div :title="trans('Chat')" class="bubble">
        <i class="gi gi-conversation"></i>
        <img id="chat-bubble" src="img/null.png" width="25" height="25">
    </div>
</template>
<script>
    // Import
    import { mapState } from 'vuex'

    //Bower
    import Favico from '@npm/favico.js/favico.js'
    let BubbleChat = []

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
            ]),
        },
        watch: {
            // Change number of unread message
            ['chat.unread'](nb) {
                for (let i=0; i<BubbleChat.length; i++) {
                    BubbleChat[i].badge(nb)
                }
            },
        },
        mounted() {
            BubbleChat.push(new Favico({
                elementId: 'chat-bubble',
                animated: 'pop',
                bgColor: '#000fff',
            }))
            BubbleChat.push(new Favico({
                animated: 'pop',
                bgColor: '#000000',
            }))
        },
    }
</script>
