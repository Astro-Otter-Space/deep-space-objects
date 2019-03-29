<template>
  <div class="pagination">
    <div class="counter">{{currentPage}} / {{lastPage}}</div>
    <a v-bind:href="linkPrevious" title="Last page">
      <button class="paginate left" v-on:click="slide(currentPage-1)" data-state="disabled"><i></i><i></i></button>
    </a>
    <a v-bind:href="linkNext" title="Next page">
      <button class="paginate right" v-on:click="slide(1)" data-state="disabled"><i></i><i></i></button>
    </a>
  </div>
</template>
<script>
  export default {
    name: "Pagination",
    data() {
      return {
        index: 0
      }
    },
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
    },
    methods: {
      slide(offset) {
        let index =  Math.min( Math.max( this.index + offset, 0 ), this.lastPage - 1 );

        let pLeft = document.querySelector('.paginate.left' );
        let pRight = document.querySelector('.paginate.right' );

        pLeft.setAttribute( 'data-state', index === 0 ? 'disabled' : '' );
        pRight.setAttribute( 'data-state', index === this.lastPage - 1 ? 'disabled' : '' );
      }
    },
    mounted: function() {
      let defaultOffset = this.currentPage-1;
      this.slide(defaultOffset);
    }
  }
</script>