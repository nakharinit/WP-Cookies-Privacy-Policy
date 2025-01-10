
jQuery(document).ready(function($) {
    $('#cpp-accept, #cpp-settings').on('click', function() {
        var consent = $(this).attr('id') === 'cpp-accept' ? 'accept' : 'settings';
        if (consent === 'settings') {
            $('#cpp-settings-popup').fadeIn();
        } else {
            $.post(cpp_ajax_url.url, { action: 'cpp_consent', consent: consent }, function() {
                $('#cpp-banner').fadeOut();
            });
        }
    });
    $('#cpp-save-settings').on('click', function() {
        alert('การตั้งค่าคุกกี้ของคุณถูกบันทึกแล้ว!');
        $('#cpp-settings-popup').fadeOut();
    });
    $('#cpp-close-settings').on('click', function() {
        $('#cpp-settings-popup').fadeOut();
    });
});
