<header class="content" id="admin-header">
    <div class="container px">
        <img class="app-logo" src="<?php echo BASE_URL ?>/assets/img/logo.svg" alt="Grupo Delta" />
        <nav>
            <a class="nav-link" href="<?php echo BASE_URL . "/admin/dashboard" ?>">Dashboard</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/admin/empresas" ?>">Empresas</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/admin/usuarios" ?>">Usuarios</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/admin/contenido" ?>">Contenido</a>
            <div class="profile-btn" style="background-image: url(<?php echo $_SESSION['profile_picture'] ? BASE_URL . $_SESSION['profile_picture'] : BASE_URL . '/assets/img/avatar.webp'; ?>)">
                <div class="profile-nav">
                    <a class="profile-link" href="<?php echo BASE_URL . "/perfil" ?>">Mi perfil</a>
                    <form action="<?php echo BASE_URL ?>/actions/auth_logout.php" method="post">
                        <button class="profile-link" type="submit" name="logout">Cerrar sesión</button>
                    </form>
                </div>
            </div>
        </nav>
    </div>
</header>

<script src="<?php echo BASE_URL ?>/assets/js/header.js"></script>