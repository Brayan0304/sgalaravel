<?php
return [
    'paths' => ['api/*'],  // Rutas que necesitan CORS
    'allowed_methods' => ['*'],  // Permitir todos los métodos o personalizar
    'allowed_origins' => ['http://localhost:3000'],  // URL del frontend React
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,  // Para cookies o autenticación
];