/*--------------------------------------------------------------
 Copyright (C) pixelemu.com
 License: http://www.pixelemu.com/license.html PixelEmu Proprietary Use License
 Website: http://www.pixelemu.com
 Support: info@pixelemu.com
 ---------------------------------------------------------------*/

(function ($) {

	"use strict";

	   function postTitleCheckValues(){
		    $( '#tc_seo input.post-titles' ).each( function( index, element ){
			    if(!$(this).val()) {
			    	$(this).addClass("error");
			    } else {
			    	$(this).removeClass("error");
			    }
		    });
	   };
		   
	/* -------------------------------------------------------- */
	/* READY state                                              */
	/* -------------------------------------------------------- */

	$(document).ready(function(){
		
		// GDPR
	    if( $('#_tc_gdpr_method').val() == '1') {
	        $('.cmb2-id--tc-gdpr-plugin').show();
	    }
	    if( $('#_tc_gdpr_method').val() == '0') {
            $('.cmb2-id--tc-terms-and-conditions').show();
            $('.cmb2-id--tc-terms-and-conditions-page').show();
            $('.cmb2-id--tc-privacy-policy').show();
            $('.cmb2-id--tc-privacy-policy-page').show();
            $('.cmb2-id--tc-gdpr').show();
            $('.cmb2-id--tc-gdpr-information').show();
	    }
	    $('#_tc_gdpr_method').bind('change', function (e) {
	        if( $('#_tc_gdpr_method').val() == '1') {
	            $('.cmb2-id--tc-gdpr-plugin').show();
	        } else{
	           $('.cmb2-id--tc-gdpr-plugin').hide();
	        }        
	        
	        if( $('#_tc_gdpr_method').val() == '0') {
	            $('.cmb2-id--tc-terms-and-conditions').show();
	            $('.cmb2-id--tc-terms-and-conditions-page').show();
	            $('.cmb2-id--tc-privacy-policy').show();
	            $('.cmb2-id--tc-privacy-policy-page').show();
	            $('.cmb2-id--tc-gdpr').show();
	            $('.cmb2-id--tc-gdpr-information').show();
	        }
	        else{
	            $('.cmb2-id--tc-terms-and-conditions').hide();
	            $('.cmb2-id--tc-terms-and-conditions-page').hide();
	            $('.cmb2-id--tc-privacy-policy').hide();
	            $('.cmb2-id--tc-privacy-policy-page').hide();
	            $('.cmb2-id--tc-gdpr').hide();
	            $('.cmb2-id--tc-gdpr-information').hide();
	        } 
	    });
	    
	    // SEO - show/hide additional field
	    if( $('#_tc_seo_expired_ad').val() == '1') {
	        $('.cmb2-id--tc-seo-expired-ad-redirect-url').show();
	    }
	    $('#_tc_seo_expired_ad').bind('change', function (e) {
	        if( $('#_tc_seo_expired_ad').val() == '1') {
	            $('.cmb2-id--tc-seo-expired-ad-redirect-url').show();
	        } else {
	           $('.cmb2-id--tc-seo-expired-ad-redirect-url').hide();
	        }
	    });
	    
	    // SEO and Security - mark empty page select fields
	    postTitleCheckValues();
	    $( "input#find-posts-submit" ).click(function() {
	    	window.setTimeout( postTitleCheckValues, 300 );
    	});
	    $( "#tc_seo .post-titles-container .clear-field" ).click(function() {
	    	$(this).siblings(".post-titles").addClass("error");
    	});
	    // set page fields in SEO tab required
	    $( "#tc_seo input.post-titles" ).attr("required", "required");
	    
	    // sell type - show/hide additional field
	    if( $('#_tc_use_selling_types2').is(':checked')) {
	        $('.cmb2-id--tc-selling-types').show();
	    }
        $(".cmb2-id--tc-use-selling-types input").click(function(){
	        if($(this).attr('id') == '_tc_use_selling_types2') {
	            $('.cmb2-id--tc-selling-types').show();
	        } else {
	           $('.cmb2-id--tc-selling-types').hide();
	        }
        });
        
	    // locations - show/hide additional fields
	    if( $('#_tc_use_locations2').is(':checked')) {
	        $('.cmb-row.location-fields').show();
	    }
        $(".cmb2-id--tc-use-locations input").click(function(){
	        if($(this).attr('id') == '_tc_use_locations2') {
	            $('.cmb-row.location-fields').show();
	        } else {
	           $('.cmb-row.location-fields').hide();
	        }
        });
	    
	    // only for advert edit view
		if($("body").hasClass("post-type-classified")){
			// locations show
			$( ".cmb2-id--tc-locations > .cmb-td" ).prepend( "<span class='tcf-clear-locations'><div class='dashicons dashicons-no'></div> <strong>Clear all locations</strong></span>" );
			$( ".tcf-clear-locations" ).click(function() {
				$( ".cmb2-id--tc-locations > .cmb-td li" ).attr('style','');
				$( ".cmb2-id--tc-locations > .cmb-td .cmb2-indented-hierarchy" ).attr('style','');
				$( ".cmb2-id--tc-locations > .cmb-td li" ).removeClass("clicked");
				$(".cmb2-id--tc-locations input").attr('checked', false);
			});
			$( ".cmb2-id--tc-locations > .cmb-td ul li" ).click(function() {
				$( this ).children("input").attr('checked', true);
				if($( this ).next(".cmb2-indented-hierarchy").length > 0) {
					if($( this ).hasClass("clicked")){
						$( this ).siblings().hide(300);
						$( this ).siblings("li").show(300);
						$( this ).removeClass("clicked");
					} else {
					  $( this ).addClass("clicked");
					  $( this ).siblings().hide(300);
					  $( this ).next(".cmb2-indented-hierarchy").show(300);
					}
				}
				$('.cmb2-id--tc-locations .cmb-th').removeClass("error");
			});
			
			// locations tree expanded if advert has already location set
			if($( ".cmb2-id--tc-locations ul.cmb2-list li input[type=radio]:checked" ).length > 0){
				$('.cmb2-id--tc-locations ul.cmb2-list li input[type=radio]:checked').parents("ul").show();
				$('.cmb2-id--tc-locations ul.cmb2-list li input[type=radio]:checked').parents("ul").siblings().hide();
				$('.cmb2-id--tc-locations ul.cmb2-list li input[type=radio]:checked').parents("ul").prev().show().addClass("clicked");
			}
		}
		
		// 'Show category on the archive page' select/unselect
		$( ".cmb2-id--tc-show-cat ul li:first-child" ).children().click(function() {
		  $( ".cmb2-id--tc-show-cat ul li input" ).prop( "checked", false );
		  $( ".cmb2-id--tc-show-cat ul li:first-child input" ).prop( "checked", true );
		});
		$( ".cmb2-id--tc-show-cat ul li + li" ).children().click(function() {
		  $( ".cmb2-id--tc-show-cat ul li:first-child input" ).prop( "checked", false );
		});

	});
	/* -------------------------------------------------------- */
	/* end READY state                                          */
	/* -------------------------------------------------------- */
})(jQuery);
