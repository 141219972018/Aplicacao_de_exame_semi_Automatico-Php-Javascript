<?php require_once('../includes/funcoes.php') ?>

<?php
if (isset($_POST['logout'])) {
    logout();
}
if (!alunoConectado()) {
    header('location:../index.php');
} else {
    $aluno = alunoConectado();
    $id_turma = $aluno['id_turma'];
}
?>

<?php require_once('layouts/header.php') ?>
<?php require_once('layouts/navbar.php') ?>

<main class="py-5" id="bg">
    <div class="container-fluid mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-8 text-center">

                <?php
                $resultados = getMeusResultados();
                    if ($resultados) {

                    ?>

                    <table class="table table-bordered bg-white">
                        <thead class="bg-info text-white">
                            <tr>
                                <th>Nome do exame</th>
                                <th>Data do exame</th>
                                <th>Total de Notas</th>
                                <th>Notas Obtidas</th>
                                <th>Resultado</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            foreach ($resultados as $resultado) {
                            ?>
                                <tr class="text-dark">
                                    <td><?php echo htmlspecialchars($resultado['nome_exame'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?php echo $resultado['data'] ?></td>
                                    <td><?php echo $resultado['total_notas'] ?></td>
                                    <td><?php echo $resultado['obtido'] ?></td>
                                    <td><?php echo (isset($resultado['obtido']) && ($resultado['obtido'] >= $resultado['passar_notas'])) ? '<span class="text-success">Aprovado</span>' : '<span class="text-danger">Reprovado</span>' ?></td>
                                </tr>

                            <?php } ?>
                        </tbody>
                    </table>

                <?php } else { ?>
                    <div class="alert alert-secondary">
                        <h2>Desculpe, nenhum resultado encontrado!</h2>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</main>

<script src="../sbadmin/js/jquery.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/scripts.js"></script>
<script src="../sbadmin/js/toastr.min.js"></script>

<?php require_once('layouts/fim.php') ?>