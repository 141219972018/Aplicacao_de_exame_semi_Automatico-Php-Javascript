<?php require_once('../includes/funcoes.php') ?>

<?php
if (!loggedAdmin()) {
    header('location:../index.php');
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['submit'])) {
    addProfessor();
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Adicionar professor</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="nome" class="small">Nome Completo</label>
                                    <input type="text" name="nome" placeholder="Digite aqui o nome completo" class="form-control" id="nome" value="<?php echo isset($_POST['nome']) ? $_POST['nome'] : null ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="telemovel" class="small">Telemóvel</label>
                                    <input type="number" name="telemovel" placeholder="Digita aqui um nº de telemóvel de 9 digito" class="form-control" id="telemovel" value="<?php echo isset($_POST['telemovel']) ? $_POST['telemovel'] : null ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="nome_utilizador" class="small">Nome de Utilizador</label>
                                    <input type="text" name="nome_utilizador" placeholder="Exemplo: mbalu@example.com" class="form-control" id="nome_utilizador" value="<?php echo isset($_POST['nome_utilizador']) ? $_POST['nome_utilizador'] : null ?>">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="password" class="small">Senha</label>
                                    <input type="password" name="password" placeholder="digita aqui uma senha de 6 digitos" class="form-control" id="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : null ?>">
                                </div>
                            </div>
                            <div class="form-row">
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