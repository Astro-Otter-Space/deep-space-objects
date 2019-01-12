<!--https://vuejsexamples.com/a-vue-js-component-for-sharing-links-to-social-networks/-->
<template>
  <social-sharing
    :url="urlShare"
    :title="titleShare"
    :description="descShare"
    inline-template
  >
    <div>
      <network v-for="network in listNetworks" v-bind:network="network.label" :key="network.id">
        <i v-bind:class="[fa, `fa-${network.name}`]"></i> {{ network.label }}
      </network>
    </div>
  </social-sharing>
</template>

<script>


  export default {
    name: 'CustomSocialSharing',
    props: {
      listNetworks: {
        type: Array,
        default: () => []
      },
      urlShare: {
        default: "",
        type: String
      },
      titleShare: {
        default: "",
        type: String
      },
      descShare: {
        default: "",
        type: String
      }
    }
  }
</script>
