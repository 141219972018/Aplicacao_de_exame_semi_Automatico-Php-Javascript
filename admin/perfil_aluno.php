<?php require_once('../includes/funcoes.php') ?>

<?php
if (!loggedAdmin()) {
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
if (isset($_POST['update'])) {
    editAluno();
}
if (isset($_POST['changePassword'])) {
    changeStudentPasswordByAdmin();
}
if (isset($_POST['delete']) && isset($_POST['id_aluno'])) {
    deleteAluno($_POST['id_aluno']);
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary"> Perfil </span></h1>
                <div class="card mb-4">
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
                                <div>Nome: <?php echo htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mt-1">Turma: <?php echo htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mt-1">Número: <?php echo htmlspecialchars($aluno['numero_aluno'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mt-1">Nome de Utilizador: <?php echo htmlspecialchars($aluno['nome_utilizador'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mt-1">
                                    <span class="text-primary hover-pointer" data-toggle="modal" data-target="#editModal">Editar perfil</span> /
                                    <span class="text-info hover-pointer" data-toggle="modal" data-target="#changePasswordModal">Alterar a senha</span>
                                </div>
                                <div class="mt-1"><span class="text-danger hover-pointer" data-toggle="modal" data-target="#deleteModal">Excluir Aluno</span></div>
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
                                                <td><?php echo htmlspecialchars($resultado['nome_exame'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($resultado['total_notas'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($resultado['obtido'], ENT_QUOTES, 'UTF-8'); ?></td>
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

<!-- Modal -->
<div class="modal fade" id="deleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Tem certeza?</h5>
                <span aria-hidden="true" class="close hover-pointer" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <div class="modal-body">
            Ele excluirá o aluno e todos os dados relacionados permanentemente.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form action="" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                    <input type="hidden" name="id_aluno" value="<?php echo $aluno['id'] ?>">
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
                <h5 class="modal-title" id="staticBackdropLabel">Editar Aluno</h5>
                <span aria-hidden="true" type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <form action="" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_aluno" value="<?php echo $aluno['id'] ?>">
                    <div class="form-group">
                        <label for="" class="small">Nome do Aluno</label>
                        <input type="text" name="nome" value="<?php echo htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Nome do aluno">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Turma</label>
                        <select class="form-control" id="turma" name="id_turma">
                            <option disabled selected>selecionar</option>
                            <?php
                            $turmas = todasTurmas();
                            if ($turmas) {
                                foreach ($turmas as $turma) {
                                    $selected = null;
                                    if ($aluno['id_turma'] == $turma['id']) {
                                        $selected = 'selected';
                                    }
                                    echo "<option value='{$turma['id']}' {$selected}>" . htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8') . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Número Aluno</label>
                        <input type="text" value="<?php echo htmlspecialchars($aluno['numero_aluno'], ENT_QUOTES, 'UTF-8') ?>" name="numero_aluno" class="form-control" placeholder="Número Aluno">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Nome de Utilizador</label>
                        <input type="text" value="<?php echo htmlspecialchars($aluno['nome_utilizador'], ENT_QUOTES, 'UTF-8') ?>" name="nome_utilizador" class="form-control" placeholder="nome_utilizador">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Avatar</label>
                        <input type="file" name="avatar" class="form-control-file">
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

<!-- Modal -->
<div class="modal fade" id="changePasswordModal" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Alterar a senha</h5>
                <span aria-hidden="true" type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <form action="" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_aluno" value="<?php echo $aluno['id'] ?>">
                    <div class="form-group">
                        <label for="" class="small">Nova senha</label>
                        <input type="password" name="password" class="form-control" placeholder="Digite a nova senha">
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" name="changePassword" class="btn btn-primary">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>

</script>

<?php require_once('layouts/fim.php') ?>