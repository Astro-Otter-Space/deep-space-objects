<template>
  <footer class="footer">
    <div class="footer__wrapper">
      <div class="footer__main">
        <div class="footer__main_bloc1">
          <p>{{title}} </p>
        </div>

        <div class="footer__main_bloc2">
          <span>{{desc}}</span>
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
        <ul>
          <li v-for="linkFooter in linksFooter">
            <a v-bind:href="linkFooter.path" v-bind:title="linkFooter.label">{{linkFooter.label}}</a>
          </li>
        </ul>
      </div>
    </div>

  </footer>
</template>

<script>
  import './../Icons/index';

  let labels = JSON.parse(document.getElementById('appHeader').dataset.labels);
  let shareButtons = JSON.parse(document.getElementById('appFooter').dataset.share);
  let linksFooter = JSON.parse(document.getElementById('appFooter').dataset.links);

  export default {
    name: "Footer",
    data() {
      return {
        btnsShare: shareButtons,
        title: labels.title,
        desc: labels.description,
        year: new Date().getFullYear(),
        linksFooter: linksFooter
      }
    }
  }
</script>