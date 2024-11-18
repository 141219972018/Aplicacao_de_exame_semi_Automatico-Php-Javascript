<?php require_once('../includes/funcoes.php') ?>

<?php
if (empty($_GET['exame'])) {
    header('location:index.php');
} else {
    $id_exame= $_GET['exame'];
}
if (!professorConectado()) {
    header('location:../index.php');
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['submit'])) {
    addPergunta();
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
                <h1 class="mt-3 h5">
                    <span class="badge badge-pill badge-primary">Adicionar perguntas</span>
                    <a href="ver_perguntas.php?exame=<?php echo $id_exame?>" class="h5">
                        <?php
                        $exame = getExame($id_exame);
                        echo htmlspecialchars($exame['nome_exame'], ENT_QUOTES, 'UTF-8');
                        ?>
                    </a>
                </h1>

                <div class="card mt-3 mb-4">
                    <div class="card-body">
                        <form method="post" action="" enctype="multipart/form-data">
                            <input type="hidden" name="id_exame" value="<?php echo $id_exame?>">
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                    <label for="pergunta" class="small">Pergunta</label>
                                    <textarea class="form-control editor" name="pergunta" id="pergunta" rows="3"><?php echo isset($_POST['pergunta']) ? htmlspecialchars($_POST['pergunta'], ENT_QUOTES, 'UTF-8') : null ?></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="opcao_a" class="small">Opção A</label>
                                    <textarea class="form-control editor" name="opcao_a" id="opcao_a" rows="3"><?php echo isset($_POST['opcao_a']) ? htmlspecialchars($_POST['opcao_a'], ENT_QUOTES, 'UTF-8') : null ?></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="opcao_b" class="small">Opção B</label>
                                    <textarea class="form-control editor" name="opcao_b" id="opcao_b" rows="3"><?php echo isset($_POST['opcao_b']) ? htmlspecialchars($_POST['opcao_b'], ENT_QUOTES, 'UTF-8') : null ?></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="opcao_c" class="small">Opção C</label>
                                    <textarea class="form-control editor" name="opcao_c" id="opcao_c" rows="3"><?php echo isset($_POST['opcao_c']) ? htmlspecialchars($_POST['opcao_c'], ENT_QUOTES, 'UTF-8') : null ?></textarea>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="opcao_d" class="small">Opção D</label>
                                    <textarea class="form-control editor" name="opcao_d" id="opcao_d" rows="3"><?php echo isset($_POST['opcao_d']) ? htmlspecialchars($_POST['opcao_d'], ENT_QUOTES, 'UTF-8') : null ?></textarea>
                                </div>
                            </div>
                            <div class="form-row mt-3">
                                <div class="form-group col-md-3">
                                    <select class="form-control" name="opcao_correta" id="exampleFormControlSelect1">
                                        <option disabled selected>Selecione a opção correta</option>
                                        <option value="opcao_a" <?php echo (isset($_POST['opcao_correta']) && ($_POST['opcao_correta'] == 'opcao_a')) ? 'selected' : null ?>>Opção A</option>
                                        <option value="opcao_b" <?php echo (isset($_POST['opcao_correta']) && ($_POST['opcao_correta'] == 'opcao_b')) ? 'selected' : null ?>>Opção B</option>
                                        <option value="opcao_c" <?php echo (isset($_POST['opcao_correta']) && ($_POST['opcao_correta'] == 'opcao_c')) ? 'selected' : null ?>>Opção C</option>
                                        <option value="opcao_d" <?php echo (isset($_POST['opcao_correta']) && ($_POST['opcao_correta'] == 'opcao_d')) ? 'selected' : null ?>>Opção D</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                                    <div class="input-group mb-3">
                                        <input type="text" name="notas" class="form-control" placeholder="notas" value="<?php echo isset($_POST['notas']) ? htmlspecialchars($_POST['notas'], ENT_QUOTES, 'UTF-8') : null ?>">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" name="submit" type="submit" id="button-addon2"><i class="fas fa-paper-plane"></i> Submeter</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

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

<script src="ckeditor/ckeditor.js"></script>

<script>
    $(function() {
        $('.editor').each(function(e) {
            CKEDITOR.replace(this.id, {

            });
        });
    });
</script>


<?php require_once('layouts/fim.php') ?>