<nav class="navbar navbar-light navbar-glass navbar-top navbar-expand-lg">
    <button class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarStandard" aria-controls="navbarStandard" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
          <a class="navbar-brand me-1 me-sm-3" href="#">
            <div class="d-flex align-items-center"><img class="me-2" src="/admin_template_child/img/logo/logo-red.png" alt="" width="40" />
            </div>
          </a>
    
    
    <div class="navbar-collapse offcanvas-lg offcanvas-start scrollbar eteria-navbar archy-main-menu-offcanvas" tabindex="-1" id="navbarStandard" aria-labelledby="navbarStandardLabel">
        <div class="offcanvas-header">
            <div class="d-flex align-items-center" id="navbarStandardLabel">
                <img class="me-2" src="/admin_template_child/img/logo/logo-red.png" alt="" width="36" />
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#navbarStandard" aria-label="Chiudi menu"></button>
        </div>
        <div class="offcanvas-body archy-main-menu-body">
            <?php
                echo $app->load_menu();
            ?>
        </div>
    </div>
    <?php 
        //$app->include_navbar_right(); 
        require_once $child_template_root.'/html/navbar_right.php';
        $app->include_custom_navbar_right();
    ?>
</nav>
