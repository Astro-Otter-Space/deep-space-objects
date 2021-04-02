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
        <h1 class="Dso__title">Titre test</h1>
        <figure>
          <img :src="coverImage" v-bind:class="headerImgCover" v-bind:alt="altImage" />
          <figcaption>{{ altImage }}</figcaption>
        </figure>
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
        default: '/build/images/default.png',
        type: String
      },
      title: {
        default: '',
        type: String
      }
      altImage: {
        default: '',
        type: String
      }
    }
  }
</script>
