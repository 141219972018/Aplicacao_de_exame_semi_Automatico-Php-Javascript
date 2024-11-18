<?php require_once('../includes/funcoes.php') ?>

<?php
if (!loggedAdmin()) {
    header('location:../index.php');
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['submit'])) {
    adicionarTurma();
}

if (isset($_POST['update'])) {
    editTurma();
}

if (isset($_POST['delete']) && isset($_POST['id_turma'])) {
    deleteTurma($_POST['id_turma']);
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Turma</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Turmas disponíveis
                            </div>
                            <div class="col-md-3 offset-md-3">
                                <form action="" method="post">
                                    <div class="input-group input-group-sm">
                                        <input class="form-control" type="text" name="turma" placeholder="Adicionar Turma...">
                                        <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" name="submit" type="submit"><i class="fas fa-paper-plane"></i> Guardar</button>
                                        </div>
                                    </div>
                                </form>
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
                                            <th>Modificar</th>
                                            <th>Ver Alunos</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($turmas as $turma) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo $turma['total_aluno'] ?></td>
                                                <td>
                                                    <i class="fas fa-trash mx-1 hover-pointer" data-toggle="modal" data-target="#deleteModal" data-id-turma="<?php echo $turma['id'] ?>"></i>
                                                    <i class="fas fa-edit mx-1 hover-pointer" data-toggle="modal" data-target="#editModal" data-id-turma="<?php echo $turma['id'] ?>" data-nome-turma="<?php echo htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8'); ?>">
                                                    </i>
                                                </td>
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

<!-- Modal -->
<div class="modal fade" id="deleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Tem certeza?</h5>
                <span aria-hidden="true" class="close hover-pointer" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <div class="modal-body">
            Ele excluirá permanentemente a turma inteira com todos os dados associados.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form action="" method="post">
                    <input type="hidden" name="id_turma">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                    <button type="submit" name="delete" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Editar Turma</h5>
                <span aria-hidden="true" type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_turma">
                    <div class="form-group">
                        <label for="" class="small">Nome da Turma</label>
                        <input type="text" name="nome_turma" class="form-control" placeholder="Nome da urma">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" name="update" class="btn btn-primary">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });

    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id_turma = button.data('id-turma')
        var modal = $(this)
        modal.find('input[name="id_turma"]').val(id_turma)
    })

    $('#editModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var modal = $(this)
        modal.find('input[name="id_turma"]').val(button.data('id-turma'))
        modal.find('input[name="nome_turma"]').val(button.data('nome-turma'))
    })
</script>

<?php require_once('layouts/fim.php') ?>