<?php require_once('../includes/funcoes.php') ?>

<?php
if (isset($_POST['logout'])) {
    logout();
}
if (!alunoConectado()) {
    header('location:../index.php');
} else {
    $aluno = alunoConectado();
    $turma = getTurma($aluno['id_turma']);
}
?>

<?php require_once('layouts/header.php') ?>
<?php require_once('layouts/navbar.php') ?>

<main class="py-5" id="bg">
    <div class="container-fluid mt-5">
        <div class="row d-flex justify-content-center">
            <div class="col-md-2 p-3 bg-primary rounded-left">
                <?php
                if ($aluno['avatar']) {
                    $url = '../uploads/avatars/' . $aluno['avatar'];
                } else {
                    $url = '../includes/placeholders/150.png';
                }
                ?>
                <img class="img-fluid" src="<?php echo $url; ?>">
            </div>

            <div class="col-md-4 p-3 bg-primary text-white rounded-right">
                <div>Nome: <?php echo htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="mt-1">Turma: <?php echo htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="mt-1">Número: <?php echo htmlspecialchars($aluno['numero_aluno'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="mt-1">Nome de Utilizador: <?php echo htmlspecialchars($aluno['nome_utilizador'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        </div>
    </div>
</main>

<script src="../sbadmin/js/jquery.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/scripts.js"></script>
<script src="../sbadmin/js/toastr.min.js"></script>

<?php require_once('layouts/fim.php') ?>