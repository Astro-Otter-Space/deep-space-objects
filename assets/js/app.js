// https://www.cloudways.com/blog/symfony-vuejs-app/
// https://fr.vuejs.org/v2/examples/select2.html
import Vue from 'vue'

import switchLang from './components/SwitchLang'

new Vue({
  el: '#switch-lang',
  components: {switchLang}
}).$mount('#switchLang');
