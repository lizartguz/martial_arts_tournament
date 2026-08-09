<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Diagnóstico de Tema CSS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .warning { color: orange; font-weight: bold; }
        pre {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <h1>🔍 Diagnóstico del Sistema de Temas CSS</h1>
    
    <div class="card">
        <h3>1. Verificación de Archivos CSS</h3>
        <?php
        $publicPath = __DIR__;
        $themesPath = $publicPath . '/css/theme/themes.css';
        $customPath = $publicPath . '/css/custom.css';
        
        echo "<p><strong>Ruta pública:</strong> $publicPath</p>";
        
        if (file_exists($themesPath)) {
            echo "<p class='success'>✅ themes.css EXISTE</p>";
            echo "<p>Tamaño: " . number_format(filesize($themesPath)) . " bytes</p>";
            echo "<p>Última modificación: " . date('Y-m-d H:i:s', filemtime($themesPath)) . "</p>";
        } else {
            echo "<p class='error'>❌ themes.css NO ENCONTRADO</p>";
            echo "<p>Ruta buscada: $themesPath</p>";
        }
        
        if (file_exists($customPath)) {
            echo "<p class='success'>✅ custom.css EXISTE</p>";
        } else {
            echo "<p class='error'>❌ custom.css NO ENCONTRADO</p>";
        }
        ?>
    </div>

    <div class="card">
        <h3>2. URLs de Acceso</h3>
        <p>Verifica que estos enlaces funcionen (deben mostrar el contenido CSS):</p>
        <ul>
            <li><a href="/css/theme/themes.css" target="_blank">/css/theme/themes.css</a></li>
            <li><a href="/css/custom.css" target="_blank">/css/custom.css</a></li>
        </ul>
    </div>

    <div class="card">
        <h3>3. Contenido de Variables CSS</h3>
        <?php
        if (file_exists($themesPath)) {
            $content = file_get_contents($themesPath);
            
            // Buscar variables
            if (strpos($content, '--theme-primary:') !== false) {
                echo "<p class='success'>✅ Variables CSS encontradas</p>";
                
                // Extraer valores
                preg_match('/--theme-primary:\s*([^;]+);/', $content, $matches);
                if (!empty($matches[1])) {
                    echo "<p>Color principal: <code>" . trim($matches[1]) . "</code></p>";
                }
            } else {
                echo "<p class='error'>❌ Variables CSS NO encontradas</p>";
            }
            
            // Buscar estilos de tabla
            if (strpos($content, '.themed-thead') !== false) {
                echo "<p class='success'>✅ Estilos .themed-thead encontrados</p>";
            } else {
                echo "<p class='error'>❌ Estilos .themed-thead NO encontrados</p>";
            }
        }
        ?>
    </div>

    <div class="card">
        <h3>4. Configuración de AdminLTE</h3>
        <?php
        $configPath = dirname($publicPath) . '/config/adminlte.php';
        if (file_exists($configPath)) {
            echo "<p class='success'>✅ adminlte.php EXISTE</p>";
            $configContent = file_get_contents($configPath);
            if (strpos($configContent, 'themes.css') !== false) {
                echo "<p class='success'>✅ themes.css está registrado en adminlte.php</p>";
            } else {
                echo "<p class='error'>❌ themes.css NO está registrado en adminlte.php</p>";
            }
        } else {
            echo "<p class='warning'>⚠️ No se pudo verificar adminlte.php</p>";
        }
        ?>
    </div>

    <div class="card">
        <h3>5. Comandos de Limpieza Recomendados</h3>
        <pre>php artisan config:clear
php artisan cache:clear
php artisan view:clear</pre>
    </div>

    <div class="card">
        <h3>6. Próximos Pasos</h3>
        <ol>
            <li>Verifica que todos los enlaces de arriba funcionen</li>
            <li>Abre <a href="/test-table-theme.html">test-table-theme.html</a> para ver una tabla de prueba</li>
            <li>Si funciona allí pero no en Livewire, el problema es de caché del navegador</li>
            <li>Presiona <kbd>Ctrl + Shift + R</kbd> en las páginas de Livewire</li>
        </ol>
    </div>
</body>
</html>

