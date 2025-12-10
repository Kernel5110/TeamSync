<?php

require __DIR__.'/vendor/autoload.php';

// Simple check for the specific string in the file
$content = file_get_contents('/home/kernel/Univercity/Programacion_Web/proyecto_final/resources/views/event.blade.php');

if (strpos($content, 'data-capacidad="{{ $evento->capacidad }}">') !== false && strpos($content, 'data-capacidad="{{ $evento->capacidad }}">
                       data-capacidad="{{ $evento->capacidad }}">') === false) {
    echo "HTML Fix: SUCCESS (Duplicate attribute removed)\n";
} else {
    echo "HTML Fix: FAILURE (Duplicate attribute might still be present)\n";
}

if (strpos($content, 'backdrop-filter: blur(10px)') !== false) {
    echo "Pagination Style: SUCCESS (CSS added)\n";
} else {
    echo "Pagination Style: FAILURE (CSS missing)\n";
}

$indexContent = file_get_contents('/home/kernel/Univercity/Programacion_Web/proyecto_final/resources/views/index.blade.php');
if (strpos($indexContent, '@auth') !== false && strpos($indexContent, 'Ir al Inicio') !== false) {
    echo "Index Logic: SUCCESS (Auth check added)\n";
} else {
    echo "Index Logic: FAILURE (Auth check missing)\n";
}
