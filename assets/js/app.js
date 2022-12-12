/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
//import './styles/app.css';

// start the Stimulus application
//import './bootstrap';

import $ from 'jquery';
window.jQuery = $;
window.$ = $;
import 'popper.js';
import 'bootstrap';
import 'jquery-validation'; 
import 'jquery-validation/dist/additional-methods';
import 'jquery-validation/dist/localization/messages_fr';
import toastr from 'toastr';

window.toastr = toastr;
window.toastr.options={closeButton:!0,debug:!1,progressBar:!1,positionClass:"toast-top-right",onclick:null,showDuration:"300",hideDuration:"1000",timeOut:"15000",extendedTimeOut:"1000",showEasing:"swing",hideEasing:"linear",showMethod:"fadeIn","progressBar": true,hideMethod:"fadeOut"};

$( () => {
	$("body").append("<div class='spinner'><div class='sk-folding-cube'><div class='sk-cube1 sk-cube'></div><div class='sk-cube2 sk-cube'></div><div class='sk-cube4 sk-cube'></div><div class='sk-cube3 sk-cube'></div></div></div>");
	$('.flashbagSuccess').each(function(i, obj) {
    	toastr.success($(this).val(), "Success");
	});
});    