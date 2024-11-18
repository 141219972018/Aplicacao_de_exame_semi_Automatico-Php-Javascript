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
            <?php
            $examesAoVivo = getExamesAoVivo($id_turma);
            if ($examesAoVivo) {
                foreach ($examesAoVivo as $exame) {
                    $disable = null;
                    $tempoRestante = temporizadorRestante($exame['id']);
                    if ($tempoRestante) {
                        if ($tempoRestante <= 0) {
                            $disable = 'disabled';
                        }
                    }
            ?>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3 class="h5 mb-4"><?php echo htmlspecialchars($exame['nome_exame'], ENT_QUOTES, 'UTF-8') ?></h3>
                                Perguntas: <?php echo $exame['total_de_perguntas'] ?> <br>
                                Notas: <?php echo $exame['total_notas'] ?> <br>
                                Tempo: <?php echo $exame['tempo_total'] . " Minutos" ?>
                            </div>
                            <div class="card-footer">
                                <a href="exame.php?id=<?php echo $exame['id'] ?>" class="btn btn-light btn-block <?php echo $disable; ?>">Continuar <i class="fas fa-angle-double-right"></i></a>
                            </div>
                        </div>
                    </div>
            <?php
                }
            }
            ?>
        </div>
    </div>
</main>

<script src="../sbadmin/js/jquery.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/scripts.js"></script>
<script src="../sbadmin/js/toastr.min.js"></script>

<?php require_once('layouts/fim.php') ?>