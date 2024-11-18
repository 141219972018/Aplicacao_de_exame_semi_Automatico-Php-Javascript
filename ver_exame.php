<?php require_once('../includes/funcoes.php') ?>

<?php
if (!professorConectado()) {
    header('location:../index.php');
} else {
    $id_utilizador = $_SESSION['id_utilizador'];
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['update'])) {
    atualizarExame();
}
if (isset($_POST['delete']) && isset($_POST['id_exame'])) {
    excluirProfessordeExame($_POST['id_exame']);
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
                <h1 class="mt-3 h5"> <span class="badge badge-pill badge-primary"> Exames </span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Exames criados
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mt-3">
                            <div class="col">
                                <?php require_once('../includes/erros_de_formulario.php') ?>
                            </div>
                        </div>
                        <?php
                        $exames = professorExames($id_utilizador);
                        if ($exames) {
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nome do exame</th>
                                            <th>Tempo total</th>
                                            <th>Total de perguntas</th>
                                            <th>Total de notas</th>
                                            <th>Nota de aprovação</th>
                                            <th>Data</th>
                                            <th>Ao vivo</th>
                                            <th>Modificar</th>
                                            <th>Perguntas</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($exames as $exame) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($exame['nome_exame'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo $exame['tempo_total'] ?></td>
                                                <td><?php echo $exame['total_de_perguntas'] ?></td>
                                                <td><?php echo $exame['total_notas'] ?></td>
                                                <td><?php echo $exame['passar_notas'] ?></td>
                                                <td><?php echo $exame['data'] ?></td>
                                                <td>
                                                    <?php
                                                    if ($exame['esta_ativo']) {
                                                        echo '<i class="fas fa-circle text-success"></i>';
                                                    } else {
                                                        echo '<i class="fas fa-circle text-black-50"></i>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <i class="fas fa-edit mx-1 hover-pointer" data-toggle="modal" data-target="#editModal" data-exame-id="<?php echo $exame['id'] ?>" data-exame-nome="<?php echo htmlspecialchars($exame['nome_exame'], ENT_QUOTES, 'UTF-8') ?>" data-total-tempo="<?php echo $exame['tempo_total'] ?>" data-total-perguntas="<?php echo $exame['total_de_perguntas'] ?>" data-total-notas="<?php echo $exame['total_notas'] ?>" data-pass-notas="<?php echo $exame['passar_notas'] ?>" data-exame-data="<?php echo $exame['data'] ?>">
                                                    </i>
                                                    <i class="fas fa-trash mx-1 hover-pointer" data-toggle="modal" data-target="#deleteModal" data-exame-id="<?php echo $exame['id'] ?>"></i>
                                                </td>

                                                <td>
                                                    <a href="adicionar_perguntas.php?exame=<?php echo $exame['id'] ?>" class="mx-1"><i class="fas fa-plus-circle" title="Atribuir perguntas e Respostas"></i></a>
                                                    <a href="ver_perguntas.php?exame=<?php echo $exame['id'] ?>" class="mx-1" title="Ver perguntas e Respostas">visualizar</a>
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
<div class="modal fade" id="deleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Tem certeza?</h5>
                <span aria-hidden="true" class="close hover-pointer" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <div class="modal-body">
            Ele excluirá todos os dados do exame, incluindo respostas e resultados de todos os alunos, se houver.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form action="" method="post">
                    <input type="hidden" name="id_exame">
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
                <h5 class="modal-title" id="staticBackdropLabel">Editar exame</h5>
                <span aria-hidden="true" type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_exame">
                    <div class="form-group">
                        <label for="" class="small">Nome do exame</label>
                        <input type="text" name="nome_exame" class="form-control" placeholder="Nome do exame">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Tempo total</label>
                        <input type="text" name="tempo_total" class="form-control" placeholder="Tempo total">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Total de perguntas</label>
                        <input type="text" name="total_de_perguntas" class="form-control" placeholder="Total de perguntas">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Total de notas</label>
                        <input type="text" name="total_notas" class="form-control" placeholder="Total de notas">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Nota de aprovação</label>
                        <input type="text" name="passar_notas" class="form-control" placeholder="Notas de aprovação">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Data do exame</label>
                        <input type="date" name="data_exame" class="form-control" placeholder="data">
                    </div>
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" name="update" class="btn btn-primary">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id_exame= button.data('exame-id')
        var modal = $(this)
        modal.find('input[name="id_exame"]').val(id_exame)
    })

    $('#editModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var modal = $(this)
        modal.find('input[name="id_exame"]').val(button.data('exame-id'))
        modal.find('input[name="nome_exame"]').val(button.data('exame-nome'))
        modal.find('input[name="tempo_total"]').val(button.data('total-tempo'))
        modal.find('input[name="total_de_perguntas"]').val(button.data('total-perguntas'))
        modal.find('input[name="total_notas"]').val(button.data('total-notas'))
        modal.find('input[name="passar_notas"]').val(button.data('pass-notas'))
        modal.find('input[name="data_exame"]').val(button.data('exame-data'))
    })
</script>

<?php require_once('layouts/fim.php') ?>