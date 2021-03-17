window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

//Axios
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

//Jquery
window.$ = window.jQuery = require('jquery');
require('jquery-ui');
require('jquery-ui-bundle');

//Moment
window.moment = require('moment');
window.momentTimezone = require('moment-timezone');

//Utilities
require('@fortawesome/fontawesome-free/js/all.js');
require('bootstrap');
require('select2');
require('timepicker');
require('@chenfengyuan/datepicker');
require('croppie');
window.intlTelInput = require('intl-tel-input');
window.cookies = require('js-cookie');
window.linkify = require('linkifyjs');
window.linkifyHtml = require('linkifyjs/html');
if(user != null){
    window.clipboardJS = require("clipboard");
}

//Moment
window.moment = require('moment');
window.momentTimezone = require('moment-timezone');

//OT
import OT from "@opentok/client";

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

import Echo from 'laravel-echo';
window.Pusher = require('pusher-js');
window.Echo = new Echo({
    broadcaster: 'pusher',
	key: process.env.MIX_PUSHER_APP_KEY,
	cluster: 'eu',
	forceTLS: true
});
