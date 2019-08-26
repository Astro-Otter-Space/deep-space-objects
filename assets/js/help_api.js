"use strict";
import Vue from 'vue';

import Simplert from 'vue2-simplert-plugin'
Vue.use(Simplert);

new Vue({
  el: "#btnConst",
  name: "btn-const",
  methods: {
    openSimplert (customObjet) {
      this.$refs.simplert.openSimplert(customObjet)
    }
  }
});

new Vue({
  el: "#btnCatalog",
  name: "btn-catalog",
  methods: {
    openSimplert (customObjet) {
      this.$refs.simplert.openSimplert(customObjet)
    }
  }
});

new Vue({
  el: "#btnType",
  name: "btn-type",
  methods: {
    openSimplert (customObjet) {
      this.$refs.simplert.openSimplert(customObjet)
    }
  }
});

