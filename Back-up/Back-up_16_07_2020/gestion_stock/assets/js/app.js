/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
const $ = require('jquery');
global.$ = global.jQuery = $;
const { TextareaEditor } = require("@textcomplete/textarea");

require('bootstrap');
require('bootstrap-star-rating');
require('bootstrap-star-rating/css/star-rating.css');
require('bootstrap-star-rating/themes/krajee-svg/theme.css');

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});
