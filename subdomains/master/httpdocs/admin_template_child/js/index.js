// JavaScript Document
var tab_history=['#dashboard'];

function show_tab(tabTarget){
    var trigger = document.querySelector('#sl_tabs a[href="' + tabTarget + '"]');
    if(!trigger){
        return;
    }

    if(typeof bootstrap !== 'undefined' && bootstrap.Tab){
        bootstrap.Tab.getOrCreateInstance(trigger).show();
    }else if(typeof $ !== 'undefined' && typeof $(trigger).tab === 'function'){
        $(trigger).tab('show');
    }else{
        $('#sl_tabs a, #sl_tabs_contents .tab-pane').removeClass('active show');
        $(trigger).addClass('active');
        $(tabTarget).addClass('active show');
    }
}

function get_tab_label($tab){
    var $label = $tab.clone();
    $label.find('.fa-window-close, svg, key').remove();
    return $.trim($label.text());
}

function sync_mobile_tabs(){
    var $mobileTabs = $('#sl_tabs_mobile');

    if(!$mobileTabs.length){
        return;
    }

    $mobileTabs.empty();

    $('#sl_tabs a').each(function(){
        var $tab = $(this);
        var tabTarget = $tab.attr('href');

        if(tabTarget === '#dashboard'){
            return;
        }

        var activeClass = $tab.hasClass('active') ? ' active' : '';
        var tabLabel = get_tab_label($tab);
        var $mobileItem = $('<button type="button" class="dropdown-item d-flex align-items-center justify-content-between archy-mobile-tab' + activeClass + '"></button>');

        $mobileItem.attr('data-tab-target', tabTarget);
        $mobileItem.append('<span class="archy-mobile-tab-label"></span>');
        $mobileItem.find('.archy-mobile-tab-label').text(tabLabel);
        $mobileItem.append('<span class="far fa-window-close archy-mobile-tab-close"></span>');
        $mobileTabs.append($mobileItem);
    });

    $('.archy-tabs-mobile-menu').toggleClass('has-tabs', $mobileTabs.children().length > 0);
}

function sync_tab_history(tab_id){
    remove_history(tab_id);
    add_history(tab_id);
}

function close_main_menu_on_mobile(){
    if(!window.matchMedia || !window.matchMedia('(max-width: 991.98px)').matches){
        return;
    }

    var menu = document.getElementById('navbarStandard');
    if(!menu || typeof bootstrap === 'undefined' || !bootstrap.Offcanvas){
        return;
    }

    var offcanvas = bootstrap.Offcanvas.getInstance(menu) || bootstrap.Offcanvas.getOrCreateInstance(menu);
    if(offcanvas){
        offcanvas.hide();
    }
}

function close_main_menu_dropdowns(){
    var menu = document.getElementById('navbarStandard');
    if(!menu){
        return;
    }

    menu.querySelectorAll('.nav-item.dropdown').forEach(function(item){
        item.classList.add('archy-dropdown-closed');
    });

    menu.querySelectorAll('.dropdown-toggle.show').forEach(function(toggle){
        toggle.classList.remove('show');
        toggle.setAttribute('aria-expanded', 'false');

        if(typeof bootstrap !== 'undefined' && bootstrap.Dropdown){
            var dropdownInstance = bootstrap.Dropdown.getInstance(toggle);
            if(dropdownInstance){
                dropdownInstance.hide();
            }
        }
    });

    menu.querySelectorAll('.dropdown-menu.show').forEach(function(dropdown){
        dropdown.classList.remove('show');
        dropdown.removeAttribute('data-bs-popper');
        dropdown.removeAttribute('data-popper-placement');
        dropdown.style.position = '';
        dropdown.style.top = '';
        dropdown.style.bottom = '';
        dropdown.style.left = '';
        dropdown.style.right = '';
        dropdown.style.zIndex = '';
        dropdown.style.transform = '';
        dropdown.style.maxWidth = '';
    });
}

function reset_main_menu_for_desktop(){
    if(!window.matchMedia || !window.matchMedia('(min-width: 992px)').matches){
        return;
    }

    var menu = document.getElementById('navbarStandard');
    if(!menu){
        return;
    }

    if(typeof bootstrap !== 'undefined' && bootstrap.Offcanvas){
        var offcanvas = bootstrap.Offcanvas.getInstance(menu);
        if(offcanvas){
            offcanvas.hide();
        }
    }

    menu.classList.remove('show', 'showing', 'hiding');
    menu.removeAttribute('aria-modal');
    menu.removeAttribute('role');
    document.body.classList.remove('offcanvas-backdrop', 'offcanvas-open', 'modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');
    document.querySelectorAll('.offcanvas-backdrop').forEach(function(backdrop){
        backdrop.remove();
    });
}

function init_desktop_main_menu_dropdowns(){
    var menu = document.getElementById('navbarStandard');
    if(!menu){
        return;
    }

    var isFirefox = navigator.userAgent.toLowerCase().indexOf('firefox') !== -1;

    menu.querySelectorAll('.nav-item.dropdown').forEach(function(item){
        if(item.dataset.archyDesktopDropdownReady === '1'){
            return;
        }

        item.dataset.archyDesktopDropdownReady = '1';

        item.addEventListener('mouseenter', function(){
            if(!window.matchMedia || !window.matchMedia('(min-width: 992px)').matches){
                return;
            }

            if(item.classList.contains('archy-dropdown-closed')){
                return;
            }

            var toggle = item.querySelector('.dropdown-toggle');
            var dropdown = item.querySelector('.dropdown-menu');

            if(toggle){
                toggle.classList.add('show');
                toggle.setAttribute('aria-expanded', 'true');
            }

            if(dropdown){
                dropdown.classList.add('show');
                dropdown.setAttribute('data-bs-popper', 'none');

                if(isFirefox && toggle){
                    var rect = toggle.getBoundingClientRect();

                    dropdown.removeAttribute('data-popper-placement');
                    dropdown.style.setProperty('position', 'fixed', 'important');
                    dropdown.style.setProperty('top', rect.bottom + 'px', 'important');
                    dropdown.style.setProperty('bottom', 'auto', 'important');
                    dropdown.style.setProperty('z-index', '10030', 'important');
                    dropdown.style.setProperty('transform', 'none', 'important');
                    dropdown.style.setProperty('max-width', 'calc(100vw - 16px)', 'important');

                    var dropdownWidth = dropdown.offsetWidth || 320;
                    var left = dropdown.classList.contains('dropdown-menu-end') ? rect.right - dropdownWidth : rect.left;
                    left = Math.max(8, Math.min(left, window.innerWidth - dropdownWidth - 8));
                    dropdown.style.setProperty('left', left + 'px', 'important');
                    dropdown.style.setProperty('right', 'auto', 'important');
                }
            }
        });

        item.addEventListener('mouseleave', function(){
            if(!window.matchMedia || !window.matchMedia('(min-width: 992px)').matches){
                return;
            }

            item.classList.remove('archy-dropdown-closed');

            var toggle = item.querySelector('.dropdown-toggle');
            var dropdown = item.querySelector('.dropdown-menu');

            if(toggle){
                toggle.classList.remove('show');
                toggle.setAttribute('aria-expanded', 'false');
            }

            if(dropdown){
                dropdown.classList.remove('show');
                dropdown.removeAttribute('data-bs-popper');
                dropdown.style.position = '';
                dropdown.style.top = '';
                dropdown.style.bottom = '';
                dropdown.style.left = '';
                dropdown.style.right = '';
                dropdown.style.zIndex = '';
                dropdown.style.transform = '';
                dropdown.style.maxWidth = '';
            }
        });
    });

    if(menu.dataset.archyDropdownClickCloseReady !== '1'){
        menu.dataset.archyDropdownClickCloseReady = '1';

        menu.addEventListener('click', function(event){
            var target = event.target.closest('a.eteria-target');

            if(!target || !window.matchMedia || !window.matchMedia('(min-width: 992px)').matches){
                return;
            }

            target.blur();

            window.setTimeout(function(){
                close_main_menu_dropdowns();

                if(document.activeElement && menu.contains(document.activeElement)){
                    document.activeElement.blur();
                }
            }, 0);
        });
    }
}

window.addEventListener('resize', reset_main_menu_for_desktop);
document.addEventListener('DOMContentLoaded', function(){
    reset_main_menu_for_desktop();
    init_desktop_main_menu_dropdowns();
});
///* nuove funzioni per togliere iframe */
/*document.addEventListener('DOMContentLoaded', function () {
    const stats = fetch('/archy/dashboard-stats.php');
    initCharts(stats);
});
*/
/*
let dashboardLoaded = false;
function loadDashboard() {
    if (dashboardLoaded) return;
    fetch('/archy/app.php?view=dashboard')
      .then(res => res.text())
      .then(html => {
        document.getElementById('dashboard').innerHTML = html;
        return fetch('/archy/dashboard-stats.php');
       // initDashboard(); // 🔥 IMPORTANTISSIMO
    })
    .then(res => res.json())
    .then(stats => {
      initDashboard(stats);
    });
}

function initDashboard(stats) {
    //destroyDashboard();
    //initCharts();
    //console.log(window.flatpickr);
    initFlatpickr();
    appCalendarInit();
    initCharts(stats);
    dropdownMenuInit();
}

function initFlatpickr() {
    document.querySelectorAll('.flatpickr').forEach(el => {
      if (!el._flatpickr) {
        flatpickr(el, {
          dateFormat: 'Y-m-d',
          allowInput: true
        });
      }
    });
}
*/
// CHARTS


// FUNZIONE GESTIONE APERTURA CHIUSURA MENU
$('.eteria-navbar li a.main-link').click(function(){
    var accordionHeaders = document.querySelectorAll('.eteria-navbar li a.main-link');
    var accordionCollapses = document.querySelectorAll('.eteria-navbar li ul.collapse'); 
    accordionHeaders.forEach(function (header) {
      //header.classList.add("collapsed");
      header.setAttribute('aria-expanded', 'false');
      header.addEventListener('click', function() {
          this.setAttribute('aria-expanded', 'true');
          if(this.classList.contains("collapsed")){
              this.setAttribute('aria-expanded', 'false');
          }
      });
    });
    accordionCollapses.forEach(function (collapse) {
      collapse.classList.remove("show");
      
    });
});

// FUNZIONE PER GESTIONE TABS NAVIGAZIONE CONTENUTI
$('body a.eteria-target').click(function(){
	var tab_id=$(this).data('id');
     
	var tab_name=$(this).html();
        if($(this).children('div').length > 0){tab_name=$(this).find('span.nav-link-text').html();}
	$('#sl_tabs li a, #sl_tabs_contents .tab-pane').removeClass('active').removeClass('show');
	if($('#'+tab_id).length==0){
            var frame_src=$(this).attr('href');
            $('#sl_tabs').append('<li class="nav-item"><a class="nav-link active" data-bs-target="#'+tab_id+'" id="'+tab_id+'-tab" href="#'+tab_id+'" aria-controls="'+tab_id+'" role="tab" data-bs-toggle="tab" aria-selected="true">'+tab_name+' <span class="far fa-window-close"></span></a> </li>');
            $('#sl_tabs_contents').append('<div role="tabpanel" class="tab-pane fade show active" id="'+tab_id+'" aria-labelledby="'+tab_id+'-tab"><div class="card overflow-hidden"><div class="card-body"><iframe class="tabbed_frame" ss-container src="'+frame_src+'"></iframe></div></div></div>');
            add_history($('#sl_tabs li a.active').attr('href'));
	}
	$('#sl_tabs li a#'+tab_id+'-tab, #sl_tabs_contents #'+tab_id).addClass('active').addClass('show');
    sync_tab_history('#' + tab_id);
    sync_mobile_tabs();
    close_main_menu_dropdowns();
    close_main_menu_on_mobile();

	return false;
});

if(location.hash){
	var data_id=location.hash.replace('#','');
	$('[data-id="'+data_id+'"]').trigger('click');
}

$('body').on('click','#sl_tabs .fa-window-close',function(){
    var tab_id = $(this).parent().attr('href');

    if(tab_id === '#dashboard'){
        return false;
    }

    remove_history(tab_id);

    var $li = $(this).parents('li');
    var isActive = $li.find('a').hasClass('active');
    var currentActive = $('#sl_tabs a.active').attr('href');

    // rimuovo contenuto e tab
    $(tab_id).remove();
    $li.remove();

    // reset stato
    $('#sl_tabs a').removeClass('active');
    $('#sl_tabs_contents .tab-pane').removeClass('active show');

    let tabToActivate = null;

    if(isActive){
        // se chiudo quello attivo → uso history
        if(tab_history.length > 0){
            tabToActivate = tab_history[tab_history.length - 1];
        } else {
            // fallback: primo disponibile
            tabToActivate = $('#sl_tabs a:visible').first().attr('href');
        }
    } else {
        // se NON è attivo → mantengo quello attuale attivo
        tabToActivate = currentActive;

        // se per qualche motivo non c'è più → fallback
        if(!tabToActivate){
            tabToActivate = $('#sl_tabs a:visible').first().attr('href');
        }
    }

    if(tabToActivate){
        var tab_id_t = tabToActivate.replace('#', '');

        show_tab(tabToActivate);
    }

    if($('#sl_tabs li:visible').length==0){
        $('#sl_tabs').css('border-bottom',0);
    }

    sync_mobile_tabs();
});

$('body').on('click', '#sl_tabs_mobile .archy-mobile-tab', function(e){
    if($(e.target).closest('.archy-mobile-tab-close').length){
        return;
    }

    show_tab($(this).data('tab-target'));
    sync_mobile_tabs();
});

$('body').on('click', '#sl_tabs_mobile .archy-mobile-tab-close', function(e){
    e.preventDefault();
    e.stopPropagation();

    var tabTarget = $(this).closest('.archy-mobile-tab').data('tab-target');
    $('#sl_tabs a[href="' + tabTarget + '"] .fa-window-close').trigger('click');
});

$('body').on('shown.bs.tab', '#sl_tabs a', function(){
    sync_tab_history($(this).attr('href'));
    sync_mobile_tabs();
});

$('#close_tabs').click(function(){
	$('#sl_tabs li').remove();
	$('#sl_tabs_contents').html('');
	tab_history=[];
	location.reload();
});

function remove_history(tab_id){
	var position = $.inArray(tab_id, tab_history);
    if(position >= 0){
	    tab_history.splice(position, 1);
    }
}
function add_history(tab_id){
	tab_history.push(tab_id);
}

sync_mobile_tabs();

//notices
function fetch_notices(){
	$.ajax({
		type: "POST",
		url: "archy/app.php?do_action=fetch_notices",
		data: '',
		dataType: 'json',
		cache: false,
		success: function(notices){
			$('#notices .notices_items').html(notices.html);
			$('#notices_count').text(notices.unread);
			if(notices.unread==0){
				$('#notices_count').fadeOut();
			}else{
				$('#notices_count').fadeIn();
			}
		}
	});
};
fetch_notices()
var interval_minutes=2;
setInterval(fetch_notices,interval_minutes*60000);

$('#mark_notices_as_read').click(function(){
	$.ajax({
		type: "POST",
		url: "archy/app.php?do_action=mark_notices_as_read",
		data: '',
		cache: false,
		success: function(notices){
			$('#notices .notices_items .list-group-item a.notification-unread').removeClass('notification-unread');
			$('#notices_count').fadeOut();
		}
	});
});

function mark_as_read(id){
	var counter=Number($('#notices_count').text());
	counter--;
	$('#notices_count').text(counter);
	if(counter==0){
		$('#notices_count').fadeOut();
	}
	$.ajax({
		type: "POST",
		url: "archy/app.php?do_action=mark_notice_as_read",
		data: 'id='+id,
		cache: false,
		success: function(notices){
			$('#notices .notices_items .list-group-item a[data-notice_id="'+id+'"]').removeClass('notification-unread');
		}
	});
}
/*$('body').on('click','.tabbed_frame',function(){
	alert('tabbed_frame click');
	//$('body',window.parent.document).trigger('click');
});*/
