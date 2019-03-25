// https://www.cloudways.com/blog/symfony-vuejs-app/
// https://fr.vuejs.org/v2/examples/select2.html
import '../css/app.scss';

import Vue from 'vue';
import Header from './Widgets/App/Header'

// Icons
import { library } from '@fortawesome/fontawesome-svg-core';
import { faBars, faFlag, faSearch } from '@fortawesome/free-solid-svg-icons';
import { faTwitter, faFacebook, faGithub } from "@fortawesome/free-brands-svg-icons";

import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
library.add(faBars, faFlag, faSearch, faTwitter, faFacebook, faGithub);
Vue.component('font-awesome-icon', FontAwesomeIcon);

// Import custom icons
import SvgIcon from 'vue-svgicon'
Vue.use(SvgIcon, {
  tagName: 'svgicon'
});

new Vue({
  render: h => h(Header),
}).$mount(`#appHeader`);
