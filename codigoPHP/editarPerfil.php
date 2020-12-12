<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 02/12/2020
 *   Editar perfil
*/

session_start(); // inicia una sesion, o recupera una existente
if(!isset($_SESSION['usuarioDAW217MtoDepartamentosTema5'])){ // si no se ha logueado le usuario
    header('Location: login.php'); // redirige al login
    exit;
}

require_once '../config/confDBPDO.php'; // incluyo el fichero de configuracion de acceso a la basde de datos

if (isset($_REQUEST['BorrarUsuario'])) { // si se ha pulsado el boton de Detalle
    try { // Bloque de código que puede tener excepciones en el objeto PDO
        $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

        $sqlUsuario = "DELETE FROM T01_Usuario WHERE T01_CodUsuario=:CodUsuario"; 

        $consultaUsuario = $miDB->prepare($sqlUsuario); // prepara la consulta
        $parametros = [':CodUsuario' => $_SESSION['usuarioDAW217MtoDepartamentosTema5'] // creo el array de parametros con el valor de los parametros de la consulta
                      ];

        $consultaUsuario->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros

    } catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
        echo "<p style='color:red;'>ERROR EN LA CONEXION</p>";
        echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
        echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
        die(); // Finalizo el script
    } finally { // codigo que se ejecuta haya o no errores
        unset($miDB); // destruyo la variable 
    } 
    session_destroy(); // Destruye toda los datosn asociados a la sesion actual
    header('Location: login.php'); // redire¡ige a la misma pagina
    exit;
}

if (isset($_REQUEST['Cancelar'])) { // si se ha pulsado el boton de Detalle
    header('Location: programa.php'); // redire¡ige a la misma pagina
    exit;
}

$aLang['es']=[ // array de las traducciones al castellano
             'title' => 'Editar Perfil',
             'logoff' => 'Cerrar sesion',
             'user' => 'Usuario',
             'description' => 'Descripcion',
             'numConexiones' => 'Numero conexiones',  
             'lastConnection' => 'Ultima conexion',
             'password' => 'Cambiar contraseña',
             'imageUser' => 'Imagen Usuario',
             'delUser' => 'Borrar Usuario',
             'change' => 'Editar',
             'cancel' => 'Cancelar'
];

$aLang['en']=[ // array de las traducciones al ingles
             'title' => 'Edit profile',
             'logoff' => 'Logoff',
             'user' => 'User',
             'description' => 'Description',
             'numConexiones' => 'Number connections',  
             'lastConnection' => 'Last Connection',
             'password' => 'Change password',
             'imageUser' => 'User image',
             'delUser' => 'Detele User',
             'change' => 'Edit',
             'cancel' => 'Cancel' 
];

require_once '../core/libreriaValidacion.php'; // incluyo la libreria de validacion para validar los campos del formulario


try { // Bloque de código que puede tener excepciones en el objeto PDO
    $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

    $sqlUsuario = "SELECT * FROM T01_Usuario WHERE T01_CodUsuario=:CodUsuario"; 

    $consultaUsuario = $miDB->prepare($sqlUsuario); // prepara la consulta
    $parametros = [':CodUsuario' => $_SESSION['usuarioDAW217MtoDepartamentosTema5'] // creo el array de parametros con el valor de los parametros de la consulta
                  ];

    $consultaUsuario->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
    
    $oUsuario = $consultaUsuario->fetchObject(); // guarda en la variable un objeto con los datos solicitados en la consulta
    
    $numConexiones = $oUsuario->T01_NumConexiones; // variable que tiene el numero de conexiones sacado de la base de datos
    $descUsuario = $oUsuario->T01_DescUsuario; // variable que tiene la descripcion del usuario sacado de la base de datos
    $imagenUsuario = $oUsuario->T01_ImagenUsuario;

} catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
    echo "<p style='color:red;'>ERROR EN LA CONEXION</p>";
    echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
    echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
    die(); // Finalizo el script
} finally { // codigo que se ejecuta haya o no errores
    unset($miDB); // destruyo la variable 
}

define("OBLIGATORIO", 1); // defino e inicializo la constante a 1 para los campos que son obligatorios

$entradaOK=true; // declaro la variable que determina si esta bien la entrada de los campos introducidos por el usuario


$aErrores = [ //declaro e inicializo el array de errores
    'DescUsuario' => null,
    'ImagenUsuario' => null
];


if (isset($_REQUEST["Editar"])) { // comprueba que el usuario le ha dado a al boton de IniciarSesion y valida la entrada de todos los campos
    $aErrores['DescUsuario'] = validacionFormularios::comprobarAlfaNumerico($_REQUEST['DescUsuario'], 255, 3, OBLIGATORIO); // comprueba que la entrada del codigo de usuario es correcta

    if (!empty($_FILES['ImagenUsuario']['name'])) { // si se ha subido un archvo
        if ($_FILES['ImagenUsuario']['size'] < 5242880) { // si el tamaño del archivo es menor de 5MB
            if (($_FILES["ImagenUsuario"]["type"] == "image/jpeg") || ($_FILES["ImagenUsuario"]["type"] == "image/jpg") || ($_FILES["ImagenUsuario"]["type"] == "image/png")) { // si el tipo del archivo es correcto
                $imagenUsuario = file_get_contents($_FILES["ImagenUsuario"]['tmp_name']); // pasa el el archivo  a una cade y se guarda en la variable $imagenUsuario
            } else {
                $aErrores['ImagenUsuario'] = "Los formatos admitidos son : jpeg,jpg,png";
            }
        } else {
            $aErrores['ImagenUsuario'] = "La imagen no puede ocupar mas de 5MB";
        }
        
        
    }
    if ($aErrores['DescUsuario'] != null) { // compruebo si hay algun mensaje de error en algun campo
            $entradaOK = false; // le doy el valor false a $entradaOK
            $_REQUEST['DescUsuario'] = ""; // si hay algun campo que tenga mensaje de error pongo $_REQUEST a null
        }
        
        if ($aErrores['ImagenUsuario'] != null) { // compruebo si hay algun mensaje de error en algun campo
            $entradaOK = false; // le doy el valor false a $entradaOK
        }
} else { // si el usuario no le ha dado al boton de enviar
    $entradaOK = false; // le doy el valor false a $entradaOK
}

if ($entradaOK) { // si la entrada esta bien recojo los valores introducidos y hago su tratamiento

    try { // Bloque de código que puede tener excepciones en el objeto PDO
        $miDB = new PDO(DNS, USER, PASSWORD); // creo un objeto PDO con la conexion a la base de datos

        $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion

        $sqlUpdateDescUsuario = "UPDATE T01_Usuario SET T01_DescUsuario=:DescUsuario, T01_ImagenUsuario=:ImagenUsuario WHERE T01_CodUsuario=:CodUsuario" ;

        $consultaUpdateDescUsuario = $miDB->prepare($sqlUpdateDescUsuario); // prepara la consulta
        $parametros = [':DescUsuario' => $_REQUEST['DescUsuario'],// creo el array de parametros con el valor de los parametros de la consulta
                       ':ImagenUsuario' => $imagenUsuario,
                       ':CodUsuario' => $_SESSION['usuarioDAW217MtoDepartamentosTema5']
                       ]; 

        $consultaUpdateDescUsuario->execute($parametros); // ejecuto la consulta pasando los parametros del array de parametros
        
        
        header('Location: programa.php'); // redirige al programa
        exit;
        
    } catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
        echo "<p style='color:red;'>ERROR EN LA CONEXION</p>";
        echo "<p style='color:red;'>Código de error: " . $miExceptionPDO->getCode() . "</p>"; // Muestra el codigo del error
        echo "<p style='color:red;'>Error: " . $miExceptionPDO->getMessage() . "</p>"; // Muestra el mensaje de error
        die(); // Finalizo el script
    } finally { // codigo que se ejecuta haya o no errores
        unset($miDB); // destruyo la variable 
    }
    
}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?php echo $aLang[$_COOKIE['idiomaActual']]['title']; ?></title>
        <meta name="viewport"   content="width=device-width, initial-scale=1.0">
        <meta name="author"     content="Javier Nieto Lorenzo">
        <meta name="robots"     content="index, follow">      
        <link rel="stylesheet"  href="../webroot/css/estilosLoginLogoff.css"       type="text/css" >
        <link rel="icon"        href="../webroot/media/favicon.ico"    type="image/x-icon">
        <style>
            header>img{
                position: absolute;
                right: 160px;
                width: 80px;
            }
        </style>
    </head>
    <body>
        <header>
            <h1><?php echo $aLang[$_COOKIE['idiomaActual']]['title']; ?></h1>
            
        </header>
        <main class="flex-container-align-item-center">
            <form name="editarPerfil" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">

                <div>
                    <label for="CodUsuario"><?php echo $aLang[$_COOKIE['idiomaActual']]['user']; ?></label>
                    <input class="required" type="text" id="CodUsuario" name="CodUsuario" value="<?php echo $_SESSION['usuarioDAW217MtoDepartamentosTema5']; ?>" readonly>
                </div>
                <div>
                    <label for="DescUsuario"><?php echo $aLang[$_COOKIE['idiomaActual']]['description']; ?></label>
                    <input style="width: 240px;" class="required" type="text" id="DescUsuario" placeholder="<?php echo $aLang[$_COOKIE['idiomaActual']]['description']; ?>" name="DescUsuario" value="<?php
                        echo (isset($_REQUEST['DescUsuario'])) ? $_REQUEST['DescUsuario'] : $descUsuario; 
                        ?>">
                </div>
                <?php
                    echo ($aErrores['DescUsuario']!=null) ? "<span style='color:#FF0000'>".$aErrores['DescUsuario']."</span>" : null;// si el campo es erroneo se muestra un mensaje de error
                ?>
                
                <div>
                    <label for="NumConexiones"><?php echo $aLang[$_COOKIE['idiomaActual']]['numConexiones']; ?></label>
                    <input style="width: 100px;" class="required" type="text" id="NumConexiones" name="NumConexiones" value="<?php echo $numConexiones ?>" readonly>
                </div>
                
                <?php if($_SESSION['fechaHoraUltimaConexionAnterior']!=null){ ?>
                <div>
                    <label for="UltimaConexion"><?php echo $aLang[$_COOKIE['idiomaActual']]['lastConnection']; ?></label>
                    <input style="width: 240px;;" class="required" type="text" id="UltimaConexion" name="UltimaConexion" value="<?php echo date('d/m/Y H:i:s', $_SESSION['fechaHoraUltimaConexionAnterior']) ?>" readonly>
                </div>
                <?php } ?>
                
                <div style="width: 500px";>
                    <label for="ImagenUsuario"><?php echo $aLang[$_COOKIE['idiomaActual']]['imageUser'] ?></label>
                    <input style="width: 390px;margin: auto; font-size: 1rem" class="required" type="file" id="ImagenUsuario" name="ImagenUsuario" value="">
                </div>
                <?php
                    echo ($aErrores['ImagenUsuario']!=null) ? "<span style='color:#FF0000'>".$aErrores['ImagenUsuario']."</span>" : null;// si el campo es erroneo se muestra un mensaje de error
                ?>
                
                <div>
                    <a class="registrarse" href="cambiarPassword.php"><?php echo $aLang[$_COOKIE['idiomaActual']]['password'] ?></a>
                    <button style="margin:auto;" class="button" type="submit" name="Editar"><?php echo $aLang[$_COOKIE['idiomaActual']]['change'] ?></button>
                    <button style="margin:auto; margin-top: 5px;" class="button" name="Cancelar"><?php echo $aLang[$_COOKIE['idiomaActual']]['cancel'] ?></button>
                    <button style="margin:auto; margin-top: 5px;" id="borrarUsuario" class="button" name="BorrarUsuario"><?php echo $aLang[$_COOKIE['idiomaActual']]['delUser'];?></button>
                </div>

                </form>
        </main>
    </body>
    <footer class="fixed">
        <a href="http://daw217.ieslossauces.es/" target="_blank"> <img src="../webroot/media/oneandone.png" alt="oneandone icon" width="40"></a>
        <address>  <a href="../../index.html">&copy; 2020-2021 Javier Nieto Lorenzo</a> </address>
        <a href="https://github.com/JavierNLSauces/" target="_blank"><img class="github" width="40" src="../webroot/media/github.png" ></a>
    </footer>
</html>