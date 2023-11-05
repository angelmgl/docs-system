<header id="admin-header">
    <div class="container px">
        <span class="app-name">SISTEMA</span>
        <nav>
            <a class="nav-link" href="<?php echo BASE_URL . "/admin/dashboard" ?>">Dashboard</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/admin/empresas" ?>">Empresas</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/admin/usuarios" ?>">Usuarios</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/admin/documentos" ?>">Documentos</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/admin/ajustes" ?>">Ajustes</a>
            <form action="<?php echo BASE_URL ?>/actions/auth_logout.php" method="post">
                <button class="nav-link" type="submit" name="logout">Cerrar sesión</button>
            </form>
        </nav>
    </div>
</header>