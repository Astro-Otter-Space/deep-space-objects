"use strict";
import Vue from 'vue';
import { default as PopupSocialNetwork } from "./components/popup_social_networks";

new Vue({
  el: "#popup1",
  name: "popup-social-network",
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
