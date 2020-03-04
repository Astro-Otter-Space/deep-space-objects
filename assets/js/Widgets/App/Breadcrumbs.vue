<template>
  <div class="breadcrumb" v-if="0 < Object.keys(links).length">
    <span v-for="(link, key, index) in links" :key="key"  v-if="index !== Object.keys(links).length - 1">
      <a :href="link.url" v-bind:title="link.label">
        {{link.label}}
      </a>
    </span>
    <span v-else>{{link.label}}</span>
  </div>
</template>

<script>
  export default {
    name: "Breadcrumbs",
    props: {
      links: {
        type: Object,
        default: () => []
      }
    }
  }
</script>
