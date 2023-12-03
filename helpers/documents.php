<?php 

function formatFileSize($bytes) {
    // Convertir a KB
    $kb = $bytes / 1024;
    // Si es menor que 1 MB, devolver en KB
    if ($kb < 1024) {
        return round($kb, 1) . " KB";
    } else {
        // Convertir a MB y devolver
        $mb = $kb / 1024;
        return round($mb, 1) . " MB";
    }
}