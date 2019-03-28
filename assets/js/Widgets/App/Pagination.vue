<template>
  <div class="paginate">
    <div class="counter">{{currentPage}} / {{lastPage}}</div>
    <a v-bind:href="linkPrevious" title="Last page">
      <button class="paginate left"><i></i><i></i></button>
    </a>
    <a v-bind:href="linkNext" title="Next page">
      <button class="paginate right"><i></i><i></i></button>
    </a>
  </div>
</template>
<script>
  export default {
    name: "Pagination",
    props: {
      currentPage: {
        type: Number,
      default: 1
      },
      lastPage: {
        type: Number,
        default: 1
      },
      linkPrevious: {
        type: String,
        default:""
      },
      linkNext: {
        type: String,
        default: ""
      }
    }
  }
</script>