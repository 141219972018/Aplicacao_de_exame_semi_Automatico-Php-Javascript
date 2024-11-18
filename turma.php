<?php require_once('../includes/funcoes.php') ?>

<?php
if (!professorConectado()) {
    header('location:../index.php');
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['submit'])) {
    adicionarTurma();
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary"> Turma </span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Turmas disponíveis
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        $turmas = todasTurmas();
                        if ($turmas) {
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Total de Alunos</th>
                                            <th>Ver Alunos</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($turmas as $turma) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo $turma['total_aluno'] ?></td>
                                                <td><a href="alunos_turma.php?turma=<?php echo $turma['id'] ?>">Clique aqui</a></td>
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

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>

<?php require_once('layouts/fim.php') ?>