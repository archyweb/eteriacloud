<ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">
    <li class="nav-item dropdown">
        
        <a class="nav-link d-flex align-items-center dropdown-toggle fa-icon-wait fs-9 pe-1 py-0" href="#" role="button" id="themeSwitchDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-sun fs-7" data-fa-transform="shrink-2" data-theme-dropdown-toggle-icon="light"></span><span class="fas fa-moon fs-7" data-fa-transform="shrink-3" data-theme-dropdown-toggle-icon="dark"></span><span class="fas fa-adjust fs-7" data-fa-transform="shrink-2" data-theme-dropdown-toggle-icon="auto"></span></a>
        <div class="dropdown-menu dropdown-menu-end dropdown-caret dropdown-menu-card border py-0 mt-3" aria-labelledby="themeSwitchDropdown">
          <div class="bg-white dark__bg-1000 rounded-2 py-2">
              <a class="dropdown-item fw-bold text-warning" href="#!" id="close_tabs"><span class="fas fa-window-close me-1"></span><span>Chiudi schede</span></a>
              <a class="dropdown-item fw-bold text-success" href="https://manuale.archymedia.com/?customer=<?php echo $app->config['customer_bucket']; ?>" target="_blank"><span class="fas fa-book-reader me-1"></span><span>Manuale d'uso</span></a>
          </div>
        </div>
        
    </li>

    <li class="nav-item dropdown" id="notices">
      <a class="nav-link notification-indicator notification-indicator-primary px-0 fa-icon-wait" id="navbarDropdownNotification" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-hide-on-body-scroll="data-hide-on-body-scroll">
          <span class="fas fa-bell" data-fa-transform="shrink-6" style="font-size: 33px;"></span>
          <span id="notices_count" class="notification-indicator-number"></span>
      </a>
      <div class="dropdown-menu dropdown-caret dropdown-menu-end dropdown-menu-card dropdown-menu-notification dropdown-caret-bg" aria-labelledby="navbarDropdownNotification">
        <div class="card card-notification shadow-none">
          <div class="card-header">
            <div class="row justify-content-between align-items-center">
              <div class="col-auto">
                <h6 class="card-header-title mb-0">Notifiche</h6>
              </div>
              <div class="col-auto ps-0 ps-sm-3"><a class="card-link fw-normal" href="javascript:void(0)" id="mark_notices_as_read">Segna tutte come già lette</a></div>
            </div>
          </div>
          <div class="scrollbar-overlay" style="max-height:19rem">
            <div class="list-group list-group-flush fw-normal fs-10 notices_items">
              <!-- LOAD NOTICES -->              
            </div>
          </div>
          <div class="card-footer text-center border-top"><a class="card-link d-block eteria-target" href="archy/app.php?view=notices" data-id="archy_notices">Mostra tutte</a></div>
        </div>
      </div>
    </li>

    <li class="nav-item dropdown">
        
        <a class="nav-link d-flex align-items-center dropdown-toggle fa-headset fs-9 pe-1 py-0" href="#" role="button" id="themeSwitchDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="fas fa-headset" data-fa-transform="shrink-6" style="font-size: 33px;"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-end dropdown-caret dropdown-menu-card border py-0 mt-3" aria-labelledby="themeSwitchDropdown">
          <div class="bg-white dark__bg-1000 rounded-2 py-2">
            <a href="archy/app.php?view=tickets" data-id="archy_tickets" class="dropdown-item eteria-target">Assistenza clienti</a>

          </div>
        </div>
        
    </li>
    
    <li class="nav-item dropdown">
        <a class="nav-link pe-0 ps-2" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <div class="avatar avatar-xl">
          <img class="rounded-circle" src="/admin_template/assets/img/team/avatar.png" alt="" />
        </div>
      </a>
      <div class="dropdown-menu dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
        <div class="bg-white dark__bg-1000 rounded-2 py-2">
          <a class="dropdown-item fw-bold text-warning" href="#!"><span class="fas fa-crown me-1"></span><span><?php echo htmlspecialchars($app->user_full_name, ENT_QUOTES, 'UTF-8'); ?></span></a>

          <div class="dropdown-divider"></div>
          <a href="archy/app.php?view=password" data-id="archy_password" class="dropdown-item eteria-target">Cambio password</a>
          
          <div class="dropdown-divider"></div>
          
          <a href="?do_action=logout" class="dropdown-item">Logout</a>
        </div>
      </div>
    </li>
  </ul>
