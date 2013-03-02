// Ajax Comment Posting
// WordPress plugin
// version 2.0

function ajaxizeComments() {

    
    // initialise
    var form, err, reply;
    function acp_initialise() {
        jQuery('#commentform').after('<div id="error"></div>');
        jQuery('#submit').after('<span id="loading" >'+messages.loading+'</span>');
        jQuery('#loading').hide();
        form = jQuery('#commentform');
        err = jQuery('#error');
        reply = false;
    }
    acp_initialise();

    jQuery('.comment-reply-link').live('click', function() {
        // checks if it's a reply to a comment
        reply = jQuery(this).parents('.depth-1').attr('id');
        err.empty();
    });

    jQuery('#cancel-comment-reply-link').live('click', function() {
        reply = false;
    });	

    jQuery('#commentform').live('submit', function(evt) {

        err.empty();

        if(form.find('#author')[0]) {
            // if not logged in, validate name and email
            if(form.find('#author').val() == '') {
                err.html('<span class="error">'+messages.empty_name+'</span>');
                return false;
            }
            if(form.find('#email').val() == '') {
                err.html('<span class="error">'+messages.empty_email+'</span>');
                return false;
            }
            var filter  = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(!filter.test(form.find('#email').val())) {
                err.html('<span class="error">'+messages.invalid_email+'</span>');
                if (evt.preventDefault) {
                    evt.preventDefault();
                }
                return false;
            }
        } // end if

        if(form.find('#comment').val() == '') {
            err.html('<span class="error">'+messages.empty_comment+'</span>');
            return false;
        }

        jQuery(this).ajaxSubmit({
        
            beforeSubmit: function() {
                jQuery('#loading').show();
                jQuery('#submit').attr('disabled','disabled');
            }, // end beforeSubmit
        
            error: function(request){
                err.empty();
                var data = request.responseText.match(/<p>(.*)<\/p>/);
                err.html('<span class="error">'+ data[1] +'</span>');
                jQuery('#loading').hide();
                jQuery('#submit').removeAttr("disabled");
                return false;
            }, // end error()
        
            success: function(data) {
                
                try {
                    // TODO: Substituir por ajax que retorne somente o comentário enviado
                    
                    /*  
                      
                    // if the comments is a reply, replace the parent comment's div with it
                    // if not, append the new comment at the bottom
                    var response = jQuery("<ol>").html(data);
                    
                    if(reply != false) {
                        jQuery('#'+reply).replaceWith(response.find('#'+reply));
                        jQuery('.commentlist').after(response.find('#respond'));
                        acp_initialise();
                    } else {
                        
                        if (jQuery(document).find('.commentlist')[0]) {
                            response.find('.commentlist li:last').hide().appendTo(jQuery('.commentlist')).slideDown('slow');
                        } else {
                            jQuery('#respond').before(response.find('.commentlist'));
                        }
                        if (jQuery(document).find('#comments')[0]) {
                            jQuery('#comments').html(response.find('#comments'));
                        } else {
                            jQuery('.commentlist').before(response.find('#comments'));
                        }
                    }
                    form.find('#comment').val('');
                    err.html('<span class="success">'+messages.comment_success+'</span>');
                    jQuery('#submit').removeAttr("disabled");
                    jQuery('#loading').hide();
                    */
                    var pid = jQuery('#comments').parents('.entry').attr('id');
                    var post_id = jQuery('#comments').parents('.entry').attr('id').replace(/[^0-9]+/g, '');
                    mapasdevista.linkToPostById(post_id);
            
                } catch (e) {
                    jQuery('#loading').hide();
                    jQuery('#submit').removeAttr("disabled");
                    alert(messages.error+'\n\n'+e);
                } // end try
                       
            } // end success()
        
        }); // end ajaxSubmit()
    
        return false; 
    
    }); // end form.submit()

};
                                    
