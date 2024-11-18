<?php require_once('../includes/funcoes.php') ?>

<?php
if (!loggedAdmin()) {
    header('location:../index.php');
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['makelive']) && isset($_POST['id_exame'])) {
    makeLive($_POST['id_exame']);
}
if (isset($_POST['atribuir'])) {
    atribuirProfessor();
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Exames</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Todos os exames
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        $exames = getExamsAdmin();
                        if ($exames) {
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Exame</th>
                                            <th>Criado por</th>
                                            <th>Turma</th>
                                            <th>Data</th>
                                            <th>ao vivo</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($exames as $exame) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($exame['nome_exame'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td>
                                                    <?php
                                                    if (!$exame['nome']) {
                                                    ?>
                                                        <span class="text-warning hover-pointer" data-toggle="modal" data-target="#addModal" data-exame-id="<?php echo $exame['id'] ?>">Professor não disponível! Ligue agora.</span>
                                                    <?php
                                                    } else {
                                                        echo htmlspecialchars($exame['nome'], ENT_QUOTES, 'UTF-8');;
                                                    ?>
                                                        <span class="text-warning float-right hover-pointer" data-toggle="modal" data-target="#addModal" data-exame-id="<?php echo $exame['id'] ?>">Mudar</span>
                                                    <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $turmas = getAtribuirTurma($exame['id']);
                                                    foreach ($turmas as $turma) {
                                                        echo '<span class="badge badge-light mr-2 font-weight-bolder">' . htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') . '</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($exame['data'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td>
                                                    <form action="" method="post">
                                                        <input type="hidden" name="id_exame" value="<?php echo $exame['id'] ?>">
                                                        <input type="hidden" name="makelive">
                                                        <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                                                        <input type="checkbox" onChange="this.form.submit()" <?php echo $exame['esta_ativo'] ? 'checked' : null ?> data-toggle="toggle" data-onstyle="success" data-size="xs">
                                                    </form>
                                                </td>
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

<!-- Modal -->
<div class="modal fade" id="addModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Atribuir professor</h5>
                <span aria-hidden="true" type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_exame">
                    <div class="form-group">
                        <label for="" class="small">Professor</label>
                        <select class="form-control" name="id_professor">
                            <?php
                            $professors = todosProfessores();
                            if ($professors) {
                                foreach ($professors as $professor) {
                                    echo '<option value="' . $professor['id'] . '">' . htmlspecialchars($professor['nome'], ENT_QUOTES, 'UTF-8') . ' - ' . htmlspecialchars($professor['nome_utilizador'], ENT_QUOTES, 'UTF-8') . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" name="atribuir" class="btn btn-primary">Atribuir</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#addModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id_exame = button.data('exame-id')
        var modal = $(this)
        modal.find('input[name="id_exame"]').val(id_exame)
    })

    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>

<?php require_once('layouts/fim.php') ?>