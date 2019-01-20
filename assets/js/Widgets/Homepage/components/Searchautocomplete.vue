<template>
  <autocomplete
    :url="url"
    :placeholder="searchPlaceholder"
    :min="2"
    param="q"
    anchor="value"
    label="label"
    :classes="customClasses"
    :onSelect="redirectToItem"
    :onInput="showDeleteEntry"
  >
  </autocomplete>
</template>
<script>
  import Autocomplete from 'vue2-autocomplete-js'
  // require('vue2-autocomplete-js/dist/style/vue2-autocomplete.css')

  export default {
    name: 'Searchautocomplete',
    components: {
      Autocomplete
    },
    props: {
      searchPlaceholder: {
        default: '',
        type: String
      },
      customClasses: {
        type: Object
      },
      url: {
        default: '/_search',
        type: String
      }
    },
    methods: {
      redirectToItem(obj) {
        window.location.replace(obj.url);
      },
      showDeleteEntry() {

        // https://stackoverflow.com/questions/6258521/clear-icon-inside-input-text https://jsbin.com/qirurohila/edit?html,css,js,console,output
        let elInput = this.$el.querySelector('.AppSearch__inputText'); //this.$refs.autocomplete
        let valueText = elInput.value;

        elInput.setAttribute('type', 'search');
        // TODO : a toggle function
        /*if (1 < valueText.length) {
          if (!elInput.classList.contains('AppSearch__inputText__x')) {
            elInput.classList.add('AppSearch__inputText__x');
          }
        } else {
          elInput.classList.remove('AppSearch__inputText__x');
        }

        elInput.addEventListener('mousemove', function(e){
          if (e.target.matches('AppSearch__inputText__x')) {
            this.classList.add('AppSearch__inputText__onX');
          }
        }, false);
        */

        elInput.addEventListener('touchstart click', function(ev) {
          //if (ev.target.matches('AppSearch__inputText__onX')) {
            this.deleteList();
          //}
        }, false);
      },
      deleteList() {
        let elInput = this.$el.querySelector('.AppSearch__inputText');
        // elInput.classList.remove('AppSearch__inputText__x AppSearch__inputText__onX');
        elInput.value = '';
      },
    }
  }
</script>
