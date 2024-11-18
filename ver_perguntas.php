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
if (isset($_POST['delete']) && isset($_POST['id_pergunta'])) {
    deletePergunta($_POST['id_pergunta']);
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
                    <span class="badge badge-pill badge-primary">Perguntas</span>
                    <span class="text-primary h5">
                        <?php
                        $exame = getExame($id_exame);
                        echo htmlspecialchars($exame['nome_exame'], ENT_QUOTES, 'UTF-8');
                        ?>
                    </span>
                </h1>

                <?php
                $perguntas = verPerguntas($id_exame);
                if ($perguntas) {
                    $count = 0;
                    foreach ($perguntas as $pergunta) {
                        $opcao_correta = $pergunta['opcao_correta'];
                ?>

                        <div class="card mt-3 mb-4">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        Pergunta - <?php echo ++$count ?> |
                                        Nota - <?php echo $pergunta['notas'] ?>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-end text-dark">
                                        <a href="editar_perguntas.php?pergunta=<?php echo $pergunta['id'] ?>"><i class="fas fa-edit mx-1"></i></a>
                                        <a><i class="fas fa-trash mx-1 hover-pointer" data-toggle="modal" data-target="#deleteModal" data-pergunta-id="<?php echo $pergunta['id'] ?>"></i></a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <?php echo $pergunta['pergunta'] ?>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="" class="small <?php echo ($opcao_correta == 'opcao_a') ? 'badge badge-success font-weight-normal' : null ?>">Opção A</label>
                                        <div><?php echo $pergunta['opcao_a'] ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="" class="small <?php echo ($opcao_correta == 'opcao_b') ? 'badge badge-success font-weight-normal' : null ?>">Opção B</label>
                                        <div><?php echo $pergunta['opcao_b'] ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="" class="small <?php echo ($opcao_correta == 'opcao_c') ? 'badge badge-success font-weight-normal' : null ?>">Opção C</label>
                                        <div><?php echo $pergunta['opcao_c'] ?></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="" class="small <?php echo ($opcao_correta == 'opcao_d') ? 'badge badge-success font-weight-normal' : null ?>">Opção D</label>
                                        <div><?php echo $pergunta['opcao_d'] ?></div>
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

<!-- Modal -->
<div class="modal fade" id="deleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Tem certeza?</h5>
                <span aria-hidden="true" class="close hover-pointer" data-dismiss="modal" aria-label="Close">&times;</span>
            </div>
            <div class="modal-body">
            Ele excluirá esta pergunta, incluindo todos os dados vinculados.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form action="" method="post">
                    <input type="hidden" name="id_pergunta">
                    <button type="submit" name="delete" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../sbadmin/js/polyfill.min.js"></script>
<script id="MathJax-script" async src="../sbadmin/js/tex-chtml.js"></script>

<script>
    $('#deleteModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget)
        var id_pergunta = button.data('pergunta-id')
        var modal = $(this)
        modal.find('input[nome="id_pergunta"]').val(id_pergunta)
    })
</script>


<?php require_once('layouts/fim.php') ?>