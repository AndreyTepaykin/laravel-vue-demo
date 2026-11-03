import usersService from '../services/users.service.js';

export const users = {
  namespaced: true,
  state: {
    items: [],
  },
  getters: {
    items: (state) => state.items,
  },
  actions: {
    async fetchAll({ commit }) {
      const items = await usersService.list();
      commit('SET_ITEMS', items);
    },
    async create({ dispatch }, user) {
      await usersService.create(user);
      await dispatch('fetchAll');
    },
    async update({ dispatch }, { id, user }) {
      await usersService.update(id, user);
      await dispatch('fetchAll');
    },
    async remove({ dispatch }, id) {
      await usersService.destroy(id);
      await dispatch('fetchAll');
    },
  },
  mutations: {
    SET_ITEMS(state, items) {
      state.items = items;
    },
  },
};
