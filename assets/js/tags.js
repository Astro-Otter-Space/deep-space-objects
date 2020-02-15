import Vue from 'vue';

import DsoTags from './Widgets/Observation/components/DsoTags'
/**
 * TAGS
 * Waiting for https://github.com/voerro/vue-tagsinput/pull/46
 */
new Vue({
  render: h => h(DsoTags),
}).$mount(`#elTags`)
