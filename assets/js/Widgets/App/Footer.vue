<template>
  <footer class="footer">
    <div class="footer__wrapper">
      <div></div>

      <div class="footer__share">
        <ul>
          <li v-for="btnShare in btnsShare">
            <a v-bind:href="btnShare.path" v-bind:title="btnShare.label">
              <svgicon v-bind:name="btnShare.icon_class" width="30" height="30" ></svgicon>
            </a>
          </li>
        </ul>
      </div>

      <div>

      </div>
    </div>

  </footer>
</template>

<script>
  import './../Icons/index';

  let shareButtons = JSON.parse(document.getElementById('appFooter').dataset.share);

  export default {
    name: "Footer",
    data() {
      return {
        btnsShare: shareButtons
      }
    }
  }
</script>