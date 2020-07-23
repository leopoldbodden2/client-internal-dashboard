
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.moment = require('moment');


require( 'jquery' );
require( 'datatables.net' );
require( 'datatables.net-bs4' );
require( 'datatables.net-responsive-bs4' );
require( 'chartjs-plugin-style' );
import $ from 'jquery';
import Popper from 'popper.js';
import DataTable from 'datatables.net-bs4';
import { loadProgressBar } from 'axios-progress-bar'
import fontawesomeall from '@fortawesome/fontawesome-pro/js/all';
import VueClipboard from 'vue-clipboard2'


window.Vue = require('vue');
Vue.use(VueClipboard)

// set some datatables defaults
$.extend(true, DataTable.defaults, {
    "dom": "<'row sticky-top'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>" +
        "<'row'<'col-sm-12'tr>>" +
        "<'row p-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-2'l><'col-sm-12 col-md-5'p>>",
    "deferRender": true,
    "autoWidth": false, // disable fixed width and enable fluid table
    "pageLength": 10, // default records per page
    "language": {
        "paginate": {
            "next": "<span class='fa fa-chevron-right'></span>",
            "previous": "<span class='fa fa-chevron-left'></span>"
        }
    }
});

loadProgressBar({ parent: '#progress-bar' }, window.axios);

// set global theme colors (this should match css theme colors)
window.ThemeColors = [
    '#57A0BE', // blue
    '#F7941D', // orange
    '#38c172', // success
    '#6cb2eb', // info
    '#6c757d', // gray
    '#ffed4a', // yellow
    '#343a40', // gray-dark
    '#F7941D', // theme-orange
    '#57A0BE', // theme-blue
    '#38c172', // green
    '#f66d9b', // pink
    '#f8f9fa', // light
    '#343a40', // dark
    '#6574cd', // indigo
    '#4dc0b5', // teal
    '#6cb2eb', // cyan
    '#e3342f', // red
    '#9561e2', // purple
    '#ffffff', // white
    '#ffed4a', // warning
    '#e3342f' // danger
];

import PassportClients from './components/passport/Clients';
import PassportAuthorizedClients from './components/passport/AuthorizedClients';
import PassportPersonalAccessTokens from './components/passport/PersonalAccessTokens';
import Visitors from './components/Visitors';
import Referrers from './components/Referrers';
import VisitorLocation from './components/VisitorLocation';
import CdyneMessages from './components/CdyneMessages';
import SocialCalendarCreate from './components/social-calendar/Create';
import SocialCalendarEdit from './components/social-calendar/Edit';
import SocialCalendarShow from './components/social-calendar/Show';


Chart.pluginService.register({
    beforeDraw: function (chart, easing) {
        if (chart.config.options.chartArea && chart.config.options.chartArea.backgroundColor) {
            var helpers = Chart.helpers;
            var ctx = chart.chart.ctx;
            var chartArea = chart.chartArea;

            ctx.save();
            ctx.fillStyle = chart.config.options.chartArea.backgroundColor;
            ctx.fillRect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
            ctx.restore();
        }
    }
});


const app = new Vue({
    el: '#app',
    components: {
        'passport-clients': PassportClients,
        'passport-authorized-clients': PassportAuthorizedClients,
        'passport-personal-access-tokens': PassportPersonalAccessTokens,
        'chart-visitors': Visitors,
        'chart-referrers': Referrers,
        'chart-visitor-location': VisitorLocation,
        'cdyne-messages': CdyneMessages,
        'social-calendar-create': SocialCalendarCreate,
        'social-calendar-edit': SocialCalendarEdit,
        'social-calendar-show': SocialCalendarShow
    }
});


