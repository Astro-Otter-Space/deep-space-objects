<template>
  <div v-bind:class="headerBackground">
    <lazy-background
      :src="backgroundImage"
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
        backgroundImage: "https://res.weblium.site/res/5c2df42d715e560022a8ae05/5c2df5722141000022dde670_optimized",
        coverImage: "https://www.wanimo.com/veterinaire/images/articles/chat/fibrosarcome-chat.jpg",
        headerBackground: 'AppHeader__background',
        headerLazyBackground: 'AppHeader__LazyBackgroundImage',
        headerImgCover: 'AppHeader__imgCover'
      };
    }
  }
</script>
