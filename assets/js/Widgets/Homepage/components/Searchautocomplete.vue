<template>
  <autocomplete
    :url="url"
    :placeholder="searchPlaceholder"
    :min="2"
    param="q"
    anchor="ajaxValue"
    label="label"
    :classes="customClasses"
    :onSelect="redirectToItem"
    :onInput="onInputMethod"
    :id="id"
  >
  </autocomplete>
</template>
<script>
  import Autocomplete from 'vue2-autocomplete-js'

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
      },
      id : {
        default: '',
        type: String
      }
    },
    methods: {
      redirectToItem: function(obj) {
        window.location.replace(obj.url);
      },
      onInputMethod: function() {
        // let searchList = this.$el.querySelector('.AppSearch__list > ul');
        // if (searchList.hasChildNodes()) {
        //   while(searchList.lastElementChild) {
        //     searchList.removeChild(searchList.lastElementChild);
        //   }
        // }

        // https://stackoverflow.com/questions/6258521/clear-icon-inside-input-text https://jsbin.com/qirurohila/edit?html,css,js,console,output
        let elInput = this.$el.querySelector('.AppSearch__inputText'); //this.$refs.autocomplete
        elInput.setAttribute('type', 'search');
        elInput.addEventListener('touchstart click', function(ev) {
          this.deleteList();
        }, false);
      },
      deleteList: function() {
        let elInput = this.$el.querySelector('.AppSearch__inputText');
        elInput.value = '';

      },
    }
  }
</script>
