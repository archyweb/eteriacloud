<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">

    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- ===============================================-->
        <!--    Document Title-->
        <!-- ===============================================-->
        <title>Eteria Cloud | Login</title>

        <!-- ===============================================-->
        <!--    Favicons-->
        <!-- ===============================================-->
        <link rel="apple-touch-icon" sizes="180x180" href="/admin_template/assets/images/favicons/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/admin_template/assets/images/favicons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/admin_template/assets/images/favicons/favicon-16x16.png">
        <link rel="shortcut icon" type="image/x-icon" href="/admin_template/assets/images/favicons/favicon.ico">
        <link rel="manifest" href="/admin_template/assets/images/favicons/manifest.json">
        <meta name="msapplication-TileImage" content="/admin_template/assets/images/favicons/mstile-150x150.png">
        <meta name="theme-color" content="#ffffff">
        <script src="/admin_template_child/js/config.js"></script>
        <script src="/admin_template/vendors/simplebar/simplebar.min.js"></script>
        
        <!-- ===============================================-->
        <!--    Stylesheets-->
        <!-- ===============================================-->
        <link href="/admin_template/vendors/prism/prism-okaidia.css" rel="stylesheet">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
        <link href="/admin_template/vendors/simplebar/simplebar.min.css" rel="stylesheet">
        <link href="/admin_template/assets/css/theme-rtl.css" rel="stylesheet" id="style-rtl">
        <link href="/admin_template/assets/css/theme.css" rel="stylesheet" id="style-default">
        <link href="/admin_template/assets/css/user-rtl.css" rel="stylesheet" id="user-style-rtl">
        <link href="/admin_template/assets/css/user.css" rel="stylesheet" id="user-style-default">
        <script>
            var isRTL = JSON.parse(localStorage.getItem('isRTL'));
            if (isRTL) {
              var linkDefault = document.getElementById('style-default');
              var userLinkDefault = document.getElementById('user-style-default');
              linkDefault.setAttribute('disabled', true);
              userLinkDefault.setAttribute('disabled', true);
              document.querySelector('html').setAttribute('dir', 'rtl');
            } else {
              var linkRTL = document.getElementById('style-rtl');
              var userLinkRTL = document.getElementById('user-style-rtl');
              linkRTL.setAttribute('disabled', true);
              userLinkRTL.setAttribute('disabled', true);
            }
        </script>
    </head>
    <body> 
        
        <!-- ===============================================-->
        <!--    Main Content-->
        <!-- ===============================================-->
        <main class="main" id="top">
      <div class="container-fluid">
        <script>
          var isFluid = JSON.parse(localStorage.getItem('isFluid'));
          if (isFluid) {
            var container = document.querySelector('[data-layout]');
            container.classList.remove('container');
            container.classList.add('container-fluid');
          }
        </script>
        <div class="row min-vh-100 bg-100">
          <div class="col-6 d-none d-lg-block position-relative">
            <div class="bg-holder" style="background-image:url(/admin_template/assets/img/generic/18.jpg);">
            </div>
            <!--/.bg-holder-->

          </div>
          <div class="col-sm-10 col-md-6 px-sm-0 align-self-center mx-auto py-5">
            <div class="row justify-content-center g-0">
              <div class="col-lg-9 col-xl-8 col-xxl-6">
                <div class="card">
                  <div class="card-header bg-circle-shape bg-shape text-center p-2">
                      <a class="font-sans-serif fw-bolder fs-5 z-1 position-relative link-light" href="#" data-bs-theme="light"><img src="/assets/images/logo.png"> Eteria Cloud</a>
                  </div>
                  <div class="card-body p-4">
                    <div class="row justify-content-center">
                      <div class="col-auto">
                        <div class="d-flex align-items-center">
                          
                          <div class="flex-1">
                            <h4>Stiamo sistemando alcune cose</h4>
                            <p class="mb-0">Ci scusiamo per l'inconveniente ma il software è attualmente sottoposto a manutenzione programmata</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
        <!-- ===============================================-->
        <!--    End of Main Content-->
        <!-- ===============================================-->
        
        <!-- ===============================================-->
        <!--    JavaScripts-->
        <!-- ===============================================-->
        <script src="/admin_template/vendors/popper/popper.min.js"></script>
        <script src="/admin_template/vendors/bootstrap/bootstrap.min.js"></script>
        <script src="/admin_template/vendors/anchorjs/anchor.min.js"></script>
        <script src="/admin_template/vendors/is/is.min.js"></script>
        <script src="/admin_template/vendors/echarts/echarts.min.js"></script>
        <script src="/admin_template/vendors/prism/prism.js"></script>
        <script src="/admin_template/vendors/fontawesome/all.min.js"></script>
        <script src="/admin_template/vendors/lodash/lodash.min.js"></script>
        <script src="/admin_template/vendors/list.js/list.min.js"></script>
        <script src="/admin_template/assets/js/theme.js"></script>
        
        
    </body>

</html>