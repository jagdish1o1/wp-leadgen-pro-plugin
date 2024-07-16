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
            $('#' + id + '_preview').attr('src', image_url);
            $('#' + id).val(uploaded_image.id);
        });
    });

    $('input[id$="_remove"]').click(function(e) {
        e.preventDefault();
        var button = $(this);
        var id = button.attr('id').replace('_remove', '');
        $('#' + id + '_preview').attr('src', '');
        $('#' + id).val('');
    });
});