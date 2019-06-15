<template>
  <footer class="footer">
    <div class="footer__wrapper">
      <div class="footer__main">
        <div class="footer__main_bloc1">
          <p>{{title}}</p>
        </div>

        <div class="footer__main_bloc2">
        </div>
      </div>

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

  let title = document.getElementById('appHeader').dataset.title;
  let shareButtons = JSON.parse(document.getElementById('appFooter').dataset.share);

  export default {
    name: "Footer",
    data() {
      return {
        btnsShare: shareButtons,
        title: title
      }
    }
  }
</script>