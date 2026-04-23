<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
    <title>Mesa de Partes Virtual UNDC</title>
    <!-- [Meta] -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description"
        content="Able Pro is a trending dashboard template built with the Bootstrap 5 design framework. It is available in multiple technologies, including Bootstrap, React, Vue, CodeIgniter, Angular, .NET, and more.">
    <meta name="keywords"
        content="Bootstrap admin template, Dashboard UI Kit, Dashboard Template, Backend Panel, react dashboard, angular dashboard">
    <meta name="author" content="Phoenixcoded">

    <!-- [Favicon] icon -->
    <link rel="icon" href="../assets/images/sistema/logo-tramite.png" type="image/x-icon"> <!-- [Font] Family -->
    <!-- <link rel="stylesheet" href="../assets/fonts/inter/inter.css" id="main-font-link" /> -->
    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="../assets/fonts/tabler-icons.min.css">
    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="../assets/fonts/feather.css">
    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/fontawesome.css">
    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="../assets/fonts/material.css">
    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="../assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="../assets/css/style-preset.css">
    <link rel="stylesheet" href="../assets/css/plugins/style.css">
    <link rel="stylesheet" href="../assets/css/plugins/stylepropio.css">
    <link rel="stylesheet" href="../assets/css/plugins/dropzone.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css">
    <!-- Incluir el CSS de Quill -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body data-pc-preset="preset-1" data-pc-sidebar-caption="true" data-pc-layout="vertical" data-pc-direction="ltr" data-pc-theme_contrast="" data-pc-theme="light">
    <!-- [ Pre-loader ] start -->
    <div class="loader-bg">
        <div class="loader-track">
            <div class="loader-fill"></div>
        </div>
    </div>
    <!-- [ Pre-loader ] End -->
    <!-- [ Sidebar Menu ] start -->
    <nav class="pc-sidebar">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="dashboard.php" class="b-brand d-flex align-items-center">
                    <!-- Escudo de la institución -->
                    <!-- <img src="../assets/images/logo-tramite.png" class="img-fluid" alt="Escudo" width="30" height="30">
 -->
                    <!-- Logo principal -->
                    <img src="../assets/images/logo-mpv-sin-fondo.png" class="img-fluid logo-lg" alt="Logo" width="auto">
                </a>

            </div>
            <div class="navbar-content">
                <div class="card pc-user-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <img src="../assets/images/user_logo.png" alt="user-image" class="user-avtar wid-45 rounded-circle" />
                            </div>
                            <div class="flex-grow-1 ms-3 me-2">
                                <h6 class="mb-0"><?php echo $_SESSION['nomcompleto'] ?></h6>
                            </div>
                            <a class="btn btn-icon btn-link-secondary avtar" data-bs-toggle="collapse" href="#pc_sidebar_userlink">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-sort-outline"></use>
                                </svg>
                            </a>
                        </div>
                        <div class="collapse pc-user-links" id="pc_sidebar_userlink">
                            <div class="pt-3">
                                <a href="../controladores/usuarios.php?op=salir">
                                    <i class="ti ti-power"></i>
                                    <span>Cerrar Sesion</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <ul class="pc-navbar">
                    <li class="pc-item pc-caption">
                        <label>Navegación</label>
                    </li>
                    <li class="pc-item">
                        <a href="index.php" class="pc-link">
                            <span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-status-up"></use>
                                </svg>
                            </span>
                            <span class="pc-mtext">Dashboard</span>
                        </a>
                    </li>
                    <li class="pc-item pc-caption">
                        <label>Bandeja de Trámites</label>
                    </li>
                    <li class="pc-item">
                        <a href="nuevo_tramite.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-element-plus"></use>
                                </svg> </span><span class="pc-mtext">Nuevo Trámite</span></a>
                    </li>
                    <li class="pc-item">
                        <a href="mis_tramites.php" class="pc-link"><span class="pc-micon">
                                <svg class="pc-icon">
                                    <use xlink:href="#custom-layer"></use>
                                </svg> </span><span class="pc-mtext">Mis trámites</span></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- [ Sidebar Menu ] end --> <!-- [ Header Topbar ] start -->
    <header class="pc-header">
        <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
            <div class="me-auto pc-mob-drp">
                <ul class="list-unstyled">
                    <!-- ======= Menu collapse Icon ===== -->
                    <li class="pc-h-item pc-sidebar-collapse">
                        <a href="#" class="pc-head-link ms-0 bg-white" id="sidebar-hide">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>
                    <li class="pc-h-item pc-sidebar-popup">
                        <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                            <i class="ti ti-menu-2"></i>
                        </a>
                    </li>

                </ul>
            </div>
            <style>
                /* Ocultar en pantallas pequeñas */
                @media (max-width: 576px) {
                    #oficina-nombre {
                        display: none !important;
                    }
                }
            </style>

            <div class="ms-auto d-flex align-items-center justify-content-between">
                <ul class="list-unstyled">

                    <li class="dropdown pc-h-item header-user-profile">
                        <a
                            class="pc-head-link dropdown-toggle arrow-none me-0"
                            data-bs-toggle="dropdown"
                            href="#"
                            role="button"
                            aria-haspopup="false"
                            data-bs-auto-close="outside"
                            aria-expanded="false">
                            <img src="../assets/images/user_logo.png" alt="user-image" class="user-avtar" />
                        </a>
                        <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                            <div class="dropdown-body">
                                <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                                    <div class="d-flex mb-1">
                                        <div class="flex-shrink-0">
                                            <img src="../assets/images/user_logo.png" alt="user-image" class="user-avtar wid-35" />
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1"><?php echo $_SESSION['nomcompleto'] ?></h6>
                                            <span><?php echo $_SESSION["correo"] ?></span>
                                            <span><?php echo $_SESSION["id_car"] ?></span>
                                            <div id="id-usuario" data-usu-id="<?php echo $_SESSION['id_estu'] ?>" style="display: none;"></div>
                                        </div>
                                    </div>
                                    <hr class="border-secondary border-opacity-50" />
                                    <div class="d-grid mb-3">
                                        <a class="btn btn-primary" href="../controladores/usuarios.php?op=salir">
                                            <svg class="pc-icon me-2">
                                                <use xlink:href="#custom-logout-1-outline"></use>
                                            </svg>Cerrar sesión
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- [ Header ] end -->