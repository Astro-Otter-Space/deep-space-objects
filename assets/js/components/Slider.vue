<template>
  <agile :options="options">
    <div class="slide slide--1"><h3>{{title}}</h3></div>
    <div class="slide slide--2"><h3>{{title}}</h3></div>
    <div class="slide slide--3"><h3>{{title}}</h3></div>
    <!--<div class="slide slide&#45;&#45;4"><h3>{{title}}</h3></div>-->
  </agile>
</template>

<script>
    let homeTitle = document.querySelector('div[data-home-widget]').dataset.homeTitle;
    import VueAgile from 'vue-agile'

    export default {
      name: "slider",
      components: {VueAgile},
      props: {
        title: homeTitle
      },
      data() {
        return {
          options: {
            arrows: false,
            dots: false,
            infinite: true,
            speed: 200,
            fade: true,
            autoplay: true,
            responsive: []
          }
        }
      }
    }
</script>

<style scoped lang="scss" type="text/scss">
  .agile {
    &__dots {
      bottom: 0;
      display: block;
      left: 10px;
      position: absolute;
      width: 30px;
    }

    &__dot {
      margin-bottom: 10px;

      button {
        background-color: transparent;
        border: 1px solid #fff;
        margin-top: 10px;

        &:hover {
           background-color: #fff;
        }
      }

      &--current {
        button {
          background-color: #fff;
        }
      }
    }
  }

  .slide {
    background: {
      position: center;
      size: cover;
    }
    height: 500px;

    &--1 {
      background-image: url(../../images/background/bg-1.jpg);
    }

    &--2 {
      background-image: url(../../images/background/bg-2.jpg);
    }

    &--3 {
       background-image: url(../../images/background/bg-3.jpg);
    }

    /*&--4 {*/
       /*background-image: url(../../images/background/bg-4.jpg);*/
    /*}*/

    h3 {
      font-size: 32px;
      font-weight: 300;
      left: 50%;
      margin: 0;
      position: absolute;
      top: 50%;
      transform: translate(-50%, -50%);
    }
  }
</style>
