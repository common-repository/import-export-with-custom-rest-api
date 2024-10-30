jQuery( document ).ready(function($) {
    $('.wotrapi_toggle:checked').parent().addClass('default_active');
    $('.wotrapi_toggle:checked').siblings('.toggle-box-switch-circle').addClass('default_active');

    $(".wotrapi_btn_edit_endpoint").click(function(){
        $('.wotrapi_edit_endpoints').addClass('d-none');
        $('.wotrapi_save_endpoints').removeClass('d-none');
        $('.wotrapi_blogdata_submit').prop('disabled', true);
    });

    $(".toggle-box-switch-board").click(function(){
        if($(this).find('.wotrapi_toggle').hasClass('minimum_input')){
            return false;
        }
        $(this).find(".toggle-box-switch-circle").toggleClass("active-toggle");
        $(this).toggleClass("active-toggle"); 
        $(this).find('input').prop('checked', !$(this).find('input').prop('checked'));
    });

    $(".wotrapi_btn_save_endpoint").click(function(){
        var edited_slug = $(".wotrapi_default_endpoint").val();
        var edited_url = $(".wotrapi_save_endpoints span").text();
        $('.wotrapi_save_endpoints').addClass('d-none');
        $('.wotrapi_edit_endpoints').removeClass('d-none');
        
        var new_endpoint = edited_url + edited_slug;

        if (edited_slug.length > 0) {
            $('.wotrapi_blogdata_submit').prop('disabled', false);
            $('.wotrapi_editSlug').html(new_endpoint).attr('href', new_endpoint);
        }else{
            $('.wotrapi_blogdata_submit').prop('disabled', false);
            $('<div class="notice notice-error is-dismissible" style="margin-left: -4px;"><p>end point is set to default.</p></div>').insertAfter($('.wotrapi_customheader'));
        }
    });

});