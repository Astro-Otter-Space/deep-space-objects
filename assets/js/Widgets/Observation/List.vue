<template>
  <div>
    <section class="Dso__main">
      <div class="Dso__container Dso__noHeader">

        <h2 class="Dso__title">Observations list</h2>

        <searchautocomplete
          ref="observationsearch"
          :searchPlaceholder="Test"
          :customClasses="autoCompleteClasse"
          :url="urlSearch"
        />

        <h3 class="Dso__title">Location des observations</h3>

      </div>
    </section>
  </div>
</template>

<script>
  import Searchautocomplete from "./../Homepage/components/Searchautocomplete"

  let urlSearchObs = document.querySelector('div[data-observations-list]').dataset.searchRoute;

  export default {
    name: "App",
    components: {
      Searchautocomplete
    },
    data () {
      return {
        urlSearch: urlSearchObs,
        autoCompleteClasse: {
          wrapper: 'AppHeader__wrapper',
          input: 'AppSearch__inputText',
          list: 'AppHeader__list'
        },
      }
    }

  }
</script>
