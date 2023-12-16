import Vue from 'vue'
import Vuex from 'vuex'
import getters from './getters'
import app from './modules/app'
import settings from './modules/settings'
import user from './modules/user'

import appInfo from './myModules/appInfo'
import type from "./myModules/type";

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    app,
    settings,
    user,
    appInfo,
    type
  },
  getters
})

export default store
