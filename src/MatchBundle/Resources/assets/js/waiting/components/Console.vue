<template>
    <table id="table-options" class="small-12 extendable">
        <thead>
        <tr>
            <th>
                {{ trans('Console') }}
                <div class="fa caret"></div>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <div class="console">
                    <div class="txt" v-for="txt in list">{{ txt }}</div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

</template>
<script>
  /* global Translator, WS */
  export default {
    data () {
      return {
        list: [],
        trans () {
          return Translator.trans(...arguments)
        },
      }
    },
    mounted () {
      this.list.unshift(Translator.trans('loading'))
      let topicName = 'game/' + document.getElementById('slug').value + '/wait'
      WS.subscribeAction(topicName, 'console', (txt) => this.list.unshift(txt))
    },
    watch: {
      // Scroll top on new message
      list (value) {
        document.querySelector('.console').scrollTop = 0
      },
    },
  }
</script>
<style lang="less">
    .console {
        min-height: 50px;
        max-height: 100px;
        background-color: #000000;
        color: #d4d4d4;
        padding: 5px;
        font-family: monospace, monospace;
        overflow-y: scroll;
    }

    .txt {
        margin: 2px 0;
    }
</style>
