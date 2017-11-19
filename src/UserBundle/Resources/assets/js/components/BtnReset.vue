<template>
    <span class="button alert" @click="clear()">{{ name }} ({{ size }})</span>
</template>

<script>
    import db from '@chat/js/database'
    /* global Translator */

    export default {
      data () {
        return {
          name: Translator.trans('reset.cache'),
          size: '0kb',
        }
      },
      methods: {
        // Clear cache
        clear () {
          db.delete()
          localStorage.clear()
          this.size = 0
        },
        // Update size
        updateSize () {
          let size = 0
          let self = this

          // Localstorage size
          for (let i = 0; i < localStorage.length; i++) {
            size += localStorage.key(i).length * 2
          }

          // IndexedDB size
          db.transaction('r', db.messages, function () {
            db.messages.each(function (message) {
              size += JSON.stringify(message).length
            })
          }).then(function () {
            self.size = Math.round(size / 102.4) / 10 + 'kb'
          })
        },
      },
      // Update size on mount
      mounted () {
        this.updateSize()
      },
    }
</script>
