(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	// Simple Accordion -- https://codepen.io/gecugamo/pen/xGLyXe

	$.fn.simpleAccordion = function() {

		this.on("click", ".accordion__control", function() {

				// Toggle the panel next to the item that was clicked
				$(this).toggleClass("accordion__control--active").next().slideToggle(250);

		});
	
		// Return jQuery object for method chaining
		return this;
	}

	// Set viewer for all results
	
	function setViewer( viewer ) {

		if ( viewer == 'var_export' ) {
			$('.item[data-tab="third"]').each( function() {
				$( this ).click();
			});
		} else if ( viewer == 'var_dump' ) {
			$('.item[data-tab="second"]').each( function() {
				$( this ).click();
			});
		} else if ( viewer == 'print_r' ) {
			$('.item[data-tab="first"]').each( function() {
				$( this ).click();
			});
		}

	}

	$(document).ready( function() {

		// Make page header sticky on scroll. Using https://github.com/AndrewHenderson/jSticky
		
		$('#vi-header').sticky({
			topSpacing: 0, // Space between element and top of the viewport (in pixels)
			zIndex: 100, // z-index
			stopper: '', // Id, class, or number value
			stickyClass: 'vi-sticky' // Class applied to element when it's stuck. Class name or false.
		})

		// Expand or collapse all individual results

		$('.toggle-results').click( function() {
			var text = $( this ).html();
			if ( text == 'Expand all' ) {
				$( this ).html( 'Collapse all' );
				$('.accordion__control').each( function() {
					if ( ! $( this ).hasClass( 'accordion__control--active' ) ) {
						$( this ).addClass( 'accordion__control--active' ).next().slideToggle(250);
					}
				});
			} else if ( text == 'Collapse all' ) {
				$( this ).html( 'Expand all' );
				$('.accordion__control').each( function() {
					if ( $( this ).hasClass( 'accordion__control--active' ) ) {
						$( this ).removeClass( 'accordion__control--active' ).next().slideToggle(250);
					}
				});
			} else {}
		});

		// Set the viewer from WP options via get_option -> wp_localize_script

		var viewer = viVars.viewer;
		// alert( 'Current viewer is ' + viewer );
		$('#results_viewer').val(viewer).change();

		setViewer( viewer );

		// Select the (default) viewer
		
		$('#results_viewer').change( function() {

			var viewer = $( this ).find("option:selected").attr('value');
			// alert( $( this ).find("option:selected").attr('value') );

			setViewer( viewer );

			$.ajax({
				url: ajaxurl,
				data: {
					'action': 'vi_set_viewer',
					'viewer': viewer
				},
				success:function(data) {
					// var data = data.slice(0,-1); // remove strange trailing zero in string returned by AJAX call
					// const response = JSON.parse(data); // create an object
					// alert( 'Viewer has been set to ' + response.viewer );
				},
				error:function(errorThrown) {
					console.log(errorThrown);
				}
			});

		});

        // Clear inspection results

		$('.clear-results').click( function( eventObject ) {

			eventObject.preventDefault();

			var button = $( this );

			$.ajax({
				url: ajaxurl,
				data: {
					'action':'vi_clear_results'
				},
				success:function(response) {

					response = JSON.parse(response)

					if ( response.success == true ) {

						$( '#inspection-results' ).empty();
						$( '#inspection-results' ).prepend('<div class="no-results">There is no data in the inspection log.</div>');

					}

				},
				error:function(errorThrown) {
					console.log(errorThrown);
				}
			});

		});

		$("#auto_load").change(function() {
		    if(this.checked) {
		        autoReload = setInterval( function(){ AjaxAutoLoad("#inspection-results"); }, 2500);
		    } else {
				clearInterval(autoReload);
			}
		});

		// simpleAccordion init

		$(".accordion").simpleAccordion();

		// Fomantic UI accordion init
		
		$(".ui.accordion").accordion();

	});

})( jQuery );

// Manually reload results

function AjaxManual(selector){(function($){
	$(selector).css({"opacity":"0.2","pointer-events":"none","cursor":"wait"});
	AjaxAutoLoad(selector);
})(jQuery);}

// Auto reload results

var autoReload = null;
var count = 0;
function AjaxAutoLoad(selector){(function($){
	$.ajax({
        type: "GET",
        url: window.location.href
    }).done(function(res){
    	$(selector).html( $(res).find(selector) );
		
		$(".accordion__control").click(function() {
			$("#auto_load").prop( "checked", false );
			clearInterval(autoReload);
			openClose(this);
		});
		
		$(selector).removeAttr("style");
    });
	// console.log(count++);
})(jQuery);}

// Toggle inspection result accordion

function openClose(selector){(function($){
	$(selector).toggleClass("accordion__control--active");
	$(selector).parents(".accordion").find(".accordion__panel").slideToggle();
})(jQuery);}