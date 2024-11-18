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
if (isset($_POST['submit'])) {
    assignExam();
}
if (isset($_POST['delete']) && isset($_POST['id_exame']) && isset($_POST['id_turma'])) {
    deleteAssignedExam($_POST['id_exame'], $_POST['id_turma']);
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Atribuir exame</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="nome" class="small">Selecione o Exame</label>
                                    <select class="form-control" id="exame" name="id_exame">
                                        <option disabled selected>Selecionar</option>
                                        <?php
                                        $exames = professorExames($id_utilizador);
                                        if ($exames) {
                                            foreach ($exames as $exame) {
                                                $selected = null;
                                                if (isset($_POST['id_exame'])) {
                                                    if ($_POST['id_exame'] == $exame['id']) {
                                                        $selected = 'selected';
                                                    }
                                                }
                                                echo "<option value='{$exame['id']}' {$selected}>" . htmlspecialchars($exame['nome_exame'], ENT_QUOTES, 'UTF-8') . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                                    <label for="nome" class="small">Selecione a Turma</label>
                                    <div class="input-group mb-3">
                                        <select class="form-control" id="turma" name="id_turma">
                                            <option disabled selected>selecionar</option>
                                            <?php
                                            $turmas = todasTurmas();
                                            if ($turmas) {
                                                foreach ($turmas as $turma) {
                                                    $selected = null;
                                                    if (isset($_POST['id_turma'])) {
                                                        if ($_POST['id_turma'] == $turma['id']) {
                                                            $selected = 'selected';
                                                        }
                                                    }
                                                    echo "<option value='{$turma['id']}' {$selected}>" . htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') . "</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary px-3" name="submit" type="submit"><i class="fas fa-paper-plane"></i> Atribuir</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col">
                                    <?php require_once('../includes/erros_de_formulario.php') ?>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Exames Atribuídos
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                        $axamesAtribuidos = getExamesAtribuidos();
                        if ($axamesAtribuidos) {
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Exame</th>
                                            <th>Turma</th>
                                            <th>Perguntas</th>
                                            <th>Excluir</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($axamesAtribuidos as $atribuir) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($atribuir['nome_exame'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?php echo htmlspecialchars($atribuir['nome_turma'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><a href="ver_perguntas.php?exame=<?php echo $atribuir['id_exame'] ?>">Ver perguntas</a></td>
                                                <td><i class="fas fa-trash hover-pointer" data-toggle="modal" data-target="#deleteModal" data-exame-id="<?php echo $atribuir['id_exame'] ?>" data-id-turma="<?php echo $atribuir['id_turma'] ?>"></i></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php
                        } else {
                        ?>
                            Nenhum registo foi encontrado!
                        <?php
                        }
                        ?>
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
            Ele excluirá o exame atribuído à turma.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form action="" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                    <input type="hidden" name="id_exame">
                    <input type="hidden" name="id_turma">
                    <button type="submit" name="delete" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id_exame= button.data('exame-id')
        var id_turma = button.data('id-turma')
        var modal = $(this)
        modal.find('input[nome="id_exame"]').val(id_exame)
        modal.find('input[nome="id_turma"]').val(id_turma)
    })
</script>

<?php require_once('layouts/fim.php') ?>