require('./bootstrap');

window.Vue = require('vue').default;

import App from './App.vue';
import {routes} from './router/routes';

//Import v-from
import { Form, HasError, AlertError } from 'vform'
window.Form = Form;
Vue.component(HasError.name, HasError)
Vue.component(AlertError.name, AlertError)

/*vue progress var
https://github.com/hilongjw/vue-progressbar
*/
import VueProgressBar from 'vue-progressbar'
Vue.use(VueProgressBar, {
  color: 'rgb(143, 255, 199)',
  failedColor: 'red',
  height: '4px'
})
Vue.component('example-component', require('./components/ExampleComponent.vue').default);

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import iziToast  from 'izitoast';
window.iziToast = iziToast;

/*
vue pagination
https://github.com/gilbitron/laravel-vue-pagination
*/
Vue.component('pagination', require('laravel-vue-pagination'));

//Filter
import moment from 'moment';
Vue.filter('upText',function (txt) {
	return txt.charAt(0).toUpperCase()+txt.slice(1);
});
Vue.filter('formateDate',function (dt) {
	return moment(dt).format('MMMM Do YYYY');
});

/*
Sweet Alert
https://sweetalert2.github.io/
*/
import Swal from 'sweetalert2'
window.swal=Swal;
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true,
  onOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
})
window.toast=Toast;

/*range slider*/
import 'vue-range-component/dist/vue-range-slider.css'
import VueRangeSlider from 'vue-range-component'
Vue.use(VueRangeSlider);


const router = new VueRouter({
    mode: 'history',
    routes: routes
});

const app = new Vue({
    el: '#app',
    router: router,
    render: h => h(App),
});
