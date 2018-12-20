window.axios = require('axios');

window.axios.interceptors.response.use(
    function (response) {
        return response;
    },
    function (error) {
        if (error.response.status === 401)
            window.location.reload();

        return Promise.reject(error);
    });

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
let token = document.head.querySelector('meta[name="csrf-token"]');
if (token)
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
else
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');


import React                            from 'react';
import {render}                         from 'react-dom';
import {BrowserRouter as Router, Route} from "react-router-dom";
import {Provider}                       from 'mobx-react';
import DevTools                         from 'mobx-react-devtools';

import {Banks, Users} from './components/index';
import * as stores                             from './stores';

if (document.getElementById('banks')) {
    render(
        <Provider banksStore={stores.banksStore}>
            <Router>
                <div>
                    <Banks path="/banks"/>
                </div>
            </Router>
        </Provider>,
        document.getElementById('banks')
    );
}
if (document.getElementById('users')) {
    render(
        <Provider usersStore={stores.usersStore}>
            <Router>
                <Users path="/users"/>
            </Router>
        </Provider>,
        document.getElementById('users')
    );
}
