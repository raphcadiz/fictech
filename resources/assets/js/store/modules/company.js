import axios from 'axios'
import Cookies from 'js-cookie'

// state
export const state = {
  companies: [],
  company: '',
  featured_companies: []
}

// getters
export const getters = {
  companies: state => state.companies,
  company: state => state.company,
  featured_companies: state => state.featured_companies
}

// mutations
export const mutations = {
  SET_COMPANIES (state, data) {
    state.companies = data
  },
  SET_COMPANY (state, data) {
    state.company = data
  },
  SET_FEATURED_COMPANIES (state, data) {
    state.featured_companies = data
  },
}

// actions
export const actions = {
  async GET_COMPANIES ({ commit, dispatch }, payload) {
    const { data } = await axios.get('/api/company', payload)
    commit('SET_COMPANIES', data)
  },

  async GET_COMPANY ({ commit, dispatch }, id) {
    const { data } = await axios.get('/api/company/' + id)
    commit('SET_COMPANY', data)
  },

  async GET_FEATURED_COMPANIES ({ commit, dispatch }, payload) {
    let params = ''
    if (payload && payload.cert_type) {
      params = '&cert_type=' + payload.cert_type
    }
    const { data } = await axios.get('/api/company?featured=true' + params)
    commit('SET_FEATURED_COMPANIES', data)
  }
}