<!-- ===============================================-->
<!--    Main Content-->
<!-- ===============================================-->
<main class="main" id="top">
  <div class="container-fluid body-container" data-layout="container-fluid">
    <script>
      var isFluid = JSON.parse(localStorage.getItem('isFluid'));
      if (isFluid) {
        var container = document.querySelector('[data-layout]');
        container.classList.remove('container');
        container.classList.add('container-fluid');
      }
    </script>
    <?php require_once $child_template_root . '/html/navbar.php'; ?>
    <div class="content">
        <div class="row g-3 mb-3">
          <div class="col-lg-12 col-xl-12 pe-lg-2 mb-3">
            <div class="h-lg-100">
                <div class="archy-tabs-shell">
                    <ul class="nav nav-tabs" role="tablist" id="sl_tabs">
                        <li class="nav-item">
                            <a class="nav-link active" href="#dashboard" id="dashboard-tab" data-bs-target="#dashboard" aria-selected="true" aria-controls="dashboard" role="tab" data-bs-toggle="tab">Dashboard <span class="far fa-window-close"></span></a>
                        </li>
                    </ul>
                    <div class="dropdown archy-tabs-mobile-menu">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="archyTabsMobileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="fas fa-layer-group"></span>
                            <span class="ms-1">Schede</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" id="sl_tabs_mobile" aria-labelledby="archyTabsMobileDropdown"></div>
                    </div>
                </div>
                <div class="tab-content border border-top-0 p-3" id="sl_tabs_contents">
                    <div role="tabpanel" class="tab-pane fade show active" id="dashboard" aria-labelledby="dashboard-tab">
                        <iframe class="tabbed_frame" id="tabbed_frame" src="archy/app.php?view=dashboard"></iframe>
                    </div>
                </div>
            </div>
          </div>
        </div>
        <footer class="footer">
          <div class="row g-0 justify-content-between fs-10 mt-4 mb-3">
            <div class="col-12 col-sm-auto text-center">
              <p class="mb-0 text-600">Eteria Cloud</p>
            </div>
            <div class="col-12 col-sm-auto text-center">
              <p class="mb-0 text-600">v2.0</p>
            </div>
          </div>
        </footer>
    </div>        
  </div>
</main>
<!-- ===============================================-->
<!--    End of Main Content-->
<!-- ===============================================-->
