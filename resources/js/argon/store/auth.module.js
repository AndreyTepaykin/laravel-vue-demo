import AuthService from '../services/auth.service.js';

const isAuthenticated = localStorage.getItem('is_authenticated') === '1';

export const auth = {
  namespaced: true,
  state: { isAuthenticated },
  getters: {
    isAuthenticated: (state) => state.isAuthenticated,
  },
  actions: {
    async login({ commit }, user) {
      await AuthService.login(user);
      commit('isAuthenticated', true);
    },
    async logout({ commit }) {
      try {
        await AuthService.logout();
      } finally {
        commit('isAuthenticated', false);
      }
    },
    async register({ commit }, user) {
      await AuthService.register(user);
      commit('isAuthenticated', true);
    },
    async forgotPassword({ commit }, data) {
      await AuthService.forgotPassword(data);
    },
    async resetPassword({ commit }, data) {
      await AuthService.resetPassword(data);
    },
  },
  mutations: {
    isAuthenticated(state, payload) {
      state.isAuthenticated = payload;
    },
  },
};
