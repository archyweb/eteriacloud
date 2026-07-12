
    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src="/admin_template/vendors/popper/popper.min.js"></script>
    <script src="/admin_template/vendors/bootstrap/bootstrap.min.js"></script>
    <script src="/admin_template/vendors/anchorjs/anchor.min.js"></script>
    <script src="/admin_template/vendors/is/is.min.js"></script>
    <script src="/admin_template/vendors/lodash/lodash.min.js"></script>
    <script src="/admin_template/vendors/prism/prism.js"></script>
    <script src="/admin_template/vendors/fontawesome/all.min.js"></script>    
    <script src="/admin_template/vendors/list.js/list.min.js"></script>
    <script src='/admin_template_child/js/fullcalendar/core/index.global.js'></script>
    <script src='/admin_template_child/js/fullcalendar/core/locales/it.global.js'></script>
    <script src="/admin_template/vendors/fullcalendar/main.min.js"></script>
    <script src="/admin_template/assets/js/flatpickr.js"></script>
    <script src="/admin_template/vendors/echarts/echarts.min.js"></script>
    <script src="/admin_template/vendors/dayjs/dayjs.min.js"></script>
    <script src="/admin_template_child/js/dayjs/locale/it.js"></script>
    <script>dayjs.locale('it');</script>
    <script src="/admin_template/assets/js/theme.js"></script>    
    <script src="/admin_template/vendors/jquery/jquery.min.js"></script>
    
    <script src="/admin_template_child/js/global.js?v=<?php echo filemtime($child_template_root . '/js/global.js'); ?>"></script>
    <script src="/admin_template_child/js/index.js?v=<?php echo filemtime($child_template_root . '/js/index.js'); ?>"></script>
    
    <?php if (count($_GET) > 0 and !isset($_GET['mode'])): ?>
        <script type="text/javascript">
            var url = '<?php echo substr($_SERVER['REQUEST_URI'], 1)?>';
            $('.eteria-navbar a[href="archy/app.php' + url + '"]').trigger('click');
        </script>
    <?php endif ?>

    </body>
</html>
