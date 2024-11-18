<?php require_once('../includes/funcoes.php') ?>

<?php
if (empty($_GET['exame'])) {
    header('location:index.php');
} else {
    $id_exame= $_GET['exame'];
    $id_aluno = $_GET['aluno'];
    $aluno = getAluno($id_aluno);
    $exame = getExame($id_exame);
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
                    <span class="badge badge-pill badge-primary">Respostas</span>
                    <span class="text-primary h5">
                        <?php
                        echo htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8');
                        ?>
                    </span>
                </h1>

                <?php
                $respostas = getRespostas($id_exame, $id_aluno);
                if ($respostas) {
                    $count = 0;
                    foreach ($respostas as $responder) {
                ?>

                        <div class="card mt-3 mb-4">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        Notas - <?php echo $responder['notas'] ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php echo $responder['pergunta'] ?>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="" class="small">Opção correta</label>
                                        <div><?php echo $responder['opcao_correta'] ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="" class="small">Opção respondida</label>
                                        <div class="<?php echo ($responder['opcao_correta'] == $responder['opcao_respondida']) ? 'text-success' : 'text-danger' ?>"><?php echo $responder['opcao_respondida'] ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    Nenhum registo foi encontrado!.
                <?php
                }
                ?>
            </div>
        </main>

        <?php require_once('layouts/footer.php') ?>
    </div>
</div>


<script src="../sbadmin/js/polyfill.min.js"></script>
<script id="MathJax-script" async src="../sbadmin/js/tex-chtml.js"></script>

<?php require_once('layouts/fim.php') ?>