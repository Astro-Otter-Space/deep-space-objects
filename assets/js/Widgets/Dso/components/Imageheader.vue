<template>
  <div v-bind:class="headerBackground">
    <lazy-background
      :src="coverImage"
      :blur="30"
      position="center"
      size="cover"
      v-bind:class="headerLazyBackground"
    >
      <div slot="content" slot-scope="{ visible }">
        <img :src="coverImage" v-bind:class="headerImgCover" />
      </div>
    </lazy-background>
  </div>
</template>

<script>
  export default {
    name: "Imageheader",
    data() {
      return {
        visible: true,
        headerBackground: 'AppHeader__background',
        headerLazyBackground: 'AppHeader__LazyBackgroundImage',
        headerImgCover: 'AppHeader__imgCover'
      };
    },
    props: {
      coverImage: {
        default: '',
        type: String
      }
    }
  }
</script>
