<header id="admin-header">
    <div class="container px">
        <span class="app-name">SISTEMA</span>
        <nav>
            <a class="nav-link" href="<?php echo BASE_URL . "/business/dashboard" ?>">Dashboard</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/business/empresas" ?>">Empresas</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/business/usuarios" ?>">Usuarios</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/business/documentos" ?>">Documentos</a>
            <a class="nav-link" href="<?php echo BASE_URL . "/business/ajustes" ?>">Ajustes</a>
            <form action="<?php echo BASE_URL ?>/actions/auth_logout.php" method="post">
                <button class="nav-link" type="submit" name="logout">Cerrar sesión</button>
            </form>
        </nav>
    </div>
</header>