import Vue        from 'vue'
import VueRouter  from 'vue-router'
import Tabs       from 'vue-tabs-component';
import VModal     from 'vue-js-modal';

Vue.use(VueRouter);
Vue.use(Tabs);
Vue.use(VModal, {dynamic: true, injectModalsContainer: true});

import App   from './components/App'
import Home  from './components/Home'
import Users from './components/Users'
import Cards from './components/cards/Cards'
import Cases from './components/Cases'
import Case  from './components/Case'

import store from './store/index'


const router = new VueRouter({
    mode  : 'history',
    routes: [
        {
            path     : '/users',
            name     : 'users',
            component: Users
        },
        {
            path     : '/',
            name     : 'home',
            component: Home
        },
        {
            path     : '/cases',
            name     : 'cases',
            component: Cases
        },
        {
            path     : '/cases/:caseId',
            name     : 'case',
            component: Case
        },
        {
            path     : '/cards',
            name     : 'cards',
            component: Cards,
        }
    ],
});

const app = new Vue({
    el        : '#app',
    components: {App},
    store     : store,
    router,
});
