import Athan from "./components/Athan";
import Vue from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import {BootstrapVue, BootstrapVueIcons} from 'bootstrap-vue'

Vue.use(BootstrapVue)
Vue.use(BootstrapVueIcons)
const moment = require('moment')
require('moment/locale/fr')


new Vue({
    name: 'app',
    el: '#app',
    components: {
        Athan
    }
});
Vue.use(require('vue-moment'), {
    moment
})

Vue.use(VueAxios, axios)