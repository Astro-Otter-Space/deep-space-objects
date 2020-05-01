import Vue from 'vue';
import AppLastUpdate from "./Widgets/Dso/AppLastUpdate";

// Import custom icons
import SvgIcon from 'vue-svgicon'
Vue.use(SvgIcon, {
  tagName: 'svgicon'
});

new Vue({
  render: h => h(AppLastUpdate)
}).$mount('#app');
