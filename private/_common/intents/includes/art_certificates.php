<script type="text/javascript">
window.onload = function () {

    $('#search_modal').on('hidden.bs.modal', function () {

        var opera_id = $('[name="main[opera_id]"]').val();

        if (!opera_id || parseInt(opera_id) <= 0) {
            return;
        }

        $.ajax({
            type: "POST",
            url: root + "archy/app.php?do_action=get_art_catalog_field_byid",
            data: {
                target: 'numero_archiviazione',
                field_id: opera_id
            },
            cache: false,
            success: function (response) {
                response = $.trim(response);

                if (response !== '') {
                    $('input[name="main[numero_archiviazione_certificato]"]').val(response);
                }
            }
        });

    });

};
</script>