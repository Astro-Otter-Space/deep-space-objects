"use strict";
import Vue from 'vue';

import Simplert from 'vue2-simplert-plugin'
Vue.use(Simplert);

new Vue({
  el: "#btnConst",
  name: "btn-const",
  methods: {
    openSimplert: function(event) {
      let customObjet = JSON.parse(event.target.dataset.filters);
      this.$refs.simplert.openSimplert(customObjet)
    }
  }
});

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

