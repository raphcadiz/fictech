import axios from 'axios'
import Cookies from 'js-cookie'

// state
export const state = {
  company_categories: [],
  category: ''
}

// getters
export const getters = {
  company_categories: state => state.company_categories,
  category: state => state.category
}

// mutations
export const mutations = {
  SET_COMPANY_CATEGORIES (state, data) {
    state.company_categories = data
  },
  SET_COMPANY_CATEGORY (state, data) {
    state.category = data
  }
}

// actions
export const actions = {
  async GET_COMPANY_CATEGORIES ({ commit, dispatch }, payload) {
    let params = ''
    if (payload.unfiltered) {
      params = '?unfiltered=1'
    }
    const { data } = await axios.get('/api/company-category' + params)
    commit('SET_COMPANY_CATEGORIES', data)
  },

  async GET_COMPANY_CATEGORY ({ commit, dispatch }, id) {
    const { data } = await axios.get('/api/company-category/' + id)
    commit('SET_COMPANY_CATEGORY', data)
  }
}