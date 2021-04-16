"use strict";
import Vue from 'vue';

new Vue({
  el: "#app",
  name: "btn-catalog",
  data() {
    return {
      showCatalog: false,
      showConstellation: false,
      showType: false
    }
  },
  methods: {
    toggleCatalog: function() {
      this.showCatalog = !this.showCatalog;
    },
    toggleConstellation: function() {
      this.showConstellation = !this.showConstellation;
    },
    toggleType: function() {
      this.showType = !this.showType;
    }
  }
});
