<!--https://vuejsexamples.com/a-vue-js-component-for-sharing-links-to-social-networks/-->
<template>
  <social-sharing
    url="urlShare"
    title="titleShare"
    description="descShare"
    inline-template
  >
    <div>
      <network v-for="network in listNetworks" :network="network.label">
        <i v-bind:class="[fa, `fa-${network.key}`]"></i> {{ network.label }}
      </network>
    </div>
  </social-sharing>
</template>

<script>
  import SocialSharing from 'vue-social-sharing'

  export default {
    name: 'SocialSharing',
    components: {
      SocialSharing
    },
    props: {
      listNetworks: {
        default: () => [],
        type: Array
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
