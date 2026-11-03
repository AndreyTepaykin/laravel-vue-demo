import { createStore } from "vuex";
import { auth } from "./auth.module.js";
import { profile } from "./profile.module.js";
import { users } from "./users.module.js";

export default createStore({
  modules: {
    auth,
    profile,
    users,
  },
  state: {
    hideConfigButton: false,
    isPinned: false,
    showConfig: false,
    sidebarType: "bg-white",
    isTransparent: "",
    isRTL: false,
    mcolor: "",
    darkMode: false,
    isNavFixed: false,
    isAbsolute: false,
    showNavs: true,
    showSidenav: true,
    showNavbar: true,
    showFooter: true,
    showMain: true,
    layout: "default",
    imageLayout: "default",
  },
  mutations: {
    toggleConfigurator(state) {
      state.showConfig = !state.showConfig;
    },
    navbarMinimize(state) {
      state.isPinned = !state.isPinned;
    },
    sidebarType(state, payload) {
      state.sidebarType = payload;
    },
    navbarFixed(state) {
      if (state.isNavFixed === false) {
        state.isNavFixed = true;
      } else {
        state.isNavFixed = false;
      }
    }
  },
  actions: {
    toggleSidebarColor({ commit }, payload) {
      commit("sidebarType", payload);
    }
  },
  getters: {}
});
