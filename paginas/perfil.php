<?php
// 1. Iniciar sesión y conectar a la BD
session_start();
include("../conexion/conexion.php");

// 2. Control de acceso
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../autenticacion/login.php");
    exit();
}

$id_usuario = $_SESSION['usuario_id'];

// 3. Consultar datos reales del usuario
$sql = "SELECT nombre_completo, email, pais_residencia, ciudad_residencia, celular FROM usuarios WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$resultado = $stmt->get_result();
$datos = $resultado->fetch_assoc();

// 4. Variables para el HTML
$usuario_nombre = $datos['nombre_completo'];
$usuario_email  = $datos['email'];
$usuario_pais   = $datos['pais_residencia'];
$usuario_ciudad = $datos['ciudad_residencia'];
$usuario_celular_completo = $datos['celular']; // Ejemplo: "+505 35985477"

$ruta_salir = "../autenticacion/logout.php"; // cierre de sesión
$ruta_dashboard="dashboard.php";

// 5. CONSULTAR HISTORIALES
// Historial de Visualización (Últimos 20)
$sql_vistas = "SELECT serie, temporada, episodio, fecha_vista FROM historial_vistas WHERE usuario_id = ? ORDER BY fecha_vista DESC LIMIT 20";
$stmt_v = $conexion->prepare($sql_vistas);
$stmt_v->bind_param("i", $id_usuario);
$stmt_v->execute();
$res_vistas = $stmt_v->get_result();

// Contenido Adquirido (Descargas)
$sql_descargas = "SELECT serie, temporada, episodio, fecha_descarga FROM historial_descargas WHERE usuario_id = ? ORDER BY fecha_descarga DESC";
$stmt_d = $conexion->prepare($sql_descargas);
$stmt_d->bind_param("i", $id_usuario);
$stmt_d->execute();
$res_descargas = $stmt_d->get_result();

// Contenido Adquirido (Temporadas Desbloqueadas)
$sql_temporadas = "SELECT serie, temporada, fecha_compra FROM compras WHERE usuario_id = ? AND tipo_compra = 'temporada' ORDER BY fecha_compra DESC";
$stmt_t = $conexion->prepare($sql_temporadas);
$stmt_t->bind_param("i", $id_usuario);
$stmt_t->execute();
$res_temporadas = $stmt_t->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil | Fuego Dragón</title>
    
    <link rel="icon" type="image/png" href="../activos/img/favicon_fd.png">
    <link rel="stylesheet" href="../activos/css/style.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        /* Estilos para tablas de historial */
        .history-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .history-table th, .history-table td { padding: 12px; text-align: left; border-bottom: 1px solid #333; color: #ccc; }
        .history-table th { color: #a30000; font-weight: bold; }
        .history-table tr:hover { background-color: #222; }
        .date-col { font-size: 0.85em; color: #666; }

        /* Estilos generales */
        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #121212;
            color: #f4f4f4;
            font-family: 'Cinzel', 'Times New Roman', Georgia, serif;
            overflow-y: scroll;
        }
        .main-header {
            width: 100%; 
            box-sizing: border-box;
            background-color: #1a1a1a;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #a30000;
        }
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px;
            text-decoration: none;
        }
        .logo-container img {
            height: 35px;
            width: auto;
        }
        .logo-container h1 {
            color: #ccc;
            margin: 0;
            font-size: 1.5em;
            font-family: 'Cinzel', serif;
            letter-spacing: 1px;
        }
        .user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-greeting {
            font-size: 1em;
            color: #ccc;
        }
        
        .btn-logout {
            background-color: #a30000;
            color: white;
        }
        
        .h1 {
            text-align:center;
        }
        /* CONTENIDO ESPECÍFICO DEL PERFIL */
        .profile-content {
            max-width: 1000px;
            margin: 40px auto;
            padding: 0 20px;
            padding-bottom: 80px; 
        }
        
        /* Secciones del Perfil */
        .profile-section {
            background-color: #1a1a1a;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .section-header h2 {
            color: #a30000;
            font-size: 1.5em;
            margin: 0;
        }

        /* Datos Personales y Contraseña (Contenedor principal en dos columnas) */
        .user-data-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            gap: 15px;
        }
        .data-display {
            display: flex;
            flex-direction: column;
            gap: 15px;
            gap: 10px;
        }
        /* Formulario de edición (oculto por defecto) */
        .data-edit-form {
            display: none; /* Oculto por defecto */
            flex-direction: column;
            gap: 15px;
        }
        .data-edit-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #121212;
            color: white;
            box-sizing: border-box;
        }
        .edit-form-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .btn-save { background-color: #28a745; color: white; }
        .btn-cancel { background-color: #6c757d; color: white; }


        /* Contenedor de cambio de contraseña */
        .change-password-area {
            background-color: #2a2a2a;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #333;
            max-width: 95%;
        }
        .change-password-header {
            padding: 15px;
            background-color: #3a3a3a;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .change-password-header h3 {
            color: #f4f4f4;
            margin: 0;
            font-size: 1.2em;
        }
        .arrow {
            font-size: 1.5em;
            transition: transform 0.3s;
            color: #ccc;
        }
        .change-password-area.active .arrow {
            transform: rotate(90deg);
            color: #a30000;
        }
        
        /* Contenido del Acordeón de Contraseña */
        .change-password-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
            padding: 0 20px;
            padding: 0 15px;
        }
        .change-password-area.active .change-password-content {
            max-height: 1000px;
            padding-top: 20px;
            padding-bottom: 20px;
            padding-top: 15px;
            padding-bottom: 15px;
        }
        .change-password-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #333;
            border-radius: 4px;
            background-color: #121212;
            color: white;
            box-sizing: border-box;
        }
        .btn-update-password {
            background-color: #3f51b5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }

        /* ACORDEÓN GENERAL (Historial, Adquirido, Comentarios) */
        .accordion-block {
            background-color: #1a1a1a;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }
        .accordion-header {
            padding: 20px 25px;
            background-color: #2a2a2a; 
            border-bottom: 1px solid #333;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }
        .accordion-block.active .accordion-content {
            max-height: 500px; 
            padding-top: 25px; 
            padding-bottom: 25px;
        }
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
            padding: 0 25px;
        }

        .accordion-content textarea {
            width: 90%;
            min-height: 120px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            resize: vertical;
        }

        /* --- ESTILOS RESPONSIVE --- */
        @media (max-width: 768px) {
            .main-header {
                flex-direction: column;
                gap: 15px;
                padding: 20px;
                text-align: center;
            }

            .profile-content {
                margin-top: 20px;
                padding: 0 15px 40px 15px;
                padding: 0 10px 40px 10px;
            }
            .profile-content h1 {
                font-size: 1.8em;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .profile-section {
                padding: 15px;
                padding: 10px;
            }

            .user-data-container {
                grid-template-columns: 1fr;
            }

            .accordion-content textarea {
                width: 100%;
                box-sizing: border-box;
            }
        }

        /* --- FOOTER --- */
        footer {
            text-align: center;
            margin-top: 60px;
            padding: 20px;
            font-size: 12px;
            opacity: 0.6;
        }

        /* --- ESTILOS DEL MODAL DE PAGO (Corrección de Scroll) --- */
        .payment-modal {
            display: none; 
            position: fixed; 
            z-index: 9999; 
            left: 0;
            top: 0;
            width: 100%; 
            height: 100%; 
            overflow-y: auto; /* Habilitar scroll vertical */
            background-color: rgba(0,0,0,0.85); 
            backdrop-filter: blur(5px);
        }
        .payment-content {
            background-color: #1a1a1a;
            margin: 50px auto; /* Margen para permitir scroll */
            border: 1px solid #333;
            width: 90%; 
            max-width: 900px;
            border-radius: 8px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .payment-header {
            padding: 15px 20px;
            border-bottom: 1px solid #333;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #222;
            border-radius: 8px 8px 0 0;
        }
        .payment-body { display: flex; flex-wrap: wrap; }
        .payment-methods { width: 30%; background-color: #151515; border-right: 1px solid #333; padding: 20px; box-sizing: border-box; }
        .payment-details { width: 70%; padding: 30px; box-sizing: border-box; }
        .close-modal { color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close-modal:hover { color: white; }
        .method-item { padding: 12px; margin-bottom: 10px; border-radius: 6px; cursor: pointer; display: flex; align-items: center; gap: 10px; color: #888; transition: all 0.2s; }
        .method-item.active { background-color: #2a2a2a; color: white; border-left: 3px solid #0070ba; }
        .method-item.disabled { opacity: 0.5; cursor: not-allowed; }
        
        @media (max-width: 768px) {
            .payment-body { flex-direction: column; }
            .payment-methods, .payment-details { width: 100%; border-right: none; }
            .payment-methods { border-bottom: 1px solid #333; }
            .payment-content { margin: 20px auto; width: 95%; }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Script para manejar el efecto acordeón en las secciones principales
            const mainHeaders = document.querySelectorAll('.accordion-header');
            
            mainHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const block = this.closest('.accordion-block');
                    block.classList.toggle('active');
                });
            });

            // Script para manejar el acordeón de CAMBIAR CONTRASEÑA (interno)
            const passwordHeader = document.querySelector('.change-password-header');
            if (passwordHeader) {
                passwordHeader.addEventListener('click', function() {
                    const block = this.closest('.change-password-area');
                    block.classList.toggle('active');
                });
            }

            // --- LÓGICA PARA EDITAR DATOS DE USUARIO ---
            const btnEdit = document.querySelector('.btn-edit');
            const btnSave = document.querySelector('.btn-save');
            const btnCancel = document.querySelector('.btn-cancel');
            
            const dataDisplay = document.querySelector('.data-display');
            const dataEditForm = document.querySelector('.data-edit-form');

            if (btnEdit && dataDisplay && dataEditForm) {
                // Inputs del formulario
                const inputNombre = document.getElementById('edit-nombre');
                const inputEmail = document.getElementById('edit-email');
                const inputPais = document.getElementById('edit-pais');
                const inputCiudad = document.getElementById('edit-ciudad');
                const inputCelular = document.getElementById('edit-celular');

                // Spans de visualización
                const displayNombre = document.getElementById('display-nombre');
                const displayEmail = document.getElementById('display-email');
                const displayPais = document.getElementById('display-pais');
                const displayCiudad = document.getElementById('display-ciudad');
                const displayCelular = document.getElementById('display-celular');

                // Al hacer clic en "Editar"
                btnEdit.addEventListener('click', () => {
                    dataDisplay.style.display = 'none';
                    dataEditForm.style.display = 'flex';
                    btnEdit.style.display = 'none';
                });

                // Al hacer clic en "Cancelar"
                btnCancel.addEventListener('click', () => {
                    dataDisplay.style.display = 'flex';
                    dataEditForm.style.display = 'none';
                    btnEdit.style.display = 'inline-block';
                });

                // Al hacer clic en "Guardar Cambios" (simulación frontend)
                btnSave.addEventListener('click', () => {
                    // Actualizar los valores en la vista de solo lectura
                    displayNombre.textContent = inputNombre.value;
                    displayEmail.textContent = inputEmail.value;
                    displayPais.textContent = inputPais.value;
                    displayCiudad.textContent = inputCiudad.value;
                    displayCelular.textContent = inputCelular.value;

                    // Volver a la vista de solo lectura
                    dataDisplay.style.display = 'flex';
                    dataEditForm.style.display = 'none';
                    btnEdit.style.display = 'inline-block';
                });
            }
        });

        /**
         * Validación de contraseña segura en el Perfil
         */
        function validarPasswordPerfil(event) {
            const form = event.target;
            const passwordInput = form.querySelector('input[name="new_password"]');
            const password = passwordInput.value;

            // Regex: Mínimo 8 caracteres, al menos una mayúscula y al menos un número
            const passRegex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;
            
            if (!passRegex.test(password)) {
                alert("Por seguridad, la nueva contraseña debe tener:\n- Mínimo 8 caracteres\n- Al menos una mayúscula\n- Al menos un número");
                event.preventDefault(); // Detiene el envío del formulario
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

    <header class="main-header">
        <a href="<?php echo $ruta_dashboard; ?>" class="logo-container">
            <img src="../activos/img/logo-fuegodragon-ok.png" alt="Fuego Dragón Logo">
            <h1>FUEGO DRAGÓN</h1>
        </a>
        <div class="user-actions">
            <span class="user-greeting">Bienvenid@, <?php echo htmlspecialchars($usuario_nombre); ?></span>
            <a href="#" class="btn-profile">Mi Perfil</a> 
            <a href="<?php echo $ruta_salir; ?>" class="btn-logout">Salir</a>
        </div>
    </header>

    <div class="profile-content">
        <a href="<?php echo $ruta_dashboard; ?>" class="btn-back-to-dashboard">
            ← Volver al inicio (Sagas)
        </a>

        <h1 class="h1">MI PERFIL</h1>

        <div class="profile-section">
            <div class="section-header">
                <h2>INFORMACIÓN DE LA CUENTA</h2>
                <button class="btn-edit">Editar Datos</button>
            </div>
            
            <div class="user-data-container">
                <!-- VISTA DE SOLO LECTURA -->
                <div class="data-display" style="display: flex;">
                    <div class="data-item">
                        <span class="data-label">Nombre:</span>
                        <span class="data-value" id="display-nombre"><?php echo htmlspecialchars($usuario_nombre); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Email:</span>
                        <span class="data-value" id="display-email"><?php echo htmlspecialchars($usuario_email); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">País:</span>
                        <span class="data-value" id="display-pais"><?php echo htmlspecialchars($usuario_pais); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Ciudad:</span>
                        <span class="data-value" id="display-ciudad"><?php echo htmlspecialchars($usuario_ciudad); ?></span>
                    </div>
                    
                    <div class="data-item">
                        <span class="data-label">Celular:</span>
                        <span class="data-value" id="display-celular">
                            <?php echo htmlspecialchars($usuario_celular_completo); ?>
                        </span>
                    </div>
                </div>

                <!-- FORMULARIO DE EDICIÓN (OCULTO) -->
                <form action="actualizar_perfil.php" method="post" class="data-edit-form-perfil">
                    <div class="data-edit-form">
                        <input type="text" id="edit-nombre" name="nuevo_nombre" value="<?php echo htmlspecialchars($usuario_nombre); ?>">
                        <input type="email" id="edit-email" name="nuevo_email" value="<?php echo htmlspecialchars($usuario_email); ?>">
                        <input type="text" id="edit-pais" name="nuevo_pais" value="<?php echo htmlspecialchars($usuario_pais); ?>">
                        <input type="text" id="edit-ciudad" name="nueva_ciudad" value="<?php echo htmlspecialchars($usuario_ciudad); ?>">
                        <input type="text" id="edit-celular" name="nuevo_celular" value="<?php echo htmlspecialchars($usuario_celular_completo); ?>">
                        
                        <div class="edit-form-actions">
                            <button type="submit" class="btn-save btn-edit">Guardar Cambios</button>
                            <!--<button class="btn-cancel btn-edit">Cancelar</button> --> <!--se cambia el btn para que este no haga nada-->
                            <button type="button" class="btn-cancel btn-edit">Cancelar</button>
                        </div>
                    </div>
                </form>

                
                <div class="change-password-area">
                    <div class="change-password-header">
                        <h3>Cambiar Contraseña</h3>
                        <span class="arrow">></span>
                    </div>
                    <div class="change-password-content">
                        <form action="actualizar_password.php" method="POST" class="change-password-form" onsubmit="return validarPasswordPerfil(event)">
                            <input type="password" name="current_password" placeholder="Contraseña Actual" required>
                            <input type="password" name="new_password" placeholder="Nueva Contraseña" required>
                            <small style="color: #888; font-size: 0.8em; display:block; margin-bottom:10px;">Mínimo 8 caracteres, una mayúscula y un número.</small>
                            <input type="password" name="confirm_new_password" placeholder="Confirmar Nueva Contraseña" required>
                            <button type="submit" class="btn-update-password">Actualizar Contraseña</button>
                        </form>
                    </div>
                </div>
            
            </div>
        </div>
        
        <div class="accordion-block">
            <div class="accordion-header">
                <h2>HISTORIAL DE VISUALIZACIÓN</h2>
                <span class="arrow">></span>
            </div>
            <div class="accordion-content">
                <?php if ($res_vistas->num_rows > 0): ?>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Serie</th>
                                <th>Episodio</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $res_vistas->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['serie']; ?></td>
                                    <td>T<?php echo $row['temporada']; ?> - E<?php echo $row['episodio']; ?></td>
                                    <td class="date-col"><?php echo date("d/m/Y H:i", strtotime($row['fecha_vista'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="no-data">Aún no has visto ningún episodio.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="accordion-block">
            <div class="accordion-header">
                <h2>CONTENIDO ADQUIRIDO (Descargas y Temporadas)</h2>
                <span class="arrow">></span>
            </div>
            <div class="accordion-content">
                <h3 style="color: #ccc; border-bottom: 1px solid #333; padding-bottom: 5px; margin-top: 10px;">Videos Descargados</h3>
                <?php if ($res_descargas->num_rows > 0): ?>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Serie</th>
                                <th>Episodio</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $res_descargas->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['serie']; ?></td>
                                    <td>T<?php echo $row['temporada']; ?> - E<?php echo $row['episodio']; ?></td>
                                    <td class="date-col"><?php echo date("d/m/Y H:i", strtotime($row['fecha_descarga'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="no-data">No has descargado contenido aún.</p>
                <?php endif; ?>

                <h3 style="color: #ccc; border-bottom: 1px solid #333; padding-bottom: 5px; margin-top: 30px;">Temporadas Desbloqueadas</h3>
                <?php if ($res_temporadas->num_rows > 0): ?>
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th>Serie</th>
                                <th>Temporada</th>
                                <th>Fecha Compra</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $res_temporadas->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['serie']; ?></td>
                                    <td>Temporada <?php echo $row['temporada']; ?></td>
                                    <td class="date-col"><?php echo date("d/m/Y H:i", strtotime($row['fecha_compra'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="no-data">No has desbloqueado temporadas completas aún.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="accordion-block">
            <div class="accordion-header">
                <h2>COMENTARIOS, SUGERENCIAS O REPORTAR UN PROBLEMA</h2>
                <span class="arrow">></span>
            </div>
            <div class="accordion-content">
                <form action="enviar_feedback.php" method="post" class="feedback-form">
                    <textarea name="comentario" rows="5" placeholder="Cuéntanos tu experiencia..." required></textarea>
                    <button type="submit" class="btn-submit">Enviar Feedback</button>
                 </form>
            </div>
        </div>

    </div>

    <!-- MODAL DE PAGO (Copiado para funcionalidad en perfil) -->
    <div id="paymentModal" class="payment-modal">
        <div class="payment-content">
            <div class="payment-header">
                <h2 id="modalTitle">Descargar Video</h2>
                <span class="close-modal" onclick="document.getElementById('paymentModal').style.display='none'">&times;</span>
            </div>
            <div class="payment-body">
                <div class="payment-methods">
                    <div class="method-item active"><i class="fa-brands fa-paypal" style="color:#0070ba;"></i> <span>PayPal</span></div>
                </div>
                <div class="payment-details">
                    <p style="color:#ccc;">Estás a punto de adquirir:</p>
                    <h3 id="productName" style="color:white; margin:10px 0;">Video</h3>
                    <div class="price-display" id="productPrice">$0.00</div>
                    <div id="paypal-button-container" style="width:100%;"></div>
                </div>
            </div>
        </div>
    </div>
    <!--footer-->
    <footer>
        <p>
            © <?php echo date("Y"); ?> Fuego Dragón - Todos los derechos reservados.<br>
            V. 2.2.0
        </p>
    </footer>
    <script src="https://www.paypal.com/sdk/js?client-id=TU_CLIENT_ID_AQUI&currency=USD"></script>
    <script>
        let currentSerie = '';
        let currentSeason = 0;
        let currentEpisode = 0;
        let currentPrice = 0;

        function abrirModalPago(tipo, serie, temporada, episodio, precio) {
            currentSerie = serie;
            currentSeason = temporada;
            currentEpisode = episodio;
            currentPrice = precio;
            
            document.getElementById('productName').innerText = "Descarga: " + serie + " T" + temporada + " E" + episodio;
            document.getElementById('productPrice').innerText = "$" + precio.toFixed(2) + " USD";
            document.getElementById('paymentModal').style.display = 'block';
            document.getElementById('paypal-button-container').innerHTML = '';

            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({ purchase_units: [{ amount: { value: currentPrice.toString() } }] });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        fetch('guardar_compra.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                serie: currentSerie,
                                temporada: currentSeason,
                                episodio: currentEpisode,
                                tipo: 'descarga',
                                monto: currentPrice,
                                orderID: data.orderID
                            })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Error en la respuesta del servidor: ' + response.statusText);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if(data.success) {
                                alert('La compra se realizó satisfactoriamente.');
                                window.location.href = "procesar_descarga.php?serie=" + currentSerie + "&t=" + currentSeason + "&e=" + currentEpisode;
                            } else {
                                alert('Error: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error en el proceso:', error);
                            alert('Hubo un problema al procesar la compra. Revisa la consola (F12) para más detalles.');
                        });
                    });
                },
                onError: function (err) {
                    console.error('PayPal Error:', err);
                    alert('Ocurrió un error con la pasarela de pago de PayPal.');
                }
            }).render('#paypal-button-container');
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('paymentModal');
            if (event.target == modal) modal.style.display = "none";
        }
    </script>

</body>
</html>