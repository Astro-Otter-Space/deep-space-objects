"use strict";
import Vue from 'vue';

//import Simplert from 'vue2-simplert-plugin'
//Vue.use(Simplert);

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

/**
new Vue({
  el: "#btnCatalog",
  name: "btn-catalog",
  methods: {
    openSimplert: function(event) {
      let customObjet = JSON.parse(event.target.dataset.filters);
      this.$refs.simplert.openSimplert(customObjet)
    }
  }
});

new Vue({
  el: "#btnType",
  name: "btn-type",
  methods: {
    openSimplert: function(event) {
      let customObjet = JSON.parse(event.target.dataset.filters);
      this.$refs.simplert.openSimplert(customObjet)
    }
  }
});

**/
