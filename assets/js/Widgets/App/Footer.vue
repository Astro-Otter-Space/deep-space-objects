<template>
  <footer v-bind:class="[ !this.isHome  ? 'footer': 'footer__isHome', 'footer']">
    <div class="footer__wrapper">
      <div class="footer__share footer__main">
        <p v-if="!this.isHome" class="footer__main_bloc1">{{title}} </p>
      </div>

      <div class="footer__share">
        <ul>
          <li v-for="linkFooter in linksFooter">
            <a v-bind:href="linkFooter.path" v-bind:title="linkFooter.label">{{linkFooter.label}}</a>
          </li>
        </ul>
      </div>

      <div class="footer__share">
        <p v-if="!this.isHome" class="footer__main_bloc2">{{desc}}</p>
      </div>
    </div>
    <div class="footer__social">
      <div class="footer__separator"></div>
      <div class="footer__share footer__share_buttons">
        <buttons-links
          :links="btnsShare"
          :color="colorBtnShare"
          width-button="40"
          height-button="40"
        ></buttons-links>
      </div>
      <div class="footer__separator"></div>
    </div>
  </footer>
</template>

<script>
  import './../Icons/index';
  import buttonsLinks from "./buttonsLinks";

  let labels = JSON.parse(document.getElementById('appFooter').dataset.labels);
  let shareButtons = JSON.parse(document.getElementById('appFooter').dataset.share);
  let linksFooter = JSON.parse(document.getElementById('appFooter').dataset.links);
  let routeSf = document.getElementById('appFooter').dataset.currentRoute;

  export default {
    name: "Footer",
    components: {
      buttonsLinks
    },
    data() {
      return {
        currentRoute: routeSf,
        isHome: true,
        btnsShare: shareButtons,
        colorBtnShare: "#e9e9e9",
        title: labels.title,
        desc: labels.desc,
        year: new Date().getFullYear(),
        allRights: labels.allRights,
        linksFooter: linksFooter
      }
    },
    methods: {
      isHomepage: function() {
        if(this.currentRoute === "homepage") {
          this.isHome = true;
        } else {
          this.isHome = false
        }
      }
    },
    beforeMount() {
      this.isHomepage()
    }
  }
</script>
