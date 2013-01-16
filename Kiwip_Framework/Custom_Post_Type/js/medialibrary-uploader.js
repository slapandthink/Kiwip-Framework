/*-----------------------------------------------------------------------------------*/
/* WooFramework Media Library-driven AJAX File Uploader Module
/* JavaScript Functions (2010-11-05)
/*
/* The code below is designed to work as a part of the WooFramework Media Library-driven
/* AJAX File Uploader Module. It is included only on screens where this module is used.
/*
/* Used with modifications for Kiwp Framework.
/*-----------------------------------------------------------------------------------*/

(function ($) {

  kiwip_frameworkMLU = {
  
/*-----------------------------------------------------------------------------------*/
/* Remove file when the "remove" button is clicked.
/*-----------------------------------------------------------------------------------*/
  
    removeFile: function () {
     
     $('.mlu_remove').live('click', function(event) { 
        $(this).hide();
        $(this).parents('.screenshot').siblings('.upload').attr('value', '');
        $(this).parents('.screenshot').slideUp();
        $(this).parents('.screenshot').siblings('.of-background-properties').hide(); //remove background properties
        return false;
      });
      
    }, // End removeFile

/*-----------------------------------------------------------------------------------*/
/* Use a custom function when working with the Media Uploads popup.
/* Requires jQuery, Media Upload and Thickbox JavaScripts.
/*-----------------------------------------------------------------------------------*/

	mediaUpload: function () {
	
	jQuery.noConflict();
	
	$( 'input.upload_button' ).removeAttr('style');
	
	var formfield,
		formID,
		btnContent = true,
		tbframe_interval;

    // hide galery settings
    $('#gallery-settings').hide();


		// On Click
		$('input.upload_button').live("click", function () {       

        formfield = $(this).prev('input.upload.file').attr('id');
        formID = $(this).attr('rel');
		

		//Change "insert into post" to "Use this Button"
		tbframe_interval = setInterval(function() {

      // add button use this image on gellery
      //<input type="submit" value="Insérer dans l’article" class="button" id="send[8]" name="send[8]">
      var testinput_gellery = jQuery('#TB_iframeContent').contents().find('#gallery-form .media-item');
      testinput_gellery.each(function (){
        // grab the id
        var imgid_gallery = $(this).attr('id').split("-");

        if($(this).find('.savesend input.button').val('Use This Image').length == '0'){
          $(this).find('.savesend').prepend('<input type="submit" value="Use This Image" class="button" id="send['+imgid_gallery[2]+']" name="send['+imgid_gallery[2]+']">');
        }
      });

      // add button use this image on library
      var testinput_library = jQuery('#TB_iframeContent').contents().find('#library-form .media-item');
      testinput_library.each(function (){
        // grab the id
        var imgid_library = $(this).attr('id').split("-");

        if($(this).find('.savesend input.button').val('Use This Image').length == '0'){
          $(this).find('.savesend').prepend('<input type="submit" value="Use This Image" class="button" id="send['+imgid_library[2]+']" name="send['+imgid_library[2]+']">');
        }
      });

      jQuery('#TB_iframeContent').contents().find('.savesend .button').val('Use This Image');
    }, 1500);

    // Display a custom title for each Thickbox popup.
    var title = '';
        
		if ( $(this).parents('tr').find('label') ) { title = $(this).parents('tr').find('label').text(); } // End IF Statement
        
		//tb_show( title, 'media-upload.php?post_id='+formID+'&TB_iframe=1' ); //error?
    tb_show( title, 'media-upload.php?TB_iframe=1&amp;post_id='+formID );
		return false;
	});
            
	window.original_send_to_editor = window.send_to_editor;
	window.send_to_editor = function(html) {
		if (formfield) {
			
			//clear interval for "Use this Button" so button text resets
			clearInterval(tbframe_interval);
        	
			// itemurl = $(html).attr('href'); // Use the URL to the main image.
          
          if ( $(html).html(html).find('img').length > 0 ) {
          
          	itemurl = $(html).html(html).find('img').attr('src'); // Use the URL to the size selected.
          	
          } else {
          
            // It's not an image. Get the URL to the file instead.
            	
  		      var htmlBits = html.split("'"); // jQuery seems to strip out XHTML when assigning the string to an object. Use alternate method.
            itemurl = htmlBits[1]; // Use the URL to the file.
          	
          	var itemtitle = htmlBits[2];
          	
          	itemtitle = itemtitle.replace( '>', '' );
          	itemtitle = itemtitle.replace( '</a>', '' );
          
          } // End IF Statement
                   
          var image = /(^.*\.jpg|jpeg|png|gif|ico*)/gi;
          var document = /(^.*\.pdf|doc|docx|ppt|pptx|odt*)/gi;
          var audio = /(^.*\.mp3|m4a|ogg|wav*)/gi;
          var video = /(^.*\.mp4|m4v|mov|wmv|avi|mpg|ogv|3gp|3g2*)/gi;
          
          if (itemurl.match(image)) {
            btnContent = '<img src="'+itemurl+'" alt="" /><a href="#" class="mlu_remove button">Remove Image</a>';
          } else {
          	
          	// No output preview if it's not an image.
            // btnContent = '';
            // Standard generic output if it's not an image.
            
            html = '<a href="'+itemurl+'" target="_blank" rel="external">View File</a>';
            btnContent = '<div class="no_image"><span class="file_link">'+html+'</span><a href="#" class="mlu_remove button">Remove</a></div>';
          }
          
          $('#' + formfield).val(itemurl);
          // $('#' + formfield).next().next('div').slideDown().html(btnContent);
          //$('#' + formfield).siblings('.screenshot').slideDown().html(btnContent);
          $('#' + formfield).siblings('.screenshot').slideDown().html(btnContent);
		      //$('#' + formfield).siblings('.of-background-properties').show(); //show background properties
          $('#' + formfield).siblings('.of-background-properties').show();
          tb_remove();
          
        } else {
          window.original_send_to_editor(html);
        }
        
        // Clear the formfield value so the other media library popups can work as they are meant to. - 2010-11-11.
        formfield = '';
      }
      
    } // End mediaUpload
   
  }; // End kiwip_frameworkMLU Object // Don't remove this, or the sky will fall on your head.

/*-----------------------------------------------------------------------------------*/
/* Execute the above methods in the kiwip_frameworkMLU object.
/*-----------------------------------------------------------------------------------*/
  
	$(document).ready(function () {

		kiwip_frameworkMLU.removeFile();
		kiwip_frameworkMLU.mediaUpload();
	
	});
  
})(jQuery);