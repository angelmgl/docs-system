<?php



?>
<header id="admin-header">
    <div class="container px">
        <span class="app-name">SISTEMA</span>
        <nav>
            <a class="nav-link" href="<?php echo BASE_URL . "/business/dashboard" ?>">Dashboard</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/business/empresas" ?>">Empresas</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/business/usuarios" ?>">Usuarios</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/business/documentos" ?>">Documentos</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/business/ajustes" ?>">Ajustes</a>
            <div 
                class="profile-btn" 
                style="background-image: url(<?php echo $_SESSION['profile_picture'] ? BASE_URL . $_SESSION['profile_picture'] : BASE_URL . '/assets/img/avatar.webp'; ?>)"
            >
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