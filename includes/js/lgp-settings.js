jQuery(document).ready(function($) {
    $('input[id$="_button"]').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var id = button.attr('id').replace('_button', '');
        var image = wp.media({
            title: 'Upload Image',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            var image_id = uploaded_image.id;
            
            $('#' + id + '_preview').attr('src', image_url).show();
            $('#' + id).val(image_id);
            $('#' + id + '_id').text(image_id);
        });
    });

    $('input[id$="_remove"]').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var id = button.attr('id').replace('_remove', '');
        
        $('#' + id + '_preview').attr('src', lgpSettings.defaultImagePlaceholder).hide();
        $('#' + id).val('');
        $('#' + id + '_id').text('');
    });

    // Updating acf custom_uri field in listings
    $('#update-custom-uri').on('click', function() {
        if (confirm('Are you sure you want to update the custom URI for all published listings?')) {
            $.post(lgpSettings.ajaxUrl, {
                action: 'update_custom_uri'
            }, function(response) {
                alert(response.message);
            });
        }
    });
});