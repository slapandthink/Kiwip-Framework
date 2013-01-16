jQuery.noConflict();
jQuery(function($){

    var error_msg = $("#message p[class='setting-error-message']");  
    // look for admin messages with the "setting-error-message" error class  
    if (error_msg.length != 0) {  
        // get the title  
        var error_setting = error_msg.attr('title');  
  
        // look for the label with the "for" attribute=setting title and give it an "error" class (style this in the css file!)  
        $("label[for='" + error_setting + "']").addClass('error');  
  
        // look for the input with id=setting title and add a red border to it.  
        $("input[id='" + error_setting + "']").attr('style', 'border-color: red');  
    }  


    /* Kixip Welcome Panel */
    $('a.kiwip-welcome-panel-toggle').on('click', function(){
        $('div.kiwip-welcome-column-panel-container').slideToggle(500);
    });

    /* TABS */
    var title_active = $('.nav-tab-wrapper a.nav-tab-active span').html();

    $('.kiwip-options-group-tab').each(function(){
        var test_title = $(this).find("h3").first().html();
        
        if(test_title != title_active){
            $(this).hide();
        }
    });


}); 