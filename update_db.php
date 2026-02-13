<?php
// Script temporal para actualizar la base de datos
// Visita esta URL una vez y luego elimina el archivo

require_once 'backend/config/database.php';

try {
    $db = Database::getInstance();

    echo "<h1>Actualizando Base de Datos - Calendario App</h1>";
    echo "<pre>";

    // Leer y ejecutar el script de actualización
    $sql = file_get_contents('backend/database/update_schema.sql');

    // Dividir por punto y coma, pero mantener las instrucciones multilinea
    $statements = [];
    $currentStatement = '';
    $inQuotes = false;
    $quoteChar = '';

    for ($i = 0; $i < strlen($sql); $i++) {
        $char = $sql[$i];

        if (!$inQuotes && ($char === '"' || $char === "'")) {
            $inQuotes = true;
            $quoteChar = $char;
        } elseif ($inQuotes && $char === $quoteChar && $sql[$i-1] !== '\\') {
            $inQuotes = false;
        }

        if (!$inQuotes && $char === ';') {
            $currentStatement = trim($currentStatement);
            if (!empty($currentStatement) && !preg_match('/^--/', $currentStatement)) {
                $statements[] = $currentStatement;
            }
            $currentStatement = '';
        } else {
            $currentStatement .= $char;
        }
    }

    // Ejecutar cada statement
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                echo "Ejecutando: " . substr($statement, 0, 50) . "...\n";
                $db->exec($statement);
                echo "✓ OK\n";
            } catch (Exception $e) {
                echo "✗ Error: " . $e->getMessage() . "\n";
            }
        }
    }

    echo "\n</pre>";
    echo "<h2 style='color: green;'>¡Base de datos actualizada exitosamente!</h2>";
    echo "<p>Ahora puedes:</p>";
    echo "<ul>";
    echo "<li>Crear eventos con asistentes seleccionados de la lista de usuarios</li>";
    echo "<li>Ver notificaciones para eventos próximos</li>";
    echo "<li>Usar todas las nuevas funcionalidades</li>";
    echo "</ul>";
    echo "<p><strong>Importante:</strong> Elimina este archivo (update_db.php) después de usarlo por seguridad.</p>";

} catch (Exception $e) {
    echo "<h2 style='color: red;'>Error actualizando la base de datos:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>