<!--
=========================================================
* Vue Argon Dashboard 2 - v3.0.0
=========================================================

* Product Page: https://creative-tim.com/product/vue-argon-dashboard-laravel
* Copyright 2022 Creative Tim (https://www.creative-tim.com)

Coded by www.creative-tim.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<template>
  <div
    class="g-sidenav-show"
    :class="{ 'g-sidenav-pinned': isPinned }"
  >
    <div
      v-show="layout === 'landing'"
      class="landing-bg h-100 bg-gradient-primary position-fixed w-100"
    ></div>

    <template v-if="showSidenav">
      <sidenav
        :custom_class="mcolor"
        :class="[isTransparent, isRTL ? 'fixed-end' : 'fixed-start']"
      />
      <main
        class="main-content position-relative max-height-vh-100 h-100 border-radius-lg"
      >
        <navbar
          :class="[navClasses]"
          :textWhite="isAbsolute ? 'text-white opacity-8' : 'text-white'"
          :minNav="navbarMinimize"
          v-if="showNavbar"
        />
        <router-view />
        <app-footer v-show="showFooter" />
        <configurator
          :class="[showConfig ? 'show' : '']"
        />
      </main>
    </template>

    <template v-else>
      <router-view />
      <configurator
        :class="[showConfig ? 'show' : '']"
      />
    </template>
  </div>
</template>
<script>
import Sidenav from "./examples/Sidenav/index.vue";
import Configurator from "@/examples/Configurator.vue";
import Navbar from "@/examples/Navbars/Navbar.vue";
import AppFooter from "@/examples/Footer.vue";
import { mapMutations, mapState } from "vuex";

export default {
  name: "App",
  components: {
    Sidenav,
    Configurator,
    Navbar,
    AppFooter,
  },
  computed: {
    ...mapState([
      "isPinned",
      "showSidenav",
      "showNavbar",
      "showFooter",
      "showConfig",
      "layout",
      "mcolor",
      "isTransparent",
      "isRTL",
      "isAbsolute",
      "isNavFixed",
      "darkMode",
    ]),
    navClasses() {
      return {
        "position-sticky bg-white left-auto top-2 z-index-sticky":
          this.isNavFixed && !this.darkMode,
        "position-sticky bg-default left-auto top-2 z-index-sticky":
          this.isNavFixed && this.darkMode,
        "position-absolute px-4 mx-0 w-100 z-index-2": this.isAbsolute,
        "px-0 mx-4": !this.isAbsolute,
      };
    },
  },
  watch: {
    isPinned: {
      immediate: true,
      handler(isPinned) {
        document.body.style.overflow =
          isPinned && this.isMobileViewport() ? "hidden" : "";
      },
    },
    $route() {
      this.closeMobileSidenav();
    },
  },
  methods: {
    ...mapMutations(["navbarMinimize"]),
    isMobileViewport() {
      return window.matchMedia("(max-width: 1199.98px)").matches;
    },
    closeMobileSidenav() {
      if (this.isMobileViewport()) {
        this.$store.state.isPinned = false;
      }
    },
  },
  beforeMount() {
    this.$store.state.isTransparent = "";
  },
  mounted() {
    this.closeMobileSidenav();
  },
  beforeUnmount() {
    document.body.style.overflow = "";
  },
};
</script>

<style>
@media (max-width: 1199.98px) {
  .g-sidenav-show:not(.g-sidenav-pinned) #sidenav-main {
    transform: translateX(-110%) !important;
    pointer-events: none;
  }
}
</style>
