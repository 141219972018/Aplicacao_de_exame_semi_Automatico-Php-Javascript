<?php require_once('../includes/funcoes.php') ?>

<?php
if (empty($_GET['exame']) || empty($_GET['turma'])) {
    header('location:index.php');
} else {
    $id_exame= $_GET['exame'];
    $id_turma = $_GET['turma'];
    if (!eProfessordeAcessoaResultadosValidos($id_exame, $id_turma)) {
        header('location:index.php');
    }
    $exame = getExame($id_exame);
    $resultados = resultados($id_turma, $id_exame);
}
if (!professorConectado()) {
    header('location:../index.php');
}
if (isset($_POST['logout'])) {
    logout();
}
?>

<?php require_once('layouts/header.php') ?>
<?php require_once('layouts/navbar.php') ?>


<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <?php require_once('layouts/sidebar.php') ?>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                <h1 class="mt-3 h5">
                    <span class="badge badge-pill badge-primary">Resultados</span>
                    <span class="text-primary h5">
                        <?php
                        echo htmlspecialchars($exame['nome_exame'], ENT_QUOTES, 'UTF-8');
                        ?>
                    </span>
                </h1>

                <?php
                $notasArray = array();
                $total_notas = $exame['total_notas'];
                $altissima = 0;
                $media = 0;
                $maisbaixo = 0;
                if ($resultados) {
                    $maisbaixo = $total_notas;
                    foreach ($resultados as $resultado) {
                        $notas = $resultado['obtido'];
                        array_push($notasArray, $notas);
                        if ($notas >= $altissima) {
                            $altissima = $notas;
                        }
                        if ($notas <= $maisbaixo) {
                            $maisbaixo = $notas;
                        }
                    }
                }
                if (count($notasArray) > 0) {
                    $media = array_sum($notasArray) / count($notasArray);
                }
                ?>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                <?php
                                $turma = getTurma($id_turma);
                                echo htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8');
                                ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <span class="text-info hover-pointer" id="printBtn" onclick="window.print();">Imprimir resultado</span>
                            </div>
                        </div>
                    </div>



                    <div class="card-body">

                        <table class="table table-bordered">
                            <tr class="bg-light">
                                <td>Total de Notas: <?php echo $total_notas ?></td>
                                <td>Notas de Aprovação: <?php echo $exame['passar_notas'] ?></td>
                                <td>Maior Pontuação: <?php echo $altissima ?></td>
                                <td>Pontuação mais Baixa: <?php echo $maisbaixo ?></td>
                                <td>Média: <?php echo round($media, 2) ?></td>
                            </tr>
                        </table>

                        <?php
                        if ($resultados) {
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="resultTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Nº Aluno</th>
                                            <th>Notas Obtidas</th>
                                            <th>Resultado</th>
                                            <th>Respostas</th>
                                            <th>Perfil</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($resultados as $resultado) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($resultado['nome_aluno'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?php echo htmlspecialchars($resultado['numero_aluno'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?php echo $resultado['obtido'] ?></td>
                                                <td>
                                                    <?php
                                                    if ($resultado['obtido'] >= $exame['passar_notas']) {
                                                        echo '<span class="text-success">Aprovado</span>';
                                                    } else {
                                                        echo '<span class="text-danger">Reprovado</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><a href="ver_respostas.php?aluno=<?php echo $resultado['id_aluno'] ?>&exame=<?php echo $id_exame?>">Visualizar</a></td>
                                                <td><a href="perfil_aluno.php?aluno=<?php echo $resultado['id_aluno'] ?>">Visita</a></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </main>

        <?php require_once('layouts/footer.php') ?>
    </div>
</div>


<?php require_once('layouts/fim.php') ?>