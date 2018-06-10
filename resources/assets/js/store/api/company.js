import axios from 'axios'
import Cookies from 'js-cookie'

export default {
  get (data) {
    return axios.get('/api/company', data);
  }
}