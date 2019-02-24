<template>
  <div>
     <div class="Dso_header">
      <image-header
        :cover-image="imageCover"
      />
    </div>
    <section class="Dso__main">
      <div class="Dso__container">
        <!--Title-->
        <h2 class="Dso__title">
          {{ titleDso }}
        </h2>

        <social-sharing
          :url="urlShare"
          :title="titleDso"
          :description="descShare"
          hashtags=""
          twitter-user=""
          inline-template
        >
          <div>
            <network network="facebook">
              <i class="fab fa-facebook"></i>
            </network>
            <network network="twitter">
              <i class="fab fa-twitter"></i>
            </network>
          </div>
        </social-sharing>

        <!--Description-->
        <a id="#description"></a>
        <div class="Dso__description" v-if="0 < descShare.length">
          <p>Lorem ipsum dolor sit amet, interdum aenean semper egestas imperdiet quisque. Diam lacus, nulla nibh quisque neque at. Velit nec maecenas quam phasellus ac et, malesuada vitae lectus. Adipiscing suspendisse, molestie sed viverra malesuada pellentesque convallis. Risus pharetra.</p>
        </div>

        <!--Table data-->
        <a id="#information"></a>
        <div class="Dso__data">
          <Table
            :columns="gridColumns"
            :data="gridData"
            :classTable="classTable"
            :classTr="classTr"
            :classTd="classTd"
          >
          </Table>
        </div>

        <!--Slider-->
        <a id="#gallery"></a>
        <div class="Dso__slider" v-if="0 < imagesDso.length">
          <h3 class="Dso__title">{{ titleGallery }}</h3>
          <images-dso-slider
            :fluxImages="imagesDso"
          />
          <div v-html="astrobinMsg"></div>
        </div>

        <!--Constellation-->
        <a id="#constellation"></a>
        <div class="Dso__list" v-if="0 < itemsDso.length">
          <h3 class="Dso__title">{{ titleConst }}</h3>
          <cards-grid
            :show-controls="false"
            :items="itemsDso"
          >
          </cards-grid>
        </div>

        <!--Sky Map-->
        <a id="#map"></a>
        <div class="Dso__map">
          <h3 class="Dso__title">{{ titleMap }}</h3>
          <div class="map"></div>
        </div>
      </div>

    </section>
  </div>
</template>

<script>
  import ImageHeader from './components/Imageheader'
  import ImagesDsoSlider from './components/ImageSlider'
  import Table from './../App/SimpleTable'
  import CardsGrid from './components/CardsGrid'

  let coverImage = document.querySelector('div[data-dso-widget]').dataset.imgcover;
  let images = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.images);
  let title = document.querySelector('div[data-dso-widget]').dataset.title;
  let titleConst = document.querySelector('div[data-dso-widget]').dataset.titleConst;
  let tabData = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.dso);
  let astrobinMsg = document.querySelector('div[data-dso-widget]').dataset.astrobinMsg;
  let dsoList = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.dsoConst);

  export default {
    name: "App",
    components: {
      ImageHeader,
      ImagesDsoSlider,
      Table,
      CardsGrid
    },
    data () {
      return {
        imageCover: coverImage,
        imagesDso: images,
        titleDso: title,
        titleGallery: "Gallery",
        titleConst: titleConst,
        titleMap: "Map",
        gridColumns: ['col0', 'col1'],
        gridData: tabData,
        classTable: "Dso__table",
        classTr: "Dso__tr",
        classTd: "Dso__td",
        urlShare: document.querySelector("link[rel='canonical']").href,
        descShare: "",
        astrobinMsg: astrobinMsg,
        itemsDso: dsoList
      }
    }
  }
</script>
