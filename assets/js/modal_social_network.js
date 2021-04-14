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
      svgBtnColor: '#3e3d40'
    }
  },
  methods: {
    hidePopin: () => {
      PopupSocialNetwork.hidePopin()
    },
    closeAndSetCookie: () => {
      PopupSocialNetwork.setCookieAndClosePopin()
    }
  },
  mounted() {
    document.onreadystatechange = () => {
      if (document.readyState === "complete") {
        PopupSocialNetwork.init();
      }
    }
  }
});
