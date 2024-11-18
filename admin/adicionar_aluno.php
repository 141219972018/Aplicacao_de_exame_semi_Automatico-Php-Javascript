<?php require_once('../includes/funcoes.php') ?>

<?php
if (!loggedAdmin()) {
    header('location:../index.php');
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['submit'])) {
    addAluno();
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Adicionar Aluno</span></h1>


                <div class="mt-3 card mb-4">
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="nome" class="small">Nome completo</label>
                                    <input type="text" name="nome" placeholder="Ex. Mblu Júnior" class="form-control" id="nome" value="<?php echo isset($_POST['nome']) ? $_POST['nome'] : null ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="turma" class="small">Turma</label>
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
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="nome_utilizador" class="small">Nome de utilizador</label>
                                    <input type="text" name="nome_utilizador" placeholder="mbalu@example.com" class="form-control" id="nome_utilizador" value="<?php echo isset($_POST['nome_utilizador']) ? $_POST['nome_utilizador'] : null ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="password" class="small">Senha</label>
                                    <input type="password" name="password" placeholder="******" class="form-control" id="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : null ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="numero_aluno" class="small">Número Aluno</label>
                                    <input type="text" name="numero_aluno" placeholder="Número de Aluno" class="form-control" id="rolln0" value="<?php echo isset($_POST['numero_aluno']) ? $_POST['numero_aluno'] : null ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="avatar" class="small">Avatar</label>
                                    <input type="file" name="avatar" class="form-control-file" id="avatar">
                                </div>
                            </div>

                            <input type="hidden" name="csrf_token" value="<?php echo $token ?>">

                            <button type="submit" name="submit" class="btn btn-primary mt-3 px-3"> <i class="fas fa-paper-plane"></i> Guardar</button>

                            <div class="row mt-3">
                                <div class="col">
                                    <?php require_once('../includes/erros_de_formulario.php') ?>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </main>

        <?php require_once('layouts/footer.php') ?>
    </div>
</div>

<?php require_once('layouts/fim.php') ?>