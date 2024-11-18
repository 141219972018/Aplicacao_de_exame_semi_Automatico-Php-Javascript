<?php require_once('../includes/funcoes.php') ?>

<?php
if (!professorConectado()) {
    header('location:../index.php');
}
if (!isset($_GET['aluno'])) {
    header('location:index.php');
} else {
    $aluno = getAluno($_GET['aluno']);
    $turma = getTurma($aluno['id_turma']);
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Perfil</span></h1>
                <div class="card mt-3 mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <?php
                                if ($aluno['avatar']) {
                                    $url = '../uploads/avatars/' . $aluno['avatar'];
                                } else {
                                    $url = '../includes/placeholders/150.png';
                                }
                                ?>
                                <img class="img-fluid" src="<?php echo $url; ?>">
                            </div>
                            <div class="col-md-3">
                                <div>Nome: <?php echo htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="mt-1">Turma: <?php echo htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="mt-1">Nº Aluno: <?php echo $aluno['numero_aluno'] ?></div>
                                <div class="mt-1">Nome de Utilizador: <?php echo htmlspecialchars($aluno['nome_utilizador'], ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                            <div class="col-md-7">
                                <?php
                                $perfilResultados = perfilResultados($aluno['id']);
                                if ($perfilResultados) {
                                ?>
                                    <table class="table table-bordered">
                                        <tr class="bg-light">
                                            <th>Exame</th>
                                            <th>Total</th>
                                            <th>Obtido</th>
                                            <th>Resultado</th>
                                        </tr>
                                        <?php foreach ($perfilResultados as $resultado) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($resultado['nome_exame'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?php echo $resultado['total_notas'] ?></td>
                                                <td><?php echo $resultado['obtido'] ?></td>
                                                <td>
                                                    <?php
                                                    if ($resultado['obtido'] >= $resultado['passar_notas']) {
                                                        echo '<span class="text-success">Passed</span>';
                                                    } else {
                                                        echo '<span class="text-danger">Failed</span>';
                                                    }

                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col">
                        <?php require_once('../includes/erros_de_formulario.php') ?>
                    </div>
                </div>
            </div>
        </main>

        <?php require_once('layouts/footer.php') ?>
    </div>
</div>


<script>

</script>

<?php require_once('layouts/fim.php') ?>