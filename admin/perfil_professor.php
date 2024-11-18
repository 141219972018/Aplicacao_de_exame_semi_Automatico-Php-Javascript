<?php require_once('../includes/funcoes.php') ?>

<?php
if (!loggedAdmin()) {
    header('location:../index.php');
}
if (!isset($_GET['professor'])) {
    header('location:index.php');
} else {
    $professor = getProfessor($_GET['professor']);
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['update'])) {
    editpPofessor();
}
if (isset($_POST['changePassword'])) {
    changeTeacherPasswordByAdmin();
}
if (isset($_POST['delete']) && isset($_POST['id_professor'])) {
    deleteProfessor($_POST['id_professor']);
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
                                if ($professor['avatar']) {
                                    $url = '../uploads/avatars/' . $professor['avatar'];
                                } else {
                                    $url = '../includes/placeholders/150.png';
                                }
                                ?>
                                <img class="img-fluid" src="<?php echo $url; ?>">
                            </div>
                            <div class="col-md-3">
                                <div>Nome: <?php echo htmlspecialchars($professor['nome'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mt-1">Telemóvel: <?php echo htmlspecialchars($professor['telemovel'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mt-1">Nome de Utilizador: <?php echo htmlspecialchars($professor['nome_utilizador'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="mt-1">
                                    <span class="text-primary hover-pointer" data-toggle="modal" data-target="#editModal">Editar perfil</span> /
                                    <span class="text-info hover-pointer" data-toggle="modal" data-target="#changePasswordModal">Alterar a senha</span>
                                </div>
                                <div class="mt-1"><span class="text-danger hover-pointer" data-toggle="modal" data-target="#deleteModal">Excluir professor</span></div>
                            </div>
                            <div class="col-md-7">
                                <?php
                                $professorExames = professorExames($professor['id']);
                                if ($professorExames) {
                                ?>
                                    <table class="table table-bordered">
                                        <tr class="bg-light">
                                            <th>Exame</th>
                                            <th>Data</th>
                                            <th>Está ao vivo</th>
                                        </tr>
                                        <?php foreach ($professorExames as $exame) { ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($exame['nome_exame'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td><?php echo htmlspecialchars($exame['data'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                <td>
                                                    <?php
                                                    if ($exame['esta_ativo']) {
                                                        echo '<i class="fas fa-circle text-success"></i>';
                                                    } else {
                                                        echo '<i class="fas fa-circle text-black-50"></i>';
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
            Ele excluirá o professor e todos os dados relacionados permanentemente.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form action="" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                    <input type="hidden" name="id_professor" value="<?php echo $professor['id'] ?>">
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
                <h5 class="modal-title" id="staticBackdropLabel">Editar professor</h5>
                <span aria-hidden="true" type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <form action="" enctype="multipart/form-data" method="post">
                <div class="modal-body">
                    <input type="hidden" name="id_professor" value="<?php echo $professor['id'] ?>">
                    <div class="form-group">
                        <label for="" class="small">Nome do professor</label>
                        <input type="text" name="nome" value="<?php echo htmlspecialchars($professor['nome'], ENT_QUOTES, 'UTF-8'); ?>" class="form-control" placeholder="Nome do Professore">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Telemóvel</label>
                        <input type="text" value="<?php echo $professor['telemovel'] ?>" name="telemovel" class="form-control" placeholder="Nº de telemóvel">
                    </div>
                    <div class="form-group">
                        <label for="" class="small">Nome de Utilizador</label>
                        <input type="text" value="<?php echo htmlspecialchars($professor['nome_utilizador'], ENT_QUOTES, 'UTF-8'); ?>" name="nome_utilizador" class="form-control" placeholder="Nome de utilizador">
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
                    <input type="hidden" name="id_professor" value="<?php echo $professor['id'] ?>">
                    <div class="form-group">
                        <label for="" class="small">Nova Senha</label>
                        <input type="password" name="password" class="form-control" placeholder="Senha">
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


<?php require_once('layouts/fim.php') ?>