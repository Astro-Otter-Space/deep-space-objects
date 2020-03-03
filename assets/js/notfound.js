import Vue from 'vue'
import Vue2TouchEvents from "vue2-touch-events";
import '../css/app.scss';
import Header from './Widgets/App/Header'
import App from './Widgets/App/404'

Vue.config.productionTip = false;
Vue.use(Vue2TouchEvents);

// Import custom icons
import SvgIcon from 'vue-svgicon'
Vue.use(SvgIcon, {
  tagName: 'svgicon'
});

// Header
new Vue({
  render: h => h(Header),
}).$mount(`#appHeader`);



new Vue({
  render: h => h(App)
}).$mount(`#appNotFound`);
