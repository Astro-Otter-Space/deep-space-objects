<template>
  <footer class="footer">
    <div class="footer__wrapper">
      <div class="footer__main footer__share">
        <div class="footer__main_bloc1">
          <p>{{title}} </p>
        </div>

        <div class="footer__main_bloc2">
          <span>{{desc}}</span>
        </div>
      </div>

      <div class="footer__share">
        <ul>
          <li v-for="linkFooter in linksFooter">
            <a v-bind:href="linkFooter.path" v-bind:title="linkFooter.label">{{linkFooter.label}}</a>
          </li>
        </ul>
      </div>

      <div class="footer__share">
        <ul>
          <li v-for="btnShare in btnsShare">
            <a v-bind:href="btnShare.path" v-bind:title="btnShare.label" target="_blank">
              <svgicon v-bind:name="btnShare.icon_class" width="30" height="30" ></svgicon>
            </a>
          </li>
        </ul>
      </div>

      <hr/>

      <div class="footer__secondLine">
        <span>HamHamFonFon</span>
        <span>{{year}}</span>
        <span>{{allRights}}</span>
      </div>
    </div>
  </footer>
</template>

<script>
  import './../Icons/index';

  let labels = JSON.parse(document.getElementById('appFooter').dataset.labels);
  let shareButtons = JSON.parse(document.getElementById('appFooter').dataset.share);
  let linksFooter = JSON.parse(document.getElementById('appFooter').dataset.links);

  export default {
    name: "Footer",
    data() {
      return {
        btnsShare: shareButtons,
        title: labels.title,
        desc: labels.desc,
        year: new Date().getFullYear(),
        allRights: labels.allRights,
        linksFooter: linksFooter
      }
    }
  }
</script>
