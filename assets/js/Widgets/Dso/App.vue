<template>
  <div id="app">
    <div class="Dso_header">
      <image-header
        :cover-image="imageCover"
      />
    </div>
    <section class="Dso__main">
      <div class="Dso__container">
        <h2 class="Dso__title">{{ titleDso }}</h2>

        <div class="Dso__description">
          <p>Lorem ipsum dolor sit amet, interdum aenean semper egestas imperdiet quisque. Diam lacus, nulla nibh quisque neque at. Velit nec maecenas quam phasellus ac et, malesuada vitae lectus. Adipiscing suspendisse, molestie sed viverra malesuada pellentesque convallis. Risus pharetra.</p>
        </div>
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
        <div class="Dso__slider">
          <h3 class="Dso__title">Gallery</h3>
          <images-dso-slider
            :fluxImages="imagesDso"
          />
        </div>
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

  let coverImage = document.querySelector('div[data-dso-widget]').dataset.imgcover;
  let images = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.images);
  let title = document.querySelector('div[data-dso-widget]').dataset.title;
  let tabData = JSON.parse(document.querySelector('div[data-dso-widget]').dataset.dso);

  export default {
    name: "App",
    components: {
      ImageHeader,
      ImagesDsoSlider,
      Table
    },
    data () {
      return {
        imageCover: coverImage,
        imagesDso: images,
        titleDso: title,
        titleMap: "Map",
        gridColumns: ['col0', 'col1'],
        gridData: tabData,
        classTable: "Dso__table",
        classTr: "Dso__tr",
        classTd: "Dso__td",
      }
    }
  }
</script>
