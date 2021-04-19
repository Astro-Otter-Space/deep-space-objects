"use strict";
import Vue from 'vue';
import { default as PopupSocialNetwork } from "./components/popup_social_networks";

import SvgIcon from 'vue-svgicon'
Vue.use(SvgIcon, {
  tagName: 'svgicon'
});

new Vue({
  el: "#popup1",
  name: "popup-social-network",
  data() {
    return {
      svgBtnColor: '#3e3d40',
      nbDays: 30
    }
  },
  methods: {
    hidePopin() {
      PopupSocialNetwork.hidePopin()
    },
    closeAndSetCookie() {
      PopupSocialNetwork.setDaysCookie(this.nbDays)
    }
  },
  beforeMount() {
    // document.addEventListener("touchend", this.hidePopin);
  },
  mounted() {
    this.$nextTick(() => {
      setTimeout(() => {
        PopupSocialNetwork.init();
      }, 2000);
    });
  }
});
