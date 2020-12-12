<?php
/**
 *   @author: Javier Nieto Lorenzo
 *   @since: 11/12/2020
 *   Mto de Departamentos Tema 5
*/ 

session_start(); // inicia una sesion, o recupera una existente
if(!isset($_SESSION['usuarioDAW217MtoDepartamentosTema5'])){ // si no se ha logueado le usuario
    header('Location: login.php'); // redirige a la ventana del login
    exit;
}

if(isset($_REQUEST['volver'])){ // si se ha pulsado volver
    header('Location: programa.php'); // redirige a la ventana del programa
    exit;
}

if(!isset($_SESSION['BusquedaDepartamento'])){ // si no esta definida la variable de sesion "BusquedaDepartamento"
    $_SESSION['BusquedaDepartamento']=""; // inicializacion de la variable de sesion "BusquedaDepartamento" a vacio para que muestre la primera vez todos los departamentos
}

if(!isset($_SESSION['PaginaActual'])){ // si no esta definida la variable de sesion "BusquedaDepartamento"
    $_SESSION['PaginaActual']=1; // inicializacion de la variable de sesion "PaginaActual" a 1 para que muestre la primera pagina la primera vez
}


if(isset($_REQUEST['insertar'])){ // si se ha pulsado el boton insertar
    header('Location: altaDepartamento.php'); // redirige a la ventana alta de departamento
    exit;
}

if(isset($_REQUEST['editar'])){ // si se ha pulsado el boton editar
    $_SESSION['CodDepartamento']=$_REQUEST['editar']; // asignacion del codigo de departamento a la variable de sesion 'CodDepartamento'
    header('Location: editarDepartamento.php'); // redirige a la ventana de editar departamento
    exit;
}

if(isset($_REQUEST['consultar'])){ // si se ha pulsado el boton consultar
    $_SESSION['CodDepartamento']=$_REQUEST['consultar']; // asignacion del codigo de departamento a la variable de sesion 'CodDepartamento'
    header('Location: mostrarDepartamento.php'); // redirige a la ventana de mostrar departamento
    exit;
}

if(isset($_REQUEST['borrar'])){ // si se ha pulsado el boton borrar
    $_SESSION['CodDepartamento']=$_REQUEST['borrar']; // asignacion del codigo de departamento a la variable de sesion 'CodDepartamento'
    header('Location: bajaDepartamento.php'); // redirige a la ventana de baja departamento
    exit;
}

if(isset($_REQUEST['baja'])){ // si se ha pulsado el boton baja
    $_SESSION['CodDepartamento']=$_REQUEST['baja']; // asignacion del codigo de departamento a la variable de sesion 'CodDepartamento'
    header('Location: bajaLogicaDepartamento.php'); // redirige a la ventana de baja logica departamento
    exit;
}

if(isset($_REQUEST['alta'])){ // si se ha pulsado el boton baja
    $_SESSION['CodDepartamento']=$_REQUEST['alta']; // asignacion del codigo de departamento a la variable de sesion 'CodDepartamento'
    header('Location: rehabilitacionDepartamento.php'); // redirige a la ventana de rehabilitacion departamento
    exit;
}

if(isset($_REQUEST['importar'])){ // si se ha pulsado  el boton importar
    header('Location: importarDepartamentos.php'); // redirige a la ventana importar departamentos
    exit;
}
if(isset($_REQUEST['exportar'])){ // si se ha pulsado el boton exportar
    header('Location: exportarDepartamentos.php'); // redirige al archivo de exportar departamentos
    exit;
}


require_once '../config/config.php'; // incluyo el fichero de configuracion de la aplicacion
require_once '../core/libreriaValidacion.php'; // incluyo la libreria de validacion para validar los campos de formulario
require_once '../config/confDBPDO.php'; // incluyo el fichero de configuracion de acceso a la basde de datos

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Mto. de Departamentos Tema 5</title> 
        <meta charset="UTF-8">
        <meta name="viewport"   content="width=device-width, initial-scale=1.0">
        <meta name="author"     content="Javier Nieto Lorenzo">
        <meta name="robots"     content="index, follow">      
        <link rel="stylesheet"  href="../webroot/css/estilosMtoDepartamentos.css"       type="text/css" >
        <link rel="icon"        href="../webroot/media/favicon.ico"    type="image/x-icon">
    </head>
    <body>
        <header>
            <h1>Mto. de Departamentos Tema 5 Javier</h1>
        </header>

        <main>
            
        <?php
        
            define("OPCIONAL",0);// definicion e inicializacion la constante a 0 para los campos que son opcionales
            
            $entradaOK=true; // inicialiazcion la variable que determina si esta bien la entrada de los campos introducidos por el usuario
            
            $errorDescDepartamento = null; // incializacion de la variable de errores de la descripcion del departamento

            if(isset($_REQUEST["Buscar"])){ // compruebo que el usuario le ha dado a al boton de enviar y valido la entrada de todos los campos
                $errorDescDepartamento= validacionFormularios::comprobarAlfaNumerico($_REQUEST['DescDepartamento'], 255, 0, OPCIONAL); // comprueba que el valor del campo introducido sea alfanumerico
                
                if($errorDescDepartamento != null){ // compruebo si hay algun mensaje de error en algun campo
                    $entradaOK=false; // le doy el valor false a $entradaOK
                    $_REQUEST['DescDepartamento']=""; // si hay algun campo que tenga mensaje de error pongo $_REQUEST a null
                }
            }

            if(isset($_REQUEST['Buscar'])){ // compruebo que el usuario le ha dado a al boton de enviar
                if($entradaOK){ // si la entrada de los campos esta bien
                    $_SESSION['BusquedaDepartamento']=$_REQUEST['DescDepartamento']; // asignacion del valor del campo de busqueda a la variable de sesion 'BusquedaDepartamento'
                    $_SESSION['PaginaActual']=1; // asignacion de la variable de sesion 'PaginaActual' a 1
                }
            }

            if (isset($_REQUEST['avanzarPagina'])) { // si se ha pulsado avanzar pagina
                $_SESSION['PaginaActual'] = $_REQUEST['avanzarPagina']; // el numero de pagina es el valor del boton avanzar pagina ($numero de pagina +1)
            } else if(isset($_REQUEST['retrocederPagina'])){ // si se ha pulsado retroceder pagina
                $_SESSION['PaginaActual'] = $_REQUEST['retrocederPagina']; // el numero de pagina es el valor del boton retroceder pagina ($numero de pagina -1)
            }else if(isset($_REQUEST['paginaInicial'])){ // si se ha pulsado pagina inicial
                $_SESSION['PaginaActual'] = $_REQUEST['paginaInicial']; // el numero de pagina es el valor del boton pagina inicial (1)
            }else if(isset($_REQUEST['paginaFinal'])){ // si se ha pulsado pagina final
                $_SESSION['PaginaActual'] = $_REQUEST['paginaFinal']; // el numero de pagina es el valor del boton pagina inicial ($numPaginaMaximo)
            }

        ?>
        
        <form  class="buscador" name="formularioBuscador" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
            <div>
                <label for="DescDepartamento">Descripcion del Departamento</label>
                <input type="text" id="DescDepartamento" name="DescDepartamento" placeholder="Introduzca Descripcion del Departamento" value="<?php 
                    echo $_SESSION['BusquedaDepartamento']; // si el campo esta correcto mantengo su valor en el formulario
                ?>">
                <?php
                    echo ($errorDescDepartamento!=null) ? "<span style='color:#FF0000'>".$errorDescDepartamento."</span>" : null;// si el campo es erroneo se muestra un mensaje de error
                ?>
            </div>
            <button type="submit" name="Buscar">&#128270; Buscar</button>
        </form>
        
        <?php
            
            if($entradaOK){ // si la entrada esta bien recojo los valores introducidos y hago su tratamiento 
                
                try { // Bloque de código que puede tener excepciones en el objeto PDO
                    $miDB = new PDO(DNS,USER,PASSWORD); // creo un objeto PDO con la conexion a la base de datos

                    $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion
                    
                    
                    $sqlDepartamentosLimit = 'SELECT * FROM T02_Departamento WHERE T02_DescDepartamento LIKE "%":DescDepartamento"%" LIMIT '.(($_SESSION['PaginaActual']-1)*MAX_NUMERO_REGISTROS).','.MAX_NUMERO_REGISTROS;
                    
                    $consultaDepartamentosLimit = $miDB->prepare($sqlDepartamentosLimit); // preparo la consulta           
                    $parametros = [":DescDepartamento" => $_SESSION['BusquedaDepartamento']];
                    $consultaDepartamentosLimit->execute($parametros); // ejecuto la consulta con los paremtros del array de parametros 
                    
                    $sqlNumeroDepartamentos = 'SELECT count(*) FROM T02_Departamento WHERE T02_DescDepartamento LIKE "%":DescDepartamento"%"';
                    
                    $consultaNumeroDepartamentos = $miDB->prepare($sqlNumeroDepartamentos); // preparo la consulta
                    $parametrosNumDepartamentos = [":DescDepartamento" => $_SESSION['BusquedaDepartamento']];
                    $consultaNumeroDepartamentos->execute($parametrosNumDepartamentos); // ejecuto la consulta con los paremtros del array de parametros 
                    
                    $resultado = $consultaNumeroDepartamentos->fetch(); // devuelve el numero de departamentos que hay en la posicion 0 de un array
                    
                    if(($resultado[0]%MAX_NUMERO_REGISTROS)==0){ // si el resto del numero de registros entre el numero de paginas es cero el maximo de paginas es su division
                        $numPaginaMaximo=$resultado[0]/MAX_NUMERO_REGISTROS;
                    }else{ // si el resto no es cero el numero de paginas es la divison redondeada a la baja + 1
                        $numPaginaMaximo =  floor($resultado[0]/MAX_NUMERO_REGISTROS)+1;
                    }
                    settype($numPaginaMaximo,"integer"); // cambio del tipo de $numPaginaMaximo a integer 
        ?>  
            
            <div class="content">
                <form name="formularioDepartamentos" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                    <table class="tablaDepartamentos">
                        <thead>
                            <tr>
                                <th>CodDepartamento</th>
                                <th>DescDepartamento</th>
                                <th>FechaCreacion</th>
                                <th>FechaBaja</th>
                                <th>VolumenNegocio</th>
                            </tr>
                        </thead>
                        <tbody>

                        <?php 
                        if($consultaDepartamentosLimit->rowCount()>0){ // si la consulta devuelve algun registro
                            $oDepartamento = $consultaDepartamentosLimit->fetchObject(); // Obtengo el primer registro de la consulta como un objeto
                            while($oDepartamento) { // recorro los registros que devuelve la consulta de la consulta 
                                $codDepartamento = $oDepartamento->T02_CodDepartamento; // variable que almacena el codigo del departamento
                        ?>
                            <tr <?php echo (($oDepartamento->T02_FechaBajaDepartamento)==null)? "class='verde'" : "class='bajaLogica'";?>>
                                <td><?php echo $codDepartamento; // obtengo el valor del codigo del departamento del registro actual ?></td>
                                <td><?php echo $oDepartamento->T02_DescDepartamento; // obtengo el valor de la descripcion del departamento del registro actual ?></td>
                                <td><?php echo date('d/m/Y',$oDepartamento->T02_FechaCreacionDepartamento); // obtengo el valor de la fecha de creacion del departamento del registro actual ?></td>
                                <td><?php echo (($oDepartamento->T02_FechaBajaDepartamento)==null)? "NULL": date('d/m/Y',$oDepartamento->T02_FechaBajaDepartamento); // obtengo el valor de la fecha de baja del departamento del registro actual ?></td>
                                <td><?php echo $oDepartamento->T02_VolumenNegocio; // obtengo el valor de la fecha de baja del departamento del registro actual ?></td>
                                <td>
                                    <button name="editar" value="<?php echo $codDepartamento;?>">&#9999;&#65039;</button>
                                    <button name="consultar" value="<?php echo $codDepartamento;?>">&#128220;</button>
                                    <button name="borrar" value="<?php echo $codDepartamento;?>">&#128465;&#65039;</button>
                                    <?php if($oDepartamento->T02_FechaBajaDepartamento==null){ // si la fecha de baja es null?>
                                    <button name="baja" value="<?php echo $codDepartamento;?>"><img class="imgButton" src="../webroot/media/baja.png" alt=""></button>
                                    <?php }else{ ?>
                                    <button name="alta" value="<?php echo $codDepartamento;?>"><img class="imgButton" src="../webroot/media/alta.png" alt=""></button>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php 
                                $oDepartamento = $consultaDepartamentosLimit->fetchObject(); // guardo el registro actual como un objeto y avanzo el puntero al siguiente registro de la consulta 
                            }

                        }else{ // si no se encuentra ningun registro
                        ?>
                    <tr>
                        <td class="rojo">No Hay ningun departamento con esa descripcion</td>
                    </tr>
                        <?php }?>
                    </tbody>
                    </table>
                </form>
                <?php if($consultaDepartamentosLimit->rowCount()>0){ // si la consulta devuelve algun registro?>
                <form name="formularioPaginacion" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                    <button <?php echo ($_SESSION['PaginaActual']==1)? "hidden" : null;?> type="submit" name="paginaInicial" value="1"><img class="imgPaginacion" src="../webroot/media/pagInicial.png" alt=""></button>
                    <button <?php echo ($_SESSION['PaginaActual']==1)? "hidden" : null;?> type="submit" name="retrocederPagina" value="<?php echo $_SESSION['PaginaActual']-1;?>"><img class="imgPaginacion" src="../webroot/media/pagAnterior.png" alt=""></button>
                    <div><?php echo $_SESSION['PaginaActual']." de ".$numPaginaMaximo?></div>
                    <button <?php echo ($_SESSION['PaginaActual']>=$numPaginaMaximo)? "hidden" : null;?> type="submit" name="avanzarPagina" value="<?php echo $_SESSION['PaginaActual']+1;?>"><img class="imgPaginacion" src="../webroot/media/pagSiguiente.png" alt=""></button>
                    <button <?php echo ($_SESSION['PaginaActual']>=$numPaginaMaximo)? "hidden" : null;?> type="submit" name="paginaFinal" value="<?php echo $numPaginaMaximo;?>"><img class="imgPaginacion" src="../webroot/media/pagFinal.png" alt=""></button>
                </form>
                <?php } ?>
                <form name="formularioBotones" action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
                    <button type="submit" name="importar" ><img src="../webroot/media/importar.png" alt="importar"> Importar</button>
                    <button type="submit" name="exportar" ><img src="../webroot/media/exportar.png" alt="exportar"> Exportar</button>
                    <button type="submit" name="insertar" ><img src="../webroot/media/insertar.png" alt="insertar"> Insertar</button>
                    <button type="submit" name="volver" value="Volver">&#11152; Volver</button>
                </form>
                <?php
                
                }catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
                    echo "<p style='color:red;'>ERROR EN LA CONEXION</p>";
                    echo "<p style='color:red;'>Código de error: ".$miExceptionPDO->getCode()."</p>"; // Muestra el codigo del error
                    echo "<p style='color:red;'>Error: ".$miExceptionPDO->getMessage()."</p>"; // Muestra el mensaje de error
                    die(); // Finalizo el script
                }finally{ // codigo que se ejecuta haya o no errores
                    unset($miDB);// destruyo la variable 
                }
            }
        ?>
            </div> 
        </main>    
        <footer>
            <a href="http://daw217.ieslossauces.es/" target="_blank"> <img src="../webroot/media/oneandone.png" alt="oneandone icon" width="40"></a>
            <address>  <a href="../../index.html">&copy; 2020-2021 Javier Nieto Lorenzo</a> </address>
            <a href="https://github.com/JavierNLSauces/" target="_blank"><img width="40" src="../webroot/media/github.png" ></a>
        </footer>
    </body>
</html>