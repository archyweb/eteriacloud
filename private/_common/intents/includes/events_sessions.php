
<?php require $main_root.'/private/_common/intents/includes/events_subscribers.php'; ?>
           
<script type="text/javascript">
    window.onload = function() {
        $('.regenerates_attachments').click(function() {
            if( confirm('Sei sicuro di voler rigenerare tutti gli allegati?' )) {
                var event_id = $('#session_event_id').val();
                var session_id = $('#session_id').val();
                $.ajax({
                    type: "POST",
                    url: '/archy/app.php?do_action=regenerates_attachments',
                    data: 'intent=' + intent + '&event_id=' + event_id + '&session_id=' + session_id,
                    cache: false,
                    beforeSend: function() {
                        $('#unloading').css('display', 'block');
                    },
                    success: function(response) {
                        if(response== 'OK'){ alert('Generazione avvenuta con successo'); }
                        else{ alert(response); }
                        location.reload();
                    },
                    error: function (request, status, error) {
                        alert('Errore nella generazione, riprovare più tardi');
                        $('#unloading').css('display', 'none');
                    }
                });
            }
        });
        
        $('#sessions_accordion').on('input', 'input[id^="archy_events_sessions_title"]', function() {
            if ($(this).val() == '') {
                $('.panel-title a', $(this).parents('.panel')).text('Sessione');
            } else {
                $('.panel-title a', $(this).parents('.panel')).text($(this).val());
            }
        });
        $('input[id^="archy_events_sessions_title"]').trigger('input');

        $('#sessions_accordion').on('click', '.add_speech', function() {
            var target = $('.row.speakers', $(this).parents('.panel'));
            var d = new Date();
            var id_suffix = d.getTime();
            $.ajax({
                type: "POST",
                url: "/archy/app.php?do_action=load_event_speech",
                data: 'id_suffix=' + id_suffix,
                cache: false,
                success: function(html) {
                    target.append(html);
                    dates();
                    init_tinymce();
                }
            });
        });
        $('.row.speakers').sortable({
            handle: ".move"
        });
        $('#sessions_accordion').on('click', '.speakers speech .trash', function() {
            if (confirm('Rimuovere intervento?')) {
                $(this).parents('speech').slideUp(function() {
                    $(this).remove()
                });
            }
        });

        $('#sessions_accordion').on('click', '.add_session_extra', function() {
            var scope = $(this).parents('.panel');
            $.ajax({
                type: "POST",
                url: "/archy/app.php?do_action=load_session_extra",
                cache: false,
                success: function(html) {
                    $('.row.session_extras', scope).append(html);
                }
            });
        });
        $('.row.session_extras').sortable({
            handle: ".move"
        });
        $('#sessions_accordion').on('click', '.session_extras speech .trash', function() {
            if (confirm('Rimuovere extra?')) {
                $(this).parents('speech').slideUp(function() {
                    $(this).remove()
                });
            }
        });

    }
</script>
<style type="text/css">
    speech {
        display: block;
        border: 5px solid #eee;
        border-right-width: 25px;
        margin: 0 15px 15px 15px;
        padding-top: 10px;
        border-radius: 15px;
        position: relative;
        background: #fff;
    }

    speech:after {
        content: "";
        display: block;
        clear: both;
    }

    speech .move,
    speech .trash {
        position: absolute;
        right: -25px;
        font-size: 15px;
        padding: 5px;
    }

    speech .move {
        top: 0;
        cursor: move;
    }

    speech .trash {
        bottom: 0;
        top: auto !important;
        cursor: pointer;
    }

    .move_session {
        cursor: move;
    }

    .trash_session,
    .duplicate_session {
        cursor: pointer;
    }
</style>