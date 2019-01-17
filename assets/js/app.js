// https://www.cloudways.com/blog/symfony-vuejs-app/
// https://fr.vuejs.org/v2/examples/select2.html
import '../css/app.scss';
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');

import Vue from 'vue';
import Header from './Widgets/App/Header'

new Vue({
  render: h => h(Header)
}).$mount(`#appHeader`);
