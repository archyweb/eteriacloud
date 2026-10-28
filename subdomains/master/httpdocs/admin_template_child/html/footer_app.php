    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src="/admin_template/vendors/popper/popper.min.js"></script>
    <script src="/admin_template/vendors/bootstrap/bootstrap.min.js"></script>
    <script src="/admin_template/vendors/anchorjs/anchor.min.js"></script>
    <script src="/admin_template/vendors/is/is.min.js"></script>
    <script src="/admin_template/vendors/jquery/jquery.min.js"> </script>
    <script src="/admin_template/vendors/prism/prism.js"></script>
    <script src="/admin_template/vendors/select2/select2.min.js"> </script>
    <script src="/admin_template/vendors/select2/select2.full.min.js"> </script>
   <!-- <script src="/admin_template/vendors/datatables.net/jquery.dataTables.min.js"></script>
    <script src="/admin_template/vendors/datatables.net-bs5/dataTables.bootstrap5.min.js"> </script>-->
    <script src="/admin_template/vendors/datatables2/datatables.min.js"> </script>
    <script src="/admin_template/vendors/fontawesome/all.min.js"></script>
    <script src="/admin_template/vendors/lodash/lodash.min.js"></script>
    <script src="/admin_template/vendors/list.js/list.min.js"></script>
    <script src="/admin_template/vendors/tinymce/tinymce.min.js"></script>    
    <script src="/admin_template/vendors/choices/choices.min.js"></script>
    <script src='/admin_template_child/js/fullcalendar/core/index.global.js'></script>
    <script src='/admin_template_child/js/fullcalendar/core/locales/it.global.js'></script>
    <script src="/admin_template/vendors/fullcalendar/main.min.js"></script>
    <script src="/admin_template/vendors/echarts/echarts.min.js"></script>
    <script src="/admin_template/assets/js/flatpickr.js"></script>
    <script src="/admin_template/vendors/dayjs/dayjs.min.js"></script>
    <script src="/admin_template_child/js/dayjs/locale/it.js"></script>
    <script>dayjs.locale('it');</script>
        
    <script src="/admin_template/vendors/inputmask/jquery.inputmask.min.js"></script>
    <?php $theme_js_path = $_SERVER['DOCUMENT_ROOT'] . '/admin_template/assets/js/theme.js'; ?>
    <script src="/admin_template/assets/js/theme.js?v=<?php echo is_file($theme_js_path) ? filemtime($theme_js_path) : time(); ?>"></script>
    
    <script type="text/javascript">
        var intent = '<?php echo @$_GET['intent']?>';
        var intent_filter = '<?php echo @$_GET['intent_filter']?>';
        var root = '/';
        var username = '<?php echo $app->username?>';
        var association =<?php echo $app->config['association'] ? 'true' : 'false'?>;
    </script>
    
    <!-- Progress bar -->
    <div id="ajax-progress" class="progress">
        <div id="ajax-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
    </div>

    <!-- Overlay con spinner -->
    <div id="ajax-loader" class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
         style="background: rgba(0,0,0,0.4); z-index: 9998;">
      <div class="text-center">
        <div class="spinner-border text-light" style="width: 3rem; height: 3rem;" role="status"></div>
        <div class="text-light mt-2">Caricamento...</div>
      </div>
    </div>
    
    <script src="/admin_template_child/js/jquery-ui.min.js"></script>
    <script src="/admin_template_child/js/global.js?v=<?php echo filemtime($child_template_root . '/js/global.js'); ?>"></script>
    <script src="/admin_template_child/js/app.js?v=<?php echo filemtime($child_template_root . '/js/app.js'); ?>"></script>
    <script src='/admin_template_child/js/jquery.fileDownload.js'></script>
    <script src='/admin_template_child/js/zxcvbn.js'></script>
    <script src="/admin_template_child/js/masonry.pkgd.min.js"></script>
    <script type="text/javascript">
        $(window).on('beforeunload', function() {
            $('#unloading').css('display', 'block');
        });
    </script>

    <iframe id="hidden_frame" name="hidden_frame" style="height:0;border:0;"></iframe>
   
         

    </body>
</html>
