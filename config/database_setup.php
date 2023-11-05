<?php 

require './config.php';

function create_table($mydb, $table_name, $sql_file) {
    // Escapar el nombre de la tabla para evitar inyecciones SQL
    $table_name_escaped = $mydb->real_escape_string($table_name);

    // Consulta SQL para verificar si la tabla existe
    $sql = "SHOW TABLES LIKE '$table_name_escaped'";
    $result = $mydb->query($sql);

    // Verificar si hay resutados
    if($result->num_rows == 0) {
        // La tabla no existe, ejecutar el script de creación
        $file = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR .'database' . DIRECTORY_SEPARATOR . $sql_file);
        if($mydb->multi_query($file)) {
            // Procesar el conjunto de resultados
            do {
                // Guardar el conjunto de resultados actual (si existe)
                if ($result = $mydb->store_result()) {
                    $result->free(); // Liberamos el resultado ya que no lo vamos a usar
                }
                // Prepararse para el próximo conjunto de resultados
            } while ($mydb->more_results() && $mydb->next_result());
            
            echo "Tabla $table_name creada y datos insertados correctamente\n";
        } else {
            echo "Error al crear la tabla $table_name: " . $mydb->error . "\n";
        }
    } else {
        // La tabla ya existe, no es necesario crearla de nuevo
        echo "La tabla $table_name ya existe en la base de datos\n";
    }
}

create_table($mydb, 'users', '01.users.sql');
create_table($mydb, 'businesses', '02.businesses.sql');
create_table($mydb, 'roles', '03.roles.sql');
create_table($mydb, 'roles_businesses', '04.roles_businesses.sql');

// Cerrar la conexión
$mydb->close();
