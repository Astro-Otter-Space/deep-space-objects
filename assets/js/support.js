import Vue from 'vue';

import buttonsLinks from "./Widgets/App/buttonsLinks";

new Vue({
  el: '#appLinks',
  components: {
    buttonsLinks
  },
  template: `
    <buttons-links
        :links="this.shareLinks"
        :color="this.colorBtn"
        width-button="50"
        height-button="50"
    ></buttons-links>
  `,
  data() {
    return {
      shareLinks: JSON.parse(document.getElementById('appLinks').dataset.links),
      colorBtn:'#3e3d40'
    }
  }
});
