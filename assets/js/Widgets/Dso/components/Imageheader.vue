<template>
  <div v-bind:class="headerBackground" v-bind:title="altImage">
    <lazy-background
      :src="coverImage"
      :blur="30"
      position="center"
      size="cover"
      v-bind:class="headerLazyBackground"
    >
      <div slot="content" slot-scope="{ visible }">
        <div v-bind:class="headerImgCover" >
          <figure>
            <img :src="coverImage" v-bind:alt="altImage" />
          </figure>
        </div>
<!--        <div v-bind:class="headerTitleCover">-->
<!--          <h1 v-bind:class="headerTitle" v-html="title"></h1>-->
<!--        </div>-->

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
        headerImgCover: 'AppHeader__imgCover',
        // headerImgCoverLeft: 'AppHeader__imgCoverLeft',
        // headerImgCoverRight: 'AppHeader__imgCoverRight',
        // headerTitleCover: 'AppHeader__titleCover',
        // headerTitle: 'AppHeader__title'
        vignette: "Vignettes__vignette"
      };
    },
    props: {
      coverImage: {
        default: '/build/images/default.png',
        type: String
      },
      title: {
        default: '',
        type: String
      },
      altImage: {
        default: '',
        type: String
      }
    }
  }
</script>
