<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>BorraDAW217DBProyectoTema51&1</title>
    </head>
    <body>
        <h1>Javier Nieto Lorenzo</h1>
        <?php
        /**
         *   @author: Javier Nieto Lorenzo
         *   @since: 26/11/2020
         *   CargaInicialDAW217DBProyectoTema51&1

        */ 
            require_once '../config/confDBPDO.php';
            echo "<h2>CargaInicialDAW217DBProyectoTema51&1</h2>";
            try { // Bloque de código que puede tener excepciones en el objeto PDO
                $miDB = new PDO(DNS,USER,PASSWORD); // creo un objeto PDO con la conexion a la base de datos
                
                $miDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Establezco el atributo para la apariciopn de errores y le pongo el modo para que cuando haya un error se lance una excepcion
                
                $sql = <<<SQL
                    -- La contraseña de los usuarios, es el codUsuario concatenado con el password, en este caso paso. [codUsuario . Password]

                    -- Introduccion de datos dentro de la tabla creada
                    INSERT INTO T02_Departamento(T02_CodDepartamento, T02_DescDepartamento, T02_FechaCreacionDepartamento, T02_VolumenNegocio) VALUES
                        ('INF', 'Departamento de informatica',1606156754, 5),
                        ('VEN', 'Departamento de ventas',1606156754, 8),
                        ('ALM', 'Departamento de almacen',1606156754, 8),
                        ('PRD', 'Departamento de produccion',1606156754, 8),
                        ('AYC', 'Departamento de administracion y contabilidad',1606156754, 8),
                        ('CON', 'Departamento de contabilidad',1606156754, 9),
                        ('MAT', 'Departamento de matematicas',1606156754, 8),
                        ('QUM', 'Departamento de quimica',1606156754, 30),
                        ('TEC', 'Departamento de tecnologia',1606156754, 40),
                        ('FIS', 'Departamento de fisica',1606156754, 45),
                        ('LEC', 'Departamento de lengua castellana',1606156754, 4),
                        ('MEC', 'Departamento de mecanica ',1606156754, 20),
                        ('VAL', 'Departamento de valores ',1606156754, 20),
                        ('REL', 'Departamento de religion ',1606156754, 20),
                        ('DTE', 'Departamento de dibujo tecnico ',1606156754, 20),
                        ('MKT', 'Departamento de marketing',1606156754, 1);
                    -- 1606156754 -> 23-nov-2020 ~19:39:14 --
                    -- El tipo de usuario es "usuario" como predeterminado, despues añado un admin --
                    INSERT INTO T01_Usuario(T01_CodUsuario, T01_DescUsuario, T01_Password) VALUES
                        ('nereaA','NereaA',SHA2('nereaApaso',256)),
                        ('miguel','Miguel',SHA2('miguelpaso',256)),
                        ('bea','Bea',SHA2('beapaso',256)),
                        ('nereaN','NereaN',SHA2('nereaNpaso',256)),
                        ('cristinaM','CristinaM',SHA2('cristinaMpaso',256)),
                        ('susana','Susana',SHA2('susanapaso',256)),
                        ('sonia','Sonia',SHA2('soniapaso',256)),
                        ('elena','Elena',SHA2('elenapaso',256)),
                        ('nacho','Nacho',SHA2('nachopaso',256)),
                        ('raul','Raul',SHA2('raulpaso',256)),
                        ('luis','Luis',SHA2('luispaso',256)),
                        ('arkaitz','Arkaitz',SHA2('arkaitzpaso',256)),
                        ('rodrigo','Rodrigo',SHA2('rodrigopaso',256)),
                        ('javier','Javier',SHA2('javierpaso',256)),
                        ('cristinaN','CristinaN',SHA2('cristinaNpaso',256)),
                        ('heraclio','Heraclio',SHA2('heracliopaso',256)),
                        ('amor','Amor',SHA2('amorpaso',256)),
                        ('antonio','Antonio',SHA2('antoniopaso',256)),
                        ('leticia','Leticia',SHA2('leticiapaso',256));

                    -- Usuario con el rol admin --
                    INSERT INTO Usuario(CodUsuario, DescUsuario, Password, Perfil) VALUES ('admin','admin',SHA2('adminpaso',256), 'administrador');

SQL;
                $miDB->exec($sql);

               
               echo "<p style='color:green;'>CARGA INICIAL CORRECTO</p>";
            }catch (PDOException $miExceptionPDO) { // Codigo que se ejecuta si hay alguna excepcion
                echo "<p style='color:red;'>ERROR</p>";
                echo "<p style='color:red;'>Código de error: ".$miExceptionPDO->getCode()."</p>"; // Muestra el codigo del error
                echo "<p style='color:red;'>Error: ".$miExceptionPDO->getMessage()."</p>"; // Muestra el mensaje de error
            }finally{
                unset($miDB);
            }

        ?> 
    </body>
</html>