import Vue from 'vue'
import 'material-design-icons-iconfont/dist/material-design-icons.css'
import vuetify from "./plugins/vuetify";
import App from './App.vue'

new Vue({
    el: '#app',
    vuetify,
    render: (h) => h(App),
});
