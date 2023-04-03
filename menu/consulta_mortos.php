<?php
    session_start();
    if(!isset($_SESSION['nif'])){
        header("Location: index.php");
        exit();
    }

    if (isset($_POST['distrito_consulta']) && isset($_POST['ano_consulta'])){
        $consulta_dis =  $_POST['distrito_consulta'];
        $ano = $_POST['ano_consulta'];
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login V8</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->
    <link rel="icon" type="image/png" href="images/icons/favicon.ico"/>
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/bootstrap/css/bootstrap.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="fonts/font-awesome-4.7.0/css/font-awesome.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/animate/animate.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/css-hamburgers/hamburgers.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/animsition/css/animsition.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/select2/select2.min.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="vendor/daterangepicker/daterangepicker.css">
<!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="css/util.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
<!--===============================================================================================-->
</head>
<body>

<div class="topnav">
        <a class="active" href="#home">Home</a>
        <a href="consultas.php">Consultas</a>
        <a href="../logout.php">Logout</a>
</div>

    <div class="limiter">
        <div class="container-login100">
            <div class="wrap-login100">
                    <span class="login100-form-title">Acidentes com Mortos por Ano</span>

                    <div style="margin-top: 70px;">
                        <label>Selecione o distrito e ano:</label>
                        <form method="post" action="">
                            <select id="distrito_consulta" class="select100" name="distrito_consulta" onchange="jsFunction()">
                            <option value="select">Selecione distrito...</option>
                                <?php
                                    include_once('../Config.php');

                                    $conn = accessBD();
                                    $sql = 'Select nome_distrito From distritos';

                                    $result = pg_prepare($conn, "query", $sql);
                                    $result = pg_execute($conn, "query", array());

                                    if ($result != FALSE) {
                                        while($arr = pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
                                ?>
                                        <option value="<?php echo $arr["nome_distrito"]?>">
                                            <?php echo $arr["nome_distrito"]?>
                                        </option>
                                <?php
                                        }
                                    }

                                    pg_close($conn);
                                ?>
                            </select>
                            <select id="ano_consulta" class="select100" name="ano_consulta" onchange="jsFunction()">
                            <option value="select">Selecione ano...</option>
                                <option value="2014">2014</option>
                                <option value="2015">2015</option>
                                <option value="2016">2016</option>
                            </select>
                            <button value="soubotão" name="but_concelho">Pesquisar</button>
                             </form>
                            <span class="focus-input100"></span>
                    </div>
            </div>
        </div>
    </div>
    <div class="limiter">
        <div class="container-table100">
            <div class="wrap-table100">
                <div class="table100 ver1 m-b-110">
                    <div class="table100-head">
                        <table>
                            <thead>
                                <tr class="row100 head">
                                    <th class="cell100 column1">Concelho</th>
                                    <th class="cell100 column2">Data e Hora</th>
                                    <th class="cell100 column3">Mortos</th>
                                    <th class="cell100 column4">Feridos Graves</th>
                                    <th class="cell100 column5">Km</th>
                                    <th class="cell100 column6">Via</th>
                                    <th class="cell100 column7">Natureza</th>
                                    <th class="cell100 column8">IG</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="table100-body js-pscroll">
                        <table>
                            <tbody>
                            <?php
                                include_once('../Config.php');

                                $conn = accessBD();

                                $sql = "Select * from sinistralidades Where m > 0 " .
                                    "And datahora BETWEEN $1 AND $2 " .
                                    "And concelho IN (Select nome_concelho from concelhos Where cod_distrito = " .
                                                            "(Select cod_distrito From distritos Where nome_distrito = $3))";
                                $result = pg_prepare($conn, "query", $sql);
                                $result = pg_execute($conn, "query", array($ano.'-01-01', $ano.'-12-31', $consulta_dis));

                                if ($result != FALSE) {
                                    while($arr = pg_fetch_array($result, NULL, PGSQL_ASSOC)) {
                            ?>
                                        <tr class="row100 body">
                                            <td class="cell100 column1"> <?php echo $arr["concelho"]?> </td>
                                            <td class="cell100 column2"> <?php echo $arr["datahora"]?> </td>
                                            <td class="cell100 column3"> <?php echo $arr["m"]?> </td>
                                            <td class="cell100 column4"> <?php echo $arr["fg"]?> </td>
                                            <td class="cell100 column5"> <?php echo $arr["km"]?> </td>
                                            <td class="cell100 column6"> <?php echo $arr["via"]?> </td>
                                            <td class="cell100 column7"> <?php echo $arr["natureza"]?> </td>
                                            <td class="cell100 column8"> <?php echo $arr["ig"]?> </td>
                                        </tr>
                                <?php
                                    }
                                }

                                pg_close($conn);
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
        </div>
    </div>


<!--===============================================================================================-->
    <script src="vendor/jquery/jquery-3.2.1.min.js"></script>
<!--===============================================================================================-->
    <script src="vendor/animsition/js/animsition.min.js"></script>
<!--===============================================================================================-->
    <script src="vendor/bootstrap/js/popper.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
<!--===============================================================================================-->
    <script src="vendor/select2/select2.min.js"></script>
<!--===============================================================================================-->
    <script src="vendor/daterangepicker/moment.min.js"></script>
    <script src="vendor/daterangepicker/daterangepicker.js"></script>
<!--===============================================================================================-->
    <script src="vendor/countdowntime/countdowntime.js"></script>
<!--===============================================================================================-->
    <script src="js/main.js"></script>

</body>
</html>