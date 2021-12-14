<template>
  <table v-bind:class="classTable">
    <tbody>
      <tr v-bind:class="classTr" v-for="entry in data">
        <td v-bind:class="classTd" v-for="key in columns" data-dark-mode>
          <span v-html="entry[key]">{{entry[key]}}</span>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
  export default {
    name:"Table",
    props: {
      data: {
        default: () => [],
        type: Array
      },
      columns: {
        default: () => [],
        type: Array
      },
      classTable: {
        default: "",
        type: String
      },
      classTr: {
        default: "",
        type: String
      },
      classTd: {
        default: "",
        type: String
      }
    },
    computed: {
    }
  }
</script>
