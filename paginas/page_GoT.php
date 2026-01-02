<?php
session_start();
include("../conexion/conexion.php");

// Verificar sesión
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../autenticacion/login.php");
    exit();
}

$ruta_dashboard = "dashboard.php"; 
$ruta_perfil = "perfil.php";
$ruta_logout = "../autenticacion/logout.php"; 

$usuario_nombre = $_SESSION['usuario_nombre'];
$titulo_serie = "GAME OF THRONES";
$sinopsis = "Nueve familias nobles luchan por el control de las tierras de Westeros, mientras un antiguo enemigo 
    regresa después de estar inactivo durante milenios.";

$temporadas = [
    1 => [
        'titulo' => 'Temporada 1',
        'episodios' => [
            ['titulo' => 'Se acerca el invierno', 'resumen' => 'El Rey Robert Baratheon viaja al Norte para ofrecerle a Ned Stark el puesto de Mano del Rey tras la muerte de Jon Arryn.'],
            ['titulo' => 'El Camino Real', 'resumen' => 'Ned Stark y sus hijas parten hacia Desembarco del Rey. Jon Snow se dirige al Muro para unirse a la Guardia de la Noche.'],
            ['titulo' => 'Lord Nieve', 'resumen' => 'Jon comienza su entrenamiento en el Muro. Ned llega a Desembarco del Rey y descubre los problemas financieros de la corona.'],
            ['titulo' => 'Tullidos, bastardos y cosas rotas', 'resumen' => 'Ned investiga la muerte de su predecesor. Catelyn Stark sospecha de los Lannister y toma medidas drásticas.'],
            ['titulo' => 'El lobo y el león', 'resumen' => 'Catelyn captura a Tyrion Lannister. Ned se enfrenta a Jaime Lannister en las calles de la capital tras enterarse del arresto.'],
            ['titulo' => 'Una corona de oro', 'resumen' => 'Viserys Targaryen exige su pago a Khal Drogo. Ned gobierna desde el Trono de Hierro mientras el Rey caza.'],
            ['titulo' => 'Ganas o mueres', 'resumen' => 'Ned confronta a Cersei sobre el secreto de sus hijos. Jon Snow toma sus votos como Hermano de la Guardia de la Noche.'],
            ['titulo' => 'Por el lado de la punta', 'resumen' => 'Los Lannister toman el control tras un accidente del Rey. Arya debe escapar del castillo para sobrevivir.'],
            ['titulo' => 'Baelor', 'resumen' => 'Ned toma una decisión fatídica en un intento por salvar a sus hijas. Robb Stark captura a un prisionero valioso.'],
            ['titulo' => 'Fuego y sangre', 'resumen' => 'La noticia de la ejecución de Ned se esparce por los Siete Reinos. Daenerys paga un precio terrible y nacen los dragones.'],
        ],
    ],
    2 => [
        'titulo' => 'Temporada 2 ',
        'episodios' => [
            ['titulo' => 'El Norte no olvida', 'resumen' => 'Tyrion llega para salvar la corona de Joffrey. Daenerys busca agua y aliados en el Desierto Rojo.'],
            ['titulo' => 'Las tierras de la noche', 'resumen' => 'Arya comparte un secreto con Gendry. Tyrion expulsa a Janos Slynt. Theon Greyjoy regresa a las Islas del Hierro.'],
            ['titulo' => 'Lo que está muerto no puede morir', 'resumen' => 'Catelyn Stark trata de forjar una alianza con Renly Baratheon. Tyrion desenmascara a un espía.'],
            ['titulo' => 'Jardín de huesos', 'resumen' => 'Joffrey castiga a Sansa por las victorias de Robb. Catelyn suplica a Stannis y Renly que se unan contra los Lannister.'],
            ['titulo' => 'El fantasma de Harrenhal', 'resumen' => 'La rivalidad entre los Baratheon termina. Tyrion descubre el arma secreta de Cersei.'],
            ['titulo' => 'Los dioses antiguos y nuevos', 'resumen' => 'Theon toma Invernalia. Myrcella es enviada fuera de Desembarco del Rey. Jon Nieve conoce a Ygritte.'],
            ['titulo' => 'Un hombre sin honor', 'resumen' => 'Jaime conoce a un pariente lejano. Theon va a la caza de los chicos Stark desaparecidos.'],
            ['titulo' => 'El príncipe de Invernalia', 'resumen' => 'Arya planea su escape de Harrenhal. Tyrion y Varys se preparan para el asedio de Stannis.'],
            ['titulo' => 'Aguasnegras', 'resumen' => 'La flota de Stannis ataca Desembarco del Rey. Tyrion lidera la defensa en una batalla épica.'],
            ['titulo' => 'Valar Morghulis', 'resumen' => 'Joffrey premia a sus súbditos. Theon incita a sus hombres a la acción. Daenerys va a la Casa de los Eternos.'],
        ],
    ],
    3 => [
        'titulo' => 'Temporada 3 ',
        'episodios' => [
            ['titulo' => 'Valar Dohaeris', 'resumen' => 'Jon es llevado ante Mance Rayder. Daenerys llega a la Bahía de los Esclavos.'],
            ['titulo' => 'Alas negras, palabras negras', 'resumen' => 'Bran encuentra nuevos aliados. Sansa habla con Olenna Tyrell sobre Joffrey.'],
            ['titulo' => 'El camino del castigo', 'resumen' => 'Jaime hace un trato con sus captores. Tyrion asume nuevas responsabilidades como Consejero de la Moneda.'],
            ['titulo' => 'Y ahora su guardia ha terminado', 'resumen' => 'La Guardia de la Noche se amotina en el Torreón de Craster. Daenerys intercambia una cadena por un ejército.'],
            ['titulo' => 'Besado por el fuego', 'resumen' => 'El Perro es juzgado por combate. Jon y Ygritte intiman en una cueva. Jaime se confiesa ante Brienne.'],
            ['titulo' => 'El ascenso', 'resumen' => 'Jon y los salvajes escalan el Muro. Melisandre visita las Tierras de los Ríos. Ros descubre el secreto de Meñique.'],
            ['titulo' => 'El oso y la doncella', 'resumen' => 'Daenerys intercambia regalos con un señor de esclavos. Brienne se enfrenta a un oso en Harrenhal.'],
            ['titulo' => 'Los segundos hijos', 'resumen' => 'La boda de Tyrion y Sansa se lleva a cabo. Daenerys conoce a los Segundos Hijos.'],
            ['titulo' => 'Las lluvias de Castamere', 'resumen' => 'Robb se presenta ante Walder Frey. Edmure Tully conoce a su novia. Ocurre la Boda Roja.'],
            ['titulo' => 'Mhysa', 'resumen' => 'Joffrey desafía a Tywin. Bran cuenta una historia de fantasmas. Daenerys es aclamada como "Mhysa" (Madre).'],
        ],
    ],
    4 => [
        'titulo' => 'Temporada 4 ',
        'episodios' => [
            ['titulo' => 'Dos espadas', 'resumen' => 'Tyrion recibe a un invitado en Desembarco del Rey. Jon Nieve advierte sobre la amenaza salvaje.'],
            ['titulo' => 'El león y la rosa', 'resumen' => 'La boda de Joffrey y Margaery se celebra. Ocurre un evento impactante durante el banquete.'],
            ['titulo' => 'Rompedora de cadenas', 'resumen' => 'Tyrion es arrestado. Sansa escapa de la ciudad. Daenerys asedia Meereen.'],
            ['titulo' => 'Guardajuramentos', 'resumen' => 'Jaime encarga una misión a Brienne. Jon recluta voluntarios para ir al Torreón de Craster.'],
            ['titulo' => 'El primero de su nombre', 'resumen' => 'Tommen es coronado Rey. Cersei y Tywin planean el próximo movimiento de la Corona.'],
            ['titulo' => 'Leyes de dioses y hombres', 'resumen' => 'Stannis y Davos buscan financiación en Braavos. Tyrion es juzgado por regicidio.'],
            ['titulo' => 'Sinsonte', 'resumen' => 'Tyrion busca un campeón. Petyr Baelish besa a Sansa, provocando la ira de Lysa Arryn.'],
            ['titulo' => 'La montaña y la víbora', 'resumen' => 'El destino de Tyrion se decide en un combate singular entre Oberyn Martell y La Montaña.'],
            ['titulo' => 'Los vigilantes del Muro', 'resumen' => 'La Guardia de la Noche defiende el Muro contra el ejército de Mance Rayder.'],
            ['titulo' => 'Los niños', 'resumen' => 'Las circunstancias cambian tras una llegada inesperada al norte del Muro. Tyrion confronta a su padre.'],
        ],
    ],
    5 => [
        'titulo' => 'Temporada 5 ',
        'episodios' => [
            ['titulo' => 'Las guerras venideras', 'resumen' => 'Cersei y Jaime se adaptan a un mundo sin Tywin. Varys revela una conspiración a Tyrion.'],
            ['titulo' => 'La casa de negro y blanco', 'resumen' => 'Arya llega a Braavos. Podrick y Brienne se encuentran con problemas en el camino.'],
            ['titulo' => 'Gorrión Supremo', 'resumen' => 'En Desembarco del Rey, la reina Margaery disfruta de su nuevo marido. Tyrion y Varys caminan por el Puente Largo de Volantis.'],
            ['titulo' => 'Hijos de la Arpía', 'resumen' => 'La Fe Militante se vuelve más agresiva. Jaime y Bronn llegan a Dorne.'],
            ['titulo' => 'Matad al chico', 'resumen' => 'Daenerys toma una decisión difícil en Meereen. Jon busca la ayuda de un aliado inesperado.'],
            ['titulo' => 'Nunca doblegado, nunca roto', 'resumen' => 'Arya entrena. Jorah y Tyrion se encuentran con esclavistas. Trystane y Myrcella hacen planes.'],
            ['titulo' => 'El regalo', 'resumen' => 'Jon se prepara para el conflicto. Sansa trata de hablar con Theon. Brienne espera una señal.'],
            ['titulo' => 'Casa Austera', 'resumen' => 'Jon viaja a Casa Austera. Cersei lucha contra la Fe. Tyrion aconseja a Daenerys.'],
            ['titulo' => 'Danza de dragones', 'resumen' => 'Stannis toma una decisión difícil. Daenerys supervisa una celebración en la arena de combate.'],
            ['titulo' => 'Misericordia', 'resumen' => 'Stannis marcha. Daenerys está rodeada de extraños. Cersei busca el perdón. Jon es traicionado.'],
        ],
    ],
    6 => [
        'titulo' => 'Temporada 6 ',
        'episodios' => [
            ['titulo' => 'La mujer roja', 'resumen' => 'Jon Nieve yace muerto. Daenerys conoce a un hombre fuerte. Cersei ve a su hija de nuevo.'],
            ['titulo' => 'A casa', 'resumen' => 'Bran entrena con el Cuervo de Tres Ojos. En Desembarco del Rey, Jaime aconseja a Tommen. Tyrion exige buenas noticias.'],
            ['titulo' => 'Perjuro', 'resumen' => 'Daenerys llega a Vaes Dothrak. Sam y Gilly viajan en barco. Arya entrena como Nadie.'],
            ['titulo' => 'Libro del desconocido', 'resumen' => 'Tyrion llega a un acuerdo. Jorah y Daario emprenden una tarea difícil. Jaime y Cersei intentan mejorar su situación.'],
            ['titulo' => 'El portón', 'resumen' => 'Tyrion busca un extraño aliado. Bran descubre un gran secreto. Brienne va en misión. Hodor sostiene la puerta.'],
            ['titulo' => 'Sangre de mi sangre', 'resumen' => 'Un viejo enemigo regresa. Gilly conoce a la familia de Sam. Arya se enfrenta a una decisión difícil.'],
            ['titulo' => 'El hombre destrozado', 'resumen' => 'El Gorrión Supremo tiene un nuevo objetivo. Jaime se enfrenta a un héroe. Arya hace un plan.'],
            ['titulo' => 'Nadie', 'resumen' => 'Mientras Jaime sopesa sus opciones, Cersei responde a una petición. Los planes de Tyrion dan fruto. Arya se enfrenta a una prueba.'],
            ['titulo' => 'La batalla de los bastardos', 'resumen' => 'Jon y Sansa se enfrentan a Ramsay Bolton en el campo de batalla. Daenerys contraataca a sus enemigos.'],
            ['titulo' => 'Vientos de invierno', 'resumen' => 'Cersei y Loras Tyrell son juzgados por los dioses. Daenerys se prepara para zarpar hacia Poniente.'],
        ],
    ],
    7 => [
        'titulo' => 'Temporada 7',
        'episodios' => [
            ['titulo' => 'Rocadragón', 'resumen' => 'Jon organiza la defensa del Norte. Cersei intenta igualar sus probabilidades. Daenerys llega a casa.'],
            ['titulo' => 'Nacida de la tormenta', 'resumen' => 'Daenerys recibe una visita inesperada. Jon se enfrenta a una revuelta. Tyrion planea la conquista de Poniente.'],
            ['titulo' => 'La justicia de la reina', 'resumen' => 'Daenerys es el centro de atención. Cersei devuelve un regalo. Jaime aprende de sus errores.'],
            ['titulo' => 'Botines de guerra', 'resumen' => 'Los Lannister pagan sus deudas. Daenerys sopesa sus opciones. Arya llega a casa.'],
            ['titulo' => 'Guardaoriente', 'resumen' => 'Daenerys ofrece una elección. Tyrion busca una solución extraña. Jon va más allá del Muro.'],
            ['titulo' => 'Más allá del Muro', 'resumen' => 'Jon y su equipo cazan a un muerto. Arya se enfrenta a Sansa. Daenerys toma una decisión impulsiva.'],
            ['titulo' => 'El dragón y el lobo', 'resumen' => 'Tyrion intenta salvar a sus hijos. Cersei y Daenerys se reúnen. Jon y Daenerys intiman. El Muro cae.'],
        ],
    ],
    8 => [
        'titulo' => 'Temporada 8',
        'episodios' => [
            ['titulo' => 'Invernalia', 'resumen' => 'Daenerys llega a Invernalia. Jon recibe noticias importantes sobre su linaje.'],
            ['titulo' => 'Caballero de los Siete Reinos', 'resumen' => 'Jaime se enfrenta a un juicio y pide perdón. Brienne es nombrada caballero. Los vivos se preparan para la batalla.'],
            ['titulo' => 'La Larga Noche', 'resumen' => 'El Rey de la Noche y su ejército llegan a Invernalia. Comienza la gran batalla entre los vivos y los muertos.'],
            ['titulo' => 'El último de los Stark', 'resumen' => 'Tras la batalla, los supervivientes miran hacia el sur. Daenerys sufre grandes pérdidas.'],
            ['titulo' => 'Las campanas', 'resumen' => 'Las fuerzas de Daenerys y Jon llegan a Desembarco del Rey. Tyrion intenta salvar la ciudad, pero el fuego reina.'],
            ['titulo' => 'El Trono de Hierro', 'resumen' => 'Tras la devastación, los líderes de Poniente deben forjar un nuevo futuro. Se elige un nuevo rey.'],
        ],
    ],
];

// --- CONSULTAR DISPONIBILIDAD EN BD ---
$videos_disponibles = [];
$sql_v = "SELECT temporada, episodio FROM videos WHERE serie = 'GoT'";
$res_v = $conexion->query($sql_v);
while($row = $res_v->fetch_assoc()) {
    $videos_disponibles[$row['temporada'] . '_' . $row['episodio']] = true;
}

// --- CONSULTAR COMPRAS DEL USUARIO ---
$temporadas_compradas = [];
$sql_c = "SELECT temporada FROM compras WHERE usuario_id = ? AND serie = 'GoT'";
$stmt_c = $conexion->prepare($sql_c);
$stmt_c->bind_param("i", $_SESSION['usuario_id']);
$stmt_c->execute();
$res_c = $stmt_c->get_result();
while($row = $res_c->fetch_assoc()) {
    $temporadas_compradas[] = $row['temporada'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_serie; ?> | Fuego Dragón</title>
    
    <link rel="icon" type="image/png" href="../activos/img/favicon_fd.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../activos/css/style.css"> 
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&display=swap" rel="stylesheet">
    
    <style>
        /* Estilos generales del cuerpo */
        body {
            background-color: #121417; 
            color: #f4f4f4;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden; /* Evitar scroll horizontal */
            overflow-y: auto; /* Habilitar scroll vertical */
        }

        /* BARRA DE NAVEGACIÓN SUPERIOR (NAVBAR) */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            background-color: #1a1a1a;
            border-bottom: 3px solid #a30000;
        }
        .logo-container {
            display: flex;
            align-items: center;
            gap: 15px; /* Espacio entre el logo y el texto */
            text-decoration: none; /* Quita el subrayado del enlace */
        }
        .logo-container img {
            height: 35px; /* Altura del logo ajustada */
            width: auto;
        }
        .logo-container h1 {
            color: #ccc;
            margin: 0;
            font-size: 1.5em;
            font-weight: bold;
            font-family: 'Cinzel', serif;
        }
        .nav-actions {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        .nav-actions a, .nav-actions span {
            color: #ccc;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        .btn-logout {
            background-color: #a30000 !important;
            color: white !important;
            font-weight: bold;
        }

        /* CONTENEDOR PRINCIPAL DE LA SERIE */
        .series-page-container {
            max-width: 1400px;
            margin: 30px auto;
            /* Aumentamos el padding inferior para dar espacio para el scroll */
            padding: 0 20px 50px 20px; 
        }
        
        /* Botón de Regreso */
        .btn-back {
            display: inline-flex;
            align-items: center;
            margin-bottom: 25px;
            color: #ccc;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.1em;
            transition: color 0.3s;
        }
        .btn-back:hover {
            color: #a30000;
        }
        .btn-back span {
            margin-right: 8px;
            font-size: 1.2em;
        }

        /* LAYOUT PRINCIPAL (Imagen + Información/Acordeón) */
        .series-detail-layout {
            display: grid;
            grid-template-columns: 350px 1fr; 
            gap: 40px;
            margin-bottom: 50px; 
        }
        
        /* IZQUIERDA: IMAGEN DE LA SERIE */
        .series-poster {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
        }
        .series-poster img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* DERECHA: INFORMACIÓN Y EPISODIOS */
        .series-info-content {
            padding-top: 5px;
        }
        .series-info-content h2 {
            font-size: 3em;
            color: white;
            margin: 0 0 10px 0;
        }
        .series-info-content p {
            color: #8fa0b5;
            font-size: 1.1em;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        
        /* SECCIÓN DE ACORDEONES (Temporadas) */
        .accordion-title {
            color: #a30000;
            font-size: 1.5em;
            margin-top: 40px;
            margin-bottom: 15px;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }
        .accordion-block {
            background-color: #1a1a1a; 
            border-radius: 4px;
            margin-bottom: 10px;
            overflow: hidden;
        }
        .accordion-header {
            padding: 15px 20px;
            background-color: #2b2b2b; 
            border-bottom: 1px solid #333;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: bold;
            font-size: 1.1em;
            color: #ccc;
        }
        .accordion-arrow {
            font-size: 1.5em;
            transition: transform 0.3s;
        }
       
        .accordion-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease-in-out;
        }
        .accordion-block.active .accordion-content {
            /* Altura suficiente para mostrar todos los 10 episodios sin problemas de scroll interno */
            max-height: 1200px; 
            overflow-y:auto;
        }
        
        /* Estilos de Episodio */
        .episode-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #333;
        }
        .episode-item:last-child {
            border-bottom: none;
        }
        .episode-details {
            text-align: left;
        }
        .episode-details h4 {
            margin: 0;
            font-size: 1em;
            color: white;
        }
        .episode-details p {
            margin: 5px 0 0 0;
            font-size: 0.85em;
            color: #8fa0b5;
        }
        
        /* Iconos y botones de acción (simulados) */
        .episode-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .action-icon {
            font-size: 1.5em;
            color: #8fa0b5;
            cursor: pointer;
        }
        .action-icon:hover {
            color: white;
        }
        .price-tag {
            background-color: gold;
            color: black;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 0.8em;
        }

        /* Estilos para etiquetas de episodio (GRATIS y BLOQUEADO) */
        .tag-free {
            background-color: #28a745; /* Verde */
            color: white;
            padding: 5px 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9em;
        }

        .locked-icon {
            color: #a30000; /* Rojo por defecto */
            cursor: pointer;
            transition: color 0.3s ease;
        }
        .locked-icon:hover {
            color: #28a745; /* Verde al pasar el mouse */
        }

        /* --- ESTILOS RESPONSIVE --- */
        @media (max-width: 768px) {
            /* Navbar */
            .navbar {
                flex-direction: column;
                gap: 15px;
                padding: 20px;
            }
            .navbar h1 {
                font-size: 1.3em;
            }

            /* Contenedor principal */
            .series-page-container {
                padding: 0 15px 40px 15px;
                margin-top: 20px;
            }

            /* Layout de una columna */
            .series-detail-layout {
                grid-template-columns: 1fr;
                gap: 25px;
            }

            /* Centrar poster */
            .series-poster {
                max-width: 300px; /* Un poco más pequeño para móviles */
                margin: 0 auto;
            }

            /* Ajustes de texto */
            .series-info-content h2 {
                font-size: 2.2em;
                text-align: center;
            }

            /* Episodios */
            .episode-item {
                flex-direction: column;
                gap: 15px;
                text-align: center;
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
            // Script para manejar los acordeones (Temporadas)
            const accordionHeaders = document.querySelectorAll('.accordion-header');
            
            accordionHeaders.forEach(header => {
                header.addEventListener('click', function() {
                    const block = this.closest('.accordion-block');
                    block.classList.toggle('active');
                    
                    // Gira la flecha
                    const arrow = this.querySelector('.accordion-arrow');
                    if (block.classList.contains('active')) {
                        arrow.style.transform = 'rotate(180deg)';
                    } else {
                        arrow.style.transform = 'rotate(0deg)';
                    }
                });
            });
            
            // Abrir la primera temporada por defecto para el demo
            const firstHeader = accordionHeaders[0];
            if (firstHeader) {
                firstHeader.click(); // Simula el clic para abrir
            }
        });
    </script>
</head>
<body>
    <header class="navbar">
        <a href="<?php echo $ruta_dashboard; ?>" class="logo-container">
            <img src="../activos/img/logo-fuegodragon-ok.png" alt="Fuego Dragón Logo">
            <h1>FUEGO DRAGÓN</h1>
        </a>
        <div class="nav-actions">
            <span class="user-greeting">Bienvenid@, <?php echo htmlspecialchars($usuario_nombre); ?></span>
            <a href="<?php echo $ruta_perfil; ?>" class="btn-profile">Mi Perfil</a>
            <a href="<?php echo $ruta_logout; ?>" class="btn-logout">Salir</a>
        </div>
    </header>

    <div class="series-page-container">
        
        <!-- Botón de volver reutilizable -->
        <a href="<?php echo $ruta_dashboard; ?>" class="btn-back-to-dashboard">← Volver a la selección</a>

        <div class="series-detail-layout">
            
            <div class="series-poster">
                <img src="../activos/img/juegodeTronos.png" alt="<?php echo $titulo_serie; ?>">
            </div>

            <div class="series-info-content">
                <h2><?php echo $titulo_serie; ?></h2>
                <p><?php echo $sinopsis; ?></p>

                <div class="accordion-title">EPISODIOS POR TEMPORADA</div>

                <?php foreach ($temporadas as $num => $temp): ?>
                    <div class="accordion-block">
                        <div class="accordion-header">
                            <?php echo $temp['titulo']; ?>
                            <span class="accordion-arrow">V</span>
                        </div>
                        <div class="accordion-content">
                            <?php if (!empty($temp['episodios'])): ?>
                                <?php foreach ($temp['episodios'] as $i => $episodio): ?>
                                    <div class="episode-item">
                                        <div class="episode-details">
                                            <h4><?php echo 'E' . ($i + 1) . ': ' . $episodio['titulo']; ?></h4>
                                            <p><?php echo $episodio['resumen']; ?></p>
                                        </div>
                                        <div class="episode-actions">
                                            <?php 
                                                $ep_num = $i + 1; // Ajuste de índice 0 a 1
                                                $key = $num . '_' . $ep_num;
                                                $disponible = isset($videos_disponibles[$key]);
                                                
                                                // Lógica de acceso: Gratis (T1 E1-4) O Comprado
                                                $es_gratis = ($num == 1 && $ep_num <= 4);
                                                $comprado = in_array($num, $temporadas_compradas);
                                                $desbloqueado = $es_gratis || $comprado;
                                            ?>

                                            <?php if ($disponible && $desbloqueado): ?>
                                                <?php if ($es_gratis && !$comprado) echo '<span class="tag-free">GRATIS</span>'; ?>
                                                
                                                <a href="ver_video.php?serie=GoT&t=<?php echo $num; ?>&e=<?php echo $ep_num; ?>" class="fa-solid fa-circle-play fa-2x action-icon" title="Ver Online" style="text-decoration:none;"></a>
                                                <span class="fa-solid fa-download fa-2x action-icon" title="Descargar ($1.00)" onclick="abrirModalPago('descarga', <?php echo $num; ?>, <?php echo $ep_num; ?>, 1.00)"></span>
                                            
                                            <?php else: ?>
                                                <!-- Botón de bloqueo que abre el modal de pago -->
                                                <span class="fa-solid fa-lock fa-2x locked-icon" title="Desbloquear Temporada" onclick="abrirModalPago('temporada', <?php echo $num; ?>, 0, 2.00)"></span>
                                                <span style="color:#a30000; font-size:0.8em; font-weight:bold;">$2.00 USD</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div style="padding: 15px 20px; color: #8fa0b5;">
                                    <p>Error de carga de episodios.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                
            </div>
        </div>
    </div>

    <footer>
        <p>
            © <?php echo date("Y"); ?> Fuego Dragón - Todos los derechos reservados.<br>
            V. 2.2.0
        </p>
    </footer>

    <!-- MODAL DE PAGO -->
    <div id="paymentModal" class="payment-modal">
        <div class="payment-content">
            <div class="payment-header">
                <h2 id="modalTitle">Desbloquear Temporada</h2>
                <span class="close-modal" onclick="cerrarModalPago()">&times;</span>
            </div>
            <div class="payment-body">
                <!-- Métodos de Pago -->
                <div class="payment-methods">
                    <div class="method-item active">
                        <i class="fa-brands fa-paypal" style="color:#0070ba; font-size:1.5em;"></i>
                        <span>PayPal</span>
                    </div>
                    <div class="method-item disabled">
                        <i class="fa-solid fa-handshake"></i> <span>Mercado Libre</span> <span class="badge-pending">Pronto</span>
                    </div>
                    <div class="method-item disabled">
                        <i class="fa-solid fa-mobile-screen"></i> <span>Nequi</span> <span class="badge-pending">Pronto</span>
                    </div>
                    <div class="method-item disabled">
                        <i class="fa-solid fa-building-columns"></i> <span>Daviplata</span> <span class="badge-pending">Pronto</span>
                    </div>
                    <div class="method-item disabled">
                        <i class="fa-brands fa-stripe-s"></i> <span>Skrill</span> <span class="badge-pending">Pronto</span>
                    </div>
                    <div class="method-item disabled">
                        <i class="fa-solid fa-cloud"></i> <span>Airtm</span> <span class="badge-pending">Pronto</span>
                    </div>
                </div>
                <!-- Detalles y Botón -->
                <div class="payment-details">
                    <p style="color:#ccc;">Estás a punto de adquirir:</p>
                    <h3 id="productName" style="color:white; margin:10px 0;">Game of Thrones - Temporada X</h3>
                    <div class="price-display" id="productPrice">$2.00</div>
                    
                    <!-- Contenedor Botones PayPal -->
                    <div id="paypal-button-container" style="width:100%;"></div>
                </div>
            </div>
        </div>
    </div>
                            <!--pruebas sandbox-->
   <!--<script src="https://www.paypal.com/sdk/js?client-id=Adbvp1XqGK34AhK_3O7JCJiYLsuYhp2m4FYMPBl0inzREYSUZXIUQTlVrjZ_eRMd816ZAIeLZzI1xzL-&currency=USD"></script>-->
   <!-- Paypal real - SDK de PayPal (Reemplaza 'test' por tu Client ID real de PayPal Developer) -->
   <script src="https://www.paypal.com/sdk/js?client-id=AZddESFFSR0zqEJxvemSFiKQNtxyey0vRGGmwsJOfTxL9nlO3e22V3W0psDkJD_E9kkOEFubh67ESmit&currency=USD"></script>
    <script>
        let currentSeason = 0;
        let currentEpisode = 0;
        let currentPrice = 0;
        let currentType = ''; // 'temporada' o 'descarga'
        const serieName = 'GoT';

        function abrirModalPago(tipo, temporada, episodio, precio) {
            currentType = tipo;
            currentSeason = temporada;
            currentEpisode = episodio;
            currentPrice = precio;
            
            if (tipo === 'temporada') {
                document.getElementById('modalTitle').innerText = "Desbloquear Temporada " + temporada;
                document.getElementById('productName').innerText = "Game of Thrones - Temporada " + temporada;
            } else {
                document.getElementById('modalTitle').innerText = "Descargar Video";
                document.getElementById('productName').innerText = "Descarga: GoT T" + temporada + " E" + episodio;
            }
            document.getElementById('productPrice').innerText = "$" + precio.toFixed(2) + " USD";
            
            document.getElementById('paymentModal').style.display = 'block';
            
            // Limpiar botón anterior si existe
            document.getElementById('paypal-button-container').innerHTML = '';

            // Renderizar botón de PayPal
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{ amount: { value: currentPrice.toString() } }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        // Pago exitoso: Guardar en BD
                        fetch('guardar_compra.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                serie: serieName,
                                temporada: currentSeason,
                                episodio: currentEpisode,
                                tipo: currentType,
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
                                if (currentType === 'descarga') {
                                    alert('La compra se realizó satisfactoriamente.');
                                    // Redirigir a la descarga
                                    window.location.href = "procesar_descarga.php?serie=" + serieName + "&t=" + currentSeason + "&e=" + currentEpisode;
                                } else {
                                    alert('El desbloqueo de la temporada se realizó satisfactoriamente.');
                                    location.reload();
                                }
                            } else {
                                alert('Error al registrar la compra: ' + data.message);
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

        function cerrarModalPago() {
            document.getElementById('paymentModal').style.display = 'none';
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('paymentModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>