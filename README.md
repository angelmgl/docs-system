# INSTALACIÓN

## REQUERIMIENTOS

- PHP 7.4 o superior
- MySQL 5.6.5 o superior

## PASOS

- Crea una base de datos y un usuario para manejarla en tu sistema
- Modifica el archivo ``config/config.php`` con el nombre de tu base de datos, nombre de usuario, contraseña y host
- En el mismo archivo actualiza todas las constantes necesarias
- Ingresa en la carpeta ``config`` desde la terminal y corre el comando ``php database_setup.php`` para crear la estructura de la base de datos
- En esta misma carpeta corre el comando ``php create_admin.php`` y sigue los pasos para crear tu primer usuario administrador del sistema.