// JavaScript Document
/*$('body').tooltip({
    selector: '[data-toggle="tooltip"], .tt',
    container: 'body'
});*/

(function () {

  let activeRequests = 0;
  let progress = 0;
  let progressInterval;

  const loader = document.getElementById("ajax-loader");
  const progressBar = document.getElementById("ajax-progress");
  const progressInner = document.getElementById("ajax-progress-bar");

  function startProgress() {
    progress = 10;
    progressBar.style.display = "block";
    progressInner.style.width = progress + "%";

    progressInterval = setInterval(() => {
      if (progress < 90) {
        progress += Math.random() * 10;
        progressInner.style.width = progress + "%";
      }
    }, 200);
  }

  function completeProgress() {
    clearInterval(progressInterval);
    progress = 100;
    progressInner.style.width = "100%";

    setTimeout(() => {
      progressBar.style.display = "none";
      progressInner.style.width = "0%";
    }, 300);
  }

  function showLoader() {
    if (loader) loader.classList.remove("d-none");
  }

  function hideLoader() {
    if (loader) loader.classList.add("d-none");
  }

  function requestStarted() {
    activeRequests++;

    if (activeRequests === 1) {
      showLoader();
      startProgress();
    }
  }

  function requestFinished() {
    activeRequests--;

    if (activeRequests <= 0) {
      activeRequests = 0;
      hideLoader();
      completeProgress();
    }
  }

  // 🌐 ESPOSIZIONE GLOBALE
  window.globalLoader = {
    start: requestStarted,
    stop: requestFinished
  };

  // 🔥 FETCH
  const originalFetch = window.fetch;
  window.fetch = function (...args) {
    requestStarted();
    return originalFetch(...args)
      .then(res => {
        requestFinished();
        return res;
      })
      .catch(err => {
        requestFinished();
        throw err;
      });
  };

  // 🔥 XHR
  const originalOpen = XMLHttpRequest.prototype.open;
  const originalSend = XMLHttpRequest.prototype.send;

  XMLHttpRequest.prototype.open = function () {
    this.addEventListener("loadend", requestFinished);
    return originalOpen.apply(this, arguments);
  };

  XMLHttpRequest.prototype.send = function () {
    requestStarted();
    return originalSend.apply(this, arguments);
  };

  // 🔥 FORM SUBMIT
  document.addEventListener('submit', function (e) {
    const form = e.target;

    if (form.dataset.noLoader === "true") return;

    requestStarted();

    setTimeout(() => {
      requestFinished();
    }, 10000);
  });

  // 🔥 LINK CLICK (redirect)
  document.addEventListener('click', function (e) {
    const link = e.target.closest('a');
    if (!link) return;

    const href = link.getAttribute('href');

    // condizioni da ignorare
    if (
      !href ||
      href.startsWith('#') ||
      href.startsWith('javascript:') ||
      link.target === '_blank' ||
      link.hasAttribute('download') ||
      e.ctrlKey || e.metaKey || e.shiftKey || e.altKey ||
      link.dataset.noLoader === "true"
    ) return;

    requestStarted();

    // fallback sicurezza
    setTimeout(() => {
      requestFinished();
    }, 10000);
  });

})();

// FUNCTIONS ART SECTION
$('input[name="main[opera_id]"]').on('input',function(){
        var id=$(this).val();
        if(id != ''){
            $.ajax({
                type: "POST",
                url: "/archy/app.php?do_action=getArtCatalogData",
                data: {"id": id},
                dataType: "json"
            }).done(function(data) {
                var response = eval(data);                                
                if($('#'+intent+'_titolo_opera').length>0){ $('#'+intent+'_titolo_opera').val(response[0].titolo_opera); }
                if($('#'+intent+'_valore_opera').length>0){ $('#'+intent+'_valore_opera').val(response[0].stima); }
                if($('#'+intent+'_nominativo_autore').length>0){ $('#'+intent+'_nominativo_autore').val(response[0].nominativo_autore); }
            }).fail(function(data) {
                alert('Errore nella selezione, si prega di riprovare');
            });
        }
});

// FUNCTIONS FOR SELECT AND SET MODEL CONTENT
$('input[name="main[model_id]"]').on('input',function(){
        var id=$(this).val();
        var current_intent=$('input[name="intent"]').val();
        if(id!=''){
                $.ajax({
                        type: "POST",
                        url: "/archy/app.php?do_action=get_model", 
                        data: 'id='+id,
                        cache: false,
                        success: function(html){
                            if($('#'+current_intent+'_model_template').length>0){
                                tinymce.get(current_intent+'_model_template').setContent(html);
                                //tinymce.get(current_intent+'_model_template').save();
                            }
                        }
                });
        }
});

function insertToEditor(e,placeholder){
    
    if(e.parents('.reminders_accordion').length>0){
        var rel=e.parents('.placeholder_container').data('rel');
        var target=e.parents('.placeholder_container').find('.placeholder_target_'+rel).val();
        if(target==1){ // oggetto
            $('#archy_reminders_reminder_subject'+rel).val($('#archy_reminders_reminder_subject'+rel).val()+placeholder);
        }else{
            tinymce.get('archy_reminders_reminder_description'+rel).insertContent(placeholder);
        }
    }else{
        var rel=e.parents('.placeholder_container').data('rel');
        var target=e.parents('.placeholder_container').find('.placeholder_target_'+rel);
        var target_editor = rel;
        if(rel+'_description'.length > 0){ target_editor = rel+'_description'; }
        if(target.length>0){
                if(target.val()==1){ // oggetto
                        var input = $('#'+rel+'_subject')[0];
                        var text = placeholder;
                        input.value = input.value.substring(0, input.selectionStart) + text + input.value.substring(input.selectionEnd);
                        input.selectionStart = input.selectionEnd = input.selectionStart + text.length;
                        //$('#'+rel+'_subject').val($('#'+rel+'_subject').val()+e.val());
                }else{
                    
                    tinymce.get(target_editor).insertContent(placeholder);
                }
        }else{
            tinymce.get(target_editor).insertContent(placeholder);
        }
    }
    
}

// FUNCTIONS FOR PLACEHOLDER CLASS
$('.placeholder_container select.placeholder_options').on('change',function(){
    //var placeholder = '';
    if($(this).data('rel')=='templateSelector'){
        const template = this.value;
        const elem = $(this);
        $.ajax({
                type: "POST",
                url: "/archy/app.php?do_action=get_placeholder_template", 
                data: 'template='+template,
                dataType: 'text',
                success: function(response){
                    //placeholder = response;
                    insertToEditor(elem,response);
                }
        });
    }else{
        var placeholder = $(this).val();
        insertToEditor($(this),placeholder);
    }
    
    
    $(this).prop('selectedIndex',0);
});

// FUNCTIONS FOR REMINDERS CLASS
$(".add_reminder").click(function(){
    if(confirm('Vuoi creare un nuovo promemoria?')){  
        var origin = $(this).data('origin');
        var origin_id = $(this).data('origin-id');
        var addEl = $(this);
        $.ajax({
                type: "POST",
                url: "/archy/app.php",
                cache: false,
                data: 'do_action=dispatch_ajax_load&class=Reminder&method=load_reminder_layout&reminder_origin='+origin+'&reminder_origin_id='+origin_id,
                success: function(html){
                    if(addEl.parent(".reminders_accordion").find('.panel').length > 0){ $(html).insertBefore(addEl); }
                    else{ addEl.parent(".reminders_accordion").find('.panel-placeholder').append(html); }
                    init_tinymce();
                }
        });
    }
    return false;
});
$('.reminders_accordion').on('click','.trashReminder',function(){
    
    if(confirm('Rimuovere questo promemoria?')){        
        var addEl = $(this);
        var id = 0;
        var currentIsPanel = false;
        var panelClass = '.accordion-item';
        if(addEl.parent(".reminders_accordion").find('.panel').length > 0){ currentIsPanel = true; }
        
        if(currentIsPanel){ id=$(this).parents('.panel-heading').data('id'); }
        else{ id=$(this).parents('.panel-heading-reminders').data('id'); }
                
        if(currentIsPanel){ panelClass = '.panel'; }
        
        $('#heading_reminders_accordion_'+id).parent(panelClass).slideUp(function(){$(this).remove()});
        
        if(id!=''){
            $.ajax({
                    type: "POST",
                    url: "/archy/app.php",
                    data: 'do_action=dispatch_ajax_load&class=Reminder&method=delete&id='+id,
                    cache: false
            });
        }
    }
    return false;
});
$('.reminders_accordion').on('click','.saveReminder',function(){
    if(confirm('Vuoi salvare questo promemoria?')){
        var addEl = $(this);
        var id = 0;
        var currentIsPanel = false;
        var panelClass = '.accordion-item';
        if(addEl.parent(".reminders_accordion").find('.panel').length > 0){ currentIsPanel = true; }        
        
        if(currentIsPanel){ id=$(this).parents('.panel-heading').data('id'); }
        else{ id=$(this).parents('.panel-heading-reminders').data('id'); }
        
        if(currentIsPanel){ panelClass = '.panel'; }
        //tinymce.get('archy_reminders_reminder_description'+id).triggerSave();
        var reminder_description = tinymce.get('archy_reminders_reminder_description'+id).getContent();
        $.ajax({
                type: "POST",
                url: "/archy/app.php",
                data: 'do_action=dispatch_ajax_load&class=Reminder&method=save&id='+id+'&'+$('#reminders_accordion_'+id).find("select, input").serialize()+'&reminder_description='+reminder_description,
                cache: false,
                success:function(html){
                    var title = $('#archy_reminders_reminder_title'+id).val();
                    if(currentIsPanel){ $('#heading_reminders_accordion_'+id+' .panel-title a').text(title); }
                    else{ $('#heading_reminders_accordion_'+id+' button.accordion-button').text(title); }
                }
        });
    }
    return false;
});

$('body').on('keydown', '.form-control.numbers_only, .numbers_only .form-control', function(e) {
    var second_dot = ($.inArray(e.keyCode, [190, 110]) !== -1) && ($(this).val().toString().indexOf('.') !== -1);
    var not_valid_minus = ($.inArray(e.keyCode, [109, 189]) !== -1) && ($(this).get(0).selectionStart > 0);
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 190, 110, 109, 189]) !== -1 ||
        (e.keyCode == 65 && e.ctrlKey === true) ||
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        if (second_dot || not_valid_minus) {
            e.preventDefault();
        } else {
            return;
        }
    }
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});
$('body').on('keydown', '.form-control.integers_only, .integers_only .form-control', function(e) {
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
        (e.keyCode == 65 && e.ctrlKey === true) ||
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        return;
    }
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});

var datetime_options = {
    format: 'DD/MM/YYYY HH:mm',
    lang: 'it',
    weekStart: 1,
    cancelText: 'INDIETRO',
    switchOnClick: true
}
$('.datetime').each(function() {
    var custom_options = datetime_options;
    var min_date = $(this).data('min_date');
    if (typeof min_date !== 'undefined') {
        if (min_date == '') {
            custom_options.minDate = new Date();
        } else {
            custom_options.minDate = new Date(min_date);
        }
    }
    $(this).bootstrapMaterialDatePicker(custom_options);
});
$('.trigger_datetime').click(function() {
    var target = $(this).data('target');
    $(target).trigger('focus');
});

function current_timestamp() {
    return new Date().toISOString().slice(0, 10) + ' ' + new Date().toLocaleTimeString();
}

function disable_buttonsbar() {
    $('#edit_buttons a.btn').addClass('disbaled');
    $('#edit_buttons button.btn').prop('disabled', true);
}

function enable_buttonsbar() {
    $('#edit_buttons a.btn').removeClass('disbaled');
    $('#edit_buttons button.btn').prop('disabled', false);
}

$('textarea.sms').on('input', function() {
    var notice_box = $(this).data('chars');
    var count = $(this).val().length;
    $(notice_box).text(count);
});
$('textarea.sms').trigger('input');
$('.select_cols').click(function() {
    var target = $('.orderable_labels input[type="checkbox"]', $(this).parents('.panel'));
    target.prop('checked', $(this).hasClass('glyphicon-plus-sign'));
    $(this).toggleClass('glyphicon-plus-sign').toggleClass('glyphicon-minus-sign');
});

/* FUNZIONE PER GESTOINE MESSAGGI */
if ($('.system-message.alert-success').length > 0) {
    $('.system-message.alert-success').append('<i class="system-message-countdown"></i>');
    $('.system-message-countdown').animate({ width: '100%' }, 1600, function() { $('.system-message.alert-success button').trigger('click'); });
}

var hot_keys = {

    85: {
        key: 'u',
        id: 'archy_users'
    },
    65: {
        key: 'a',
        id: 'archy_registry'
    },
    67: {
        key: 'c',
        id: 'archy_registry_categories'
    }
   
};

$(document).keydown(function(e) {

    if (e.ctrlKey && e.shiftKey && hot_keys.hasOwnProperty(e.keyCode)) {

        var where = $('#navbar').length > 0 ?
            window :
            parent.window;

        var id = hot_keys[e.keyCode]['id'];

        where.$('[data-id="' + id + '"]').trigger('click');
        if (e.altKey) {
            where.$('#' + id + ' iframe').attr('src', 'archy/app.php?intent=' + id + '&view=edit');
        }

    }
});