<nav class="navbar navbar-light navbar-glass navbar-top navbar-expand">

    <button class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
    <a class="navbar-brand me-1 me-sm-3" href="index.php">
      <div class="d-flex align-items-center"><img class="me-2" src="/admin_template_child/img/logo_archy_art.png" alt="" width="40" />
      </div>
    </a>
    <ul class="navbar-nav align-items-center d-none d-lg-block">
        <li class="nav-item">
            Benvenuto in Eteria Cloud
        </li>
    </ul>
    <?php 
        //$app->include_navbar_right(); 
        require_once $child_template_root.'/html/navbar_right.php';
        $app->include_custom_navbar_right();
    ?>
</nav>