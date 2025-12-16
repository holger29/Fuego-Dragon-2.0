<?php
    $ruta_login = "login.php";
    $ruta_regresar_home = "../LandingPage.php";
    //$ruta_dashboard = "../paginas/dashboard.php";

    // --- LISTA EXTENDIDA DE PAÍSES Y SUS PREFIJOS TELEFÓNICOS (MÁS ROBUSTA) ---
    // Claves: País en minúsculas y sin acentos (para la normalización en JS)
    $prefijos = [
        "afghanistan" => "+93",
        "alemania" => "+49",
        "arabia saudita" => "+966",
        "argentina" => "+54",
        "australia" => "+61",
        "austria" => "+43",
        "belgica" => "+32",
        "bolivia" => "+591",
        "brasil" => "+55",
        "canada" => "+1",
        "chile" => "+56",
        "china" => "+86",
        "colombia" => "+57",
        "corea del sur" => "+82",
        "costa rica" => "+506",
        "cuba" => "+53",
        "dinamarca" => "+45",
        "ecuador" => "+593",
        "egipto" => "+20",
        "el salvador" => "+503",
        "emiratos arabes unidos" => "+971",
        "espana" => "+34",
        "estados unidos" => "+1",
        "filipinas" => "+63",
        "francia" => "+33",
        "guatemala" => "+502",
        "honduras" => "+504",
        "hong kong" => "+852",
        "india" => "+91",
        "indonesia" => "+62",
        "irlanda" => "+353",
        "israel" => "+972",
        "italia" => "+39",
        "japon" => "+81",
        "malasia" => "+60",
        "mexico" => "+52",
        "nicaragua" => "+505",
        "noruega" => "+47",
        "nueva zelanda" => "+64",
        "panama" => "+507",
        "paraguay" => "+595",
        "peru" => "+51",
        "portugal" => "+351",
        "puerto rico" => "+1", // Se usa el prefijo de EE. UU.
        "reino unido" => "+44",
        "republica dominicana" => "+1", // Se usa el prefijo de EE. UU.
        "rusia" => "+7",
        "singapur" => "+65",
        "sudafrica" => "+27",
        "suecia" => "+46",
        "suiza" => "+41",
        "tailandia" => "+66",
        "taiwan" => "+886",
        "turquia" => "+90",
        "ucrania" => "+380",
        "uruguay" => "+598",
        "venezuela" => "+58",
        // Puedes seguir añadiendo más países aquí
    ];
    $prefijos_json = json_encode($prefijos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario | Fuego Dragón</title>
    
    <link rel="icon" type="image/png" href="../activos/img/favicon_fd.png">
    <link rel="stylesheet" href="../activos/css/style.css"> 
    
    <style>
        /* [ ... Estilos CSS anteriores sin cambios ... ] */

        body {
            background-color: #121417; 
            color: #f4f4f4;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow-y: auto;
        }

        .auth-container {
            max-width: 400px;
            width: 90%;
            padding: 30px;
            background-color: #1a1a1a; 
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
            text-align: center;
            box-sizing: border-box;
            margin: 20px 0;
        }

        h1 {
            color: #a30000;
            font-size: 2em;
            margin-bottom: 25px;
            border-bottom: 2px solid #333; 
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #ccc;
            font-size: 0.9em;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #2b2b2b;
            color: white;
            box-sizing: border-box;
        }
        
        .celular-input-group {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        #prefijo_celular {
            width: 70px;
            padding: 12px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #2b2b2b;
            color: #f4f4f4;
            box-sizing: border-box;
            text-align: center;
            pointer-events: none; 
            user-select: none;
            font-weight: bold;
        }

        #celular_numero {
            flex-grow: 1;
            padding: 12px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #2b2b2b;
            color: white;
            box-sizing: border-box;
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 4px;
            background-color: #a30000; 
            color: white;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
        }
        .btn-register:hover {
            background-color: #880000;
        }

        .link-group {
            margin-top: 20px;
            font-size: 0.9em;
        }
        .link-group a {
            color: gray;
            text-decoration: none;
            transition: color 0.3s;
        }
        .link-group a:hover {
            color: #a30000;
        }
        .link-group .iniciar_sesion {
            font-weight: bold;
            color: rgb(2, 139, 64);
        }
     
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            text-align: center;
        }
        .message.error {
            background-color: #dc3545;
            color: white;
        }
        .message.success {
            background-color: #28a745;
            color: white;
        }

        @media (max-width: 480px) {
            .auth-container {
                padding: 25px 20px;
            }
            h1 {
                font-size: 1.5em;
            }
            #prefijo_celular {
                width: 60px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <h1>REGISTRO DE NUEVO USUARIO</h1>

        <form action="<?php echo $ruta_login; ?>" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="pais">País de Residencia:</label>
                <input type="text" id="pais" name="pais" oninput="actualizarPrefijoCelular()" required>
            </div>
            
            <div class="form-group">
                <label for="ciudad">Ciudad de Residencia:</label>
                <input type="text" id="ciudad" name="ciudad" required>
            </div>
            
            <div class="form-group">
                <label for="celular_numero">Celular:</label>
                <div class="celular-input-group">
                    <input type="text" id="prefijo_celular" value="" readonly title="Prefijo de país" name="prefijo_celular_display">
                    <input type="text" id="celular_numero" name="celular" placeholder="Número de celular" required>
                </div>
            </div>

            <button type="submit" class="btn-register">
                Registrar Usuario
            </button>
        </form>
        
        <div class="link-group">
            <a href="<?php echo $ruta_login; ?>">¿Ya tienes cuenta? <span class="iniciar_sesion">Inicia sesión aquí.</span></a>
            <br>
            <a href="<?php echo $ruta_regresar_home; ?>">← Volver a la página principal</a>
        </div>
    </div>

    <script>
        // Mapeo de prefijos PHP a JavaScript (Ahora con la lista extendida)
        const prefijosPais = <?php echo $prefijos_json; ?>;

        /**
         * Normaliza el nombre del país (minúsculas, sin acentos) para la búsqueda.
         * @param {string} pais - El nombre del país introducido.
         * @returns {string} - El nombre del país normalizado.
         */
        function normalizarPais(pais) {
            // Utilizamos el método trim() para eliminar espacios en blanco al inicio y final.
            return pais.trim()
                       .toLowerCase()
                       .normalize("NFD")
                       .replace(/[\u0300-\u036f]/g, ""); // Elimina acentos y diacríticos
        }

        /**
         * Actualiza el campo de prefijo de celular basado en el país introducido.
         */
        function actualizarPrefijoCelular() {
            const inputPais = document.getElementById('pais');
            const inputPrefijo = document.getElementById('prefijo_celular');
            const pais = inputPais.value.trim();
            const paisNormalizado = normalizarPais(pais);

            // Busca el prefijo en el objeto mapeado
            const prefijoEncontrado = prefijosPais[paisNormalizado];

            if (prefijoEncontrado) {
                inputPrefijo.value = prefijoEncontrado;
            } else {
                // Si no se encuentra, se asume que aún no ha terminado de escribir o el país no está en la lista.
                // Se puede dejar vacío o con un marcador.
                inputPrefijo.value = '+?'; // Marcador visual para indicar que falta el prefijo o el país
            }
        }

        // Ejecutar al cargar la página para inicializar el prefijo si el campo de país ya tiene valor (aunque es poco probable en un registro nuevo)
        document.addEventListener('DOMContentLoaded', actualizarPrefijoCelular);
    </script>
</body>
</html>