<?php require_once('../includes/funcoes.php') ?>

<?php
if (empty($_GET['id'])) {
    header('location:index.php');
} else {
    if (!alunoConectado()) {
        header('location:../index.php');
    } else {
        $aluno = alunoConectado();
        $id_turma = $aluno['id_turma'];
        $id_exame = $_GET['id'];
        $perguntas = getPerguntas($id_exame);
        $respostas = getRespostas($id_exame, $_SESSION['id_utilizador']);
        iniciarTemporizador($id_exame);
        if (temporizadorRestante($id_exame) <= 0) {
            header('location:index.php');}
    }
}
if (isset($_POST['logout'])) {
    logout();
}

?>

<?php require_once('layouts/header.php') ?>

<input type="hidden" id="id_exame" value="<?php echo $id_exame ?>">
<!-- navbar starts -->
<nav class="navbar sb-topnav navbar-expand navbar-dark bg-navy">
    <a class="navbar-brand" href="index.php">Exame S-Automático</a>

    <ul class="navbar-nav mr-auto">
        <li class="nav-item">
            <span class="nav-link"> <i class="fas fa-clock fa-fw"></i> <span id="temporizador">000:00</span> (Tempo Restante)</span>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="resultados.php"><i class="fas fa-chart-bar fa-fw"></i> Resultados</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="document.getElementById('logout-form').submit();"><i class="fas fa-power-off fa-fw"></i> Sair</a>
        </li>
    </ul>

    <form action="" id="logout-form" method="post">
        <input type="hidden" name="logout">
    </form>
</nav>
<!-- navbar fim -->

<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <!-- sidebar início -->
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Perguntas</div>
                    <div class="px-3">
                        <?php
                        if ($perguntas) {
                            $count = 0;
                            foreach ($perguntas as $pergunta) {
                                $opcao_respondida = null;
                                foreach ($respostas as $responder) {
                                    if ($pergunta['id'] == $responder['id_pergunta']) {
                                        $opcao_respondida = $responder['opcao_respondida'];
                                    }
                                }
                        ?>
                                <a href="<?php echo '#' . $pergunta['id'] . '-card' ?>" class="btn btn-sm pergunta-index mb-1 <?php echo (($opcao_respondida != null) && ($opcao_respondida != '')) ? 'btn-success' : 'btn-light' ?>" id="<?php echo $pergunta['id'] . '-pergunta-index' ?>"><?php echo ++$count ?></a>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Conectado como:</div>
                <?php
                $aluno = alunoConectado();
                if ($aluno) {
                    echo htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8');
                }
                ?>
            </div>
        </nav>
        <!-- sidebar ends -->
    </div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid bg-light">
                <div class="row">
                    <div class="col-md-12">
                        <?php
                        if ($perguntas) {
                            $count = 0;
                            foreach ($perguntas as $pergunta) {
                                $opcao_respondida = null;
                                foreach ($respostas as $responder) {
                                    if ($pergunta['id'] == $responder['id_pergunta']) {
                                        $opcao_respondida = $responder['opcao_respondida'];
                                    }
                                }
                        ?>
                                <div id="<?php echo $pergunta['id'] . '-card' ?>" class="card mt-3 mb-4 <?php echo (($opcao_respondida != null) && ($opcao_respondida != '')) ? 'border-success' : null ?>">
                                    <div class="card-header bg-white py-0">
                                        <div class="row">
                                            <div class="col-md-1 py-1 text-left small border-right">
                                                Pergunta - <?php echo ++$count; ?> <br>
                                                Notas - <?php echo $pergunta['notas'] ?>
                                            </div>
                                            <div class="col-md-11 py-1">
                                                <?php echo $pergunta['pergunta'] ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input opcao" type="radio" name="<?php echo $pergunta['id'] ?>" id="<?php echo $pergunta['id'] . '_opcao_a' ?>" value="opcao_a" <?php echo ($opcao_respondida == 'opcao_a') ? 'checked' : null ?>>
                                                    <label class="form-check-label small" for="<?php echo $pergunta['id'] . '_opcao_a' ?>">Opção A</label>
                                                </div>
                                                <div class="mt-3">
                                                    <?php echo $pergunta['opcao_a'] ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input opcao" type="radio" name="<?php echo $pergunta['id'] ?>" id="<?php echo $pergunta['id'] . '_opcao_b' ?>" value="opcao_b" <?php echo ($opcao_respondida == 'opcao_b') ? 'checked' : null ?>>
                                                    <label class="form-check-label small" for="<?php echo $pergunta['id'] . '_opcao_b' ?>">Opção B</label>
                                                </div>
                                                <div class="mt-3">
                                                    <?php echo $pergunta['opcao_b'] ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input opcao" type="radio" name="<?php echo $pergunta['id'] ?>" id="<?php echo $pergunta['id'] . '_opcao_c' ?>" value="opcao_c" <?php echo ($opcao_respondida == 'opcao_c') ? 'checked' : null ?>>
                                                    <label class="form-check-label small" for="<?php echo $pergunta['id'] . '_opcao_c' ?>">Opção C</label>
                                                </div>
                                                <div class="mt-3">
                                                    <?php echo $pergunta['opcao_c'] ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input opcao" type="radio" name="<?php echo $pergunta['id'] ?>" id="<?php echo $pergunta['id'] . '_opcao_d' ?>" value="opcao_d" <?php echo ($opcao_respondida == 'opcao_d') ? 'checked' : null ?>>
                                                    <label class="form-check-label small" for="<?php echo $pergunta['id'] . '_opcao_d' ?>">Opção D</label>
                                                </div>
                                                <div class="mt-3">
                                                    <?php echo $pergunta['opcao_d'] ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input opcao" type="radio" name="<?php echo $pergunta['id'] ?>" id="<?php echo $pergunta['id'] . '_' ?>" value="" <?php echo ($opcao_respondida == '') ? 'checked' : null ?>>
                                                    <label class="form-check-label small" for="<?php echo $pergunta['id'] . '_' ?>">Ignorar esta pergunta</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </main>
        <?php require_once('layouts/footer.php') ?>
    </div>
</div>


<script src="../sbadmin/js/polyfill.min.js"></script>
<script id="MathJax-script" async src="../sbadmin/js/tex-chtml.js"></script>


<script>
    $(document).ready(function() {
        $('.opcao').click(function() {
            var value = $(this).val();
            var idpergunta = $(this).attr('nome');

            $.post("ajax_resposta.php", {
                id_pergunta: idpergunta,
                selected_option: value
            }).done(function(data, textStatus, jqXHR) {
                var response = JSON.parse(data);
                if (response.id_pergunta && response.selected_option) {
                    if (response.selected_option != 'pular') {
                        $('#' + response.id_pergunta + '-pergunta-index').removeClass('btn-light').adicionarTurma('btn-success');
                        $('#' + response.id_pergunta + '-card').adicionarTurma('border-success');
                    } else {
                        $('#' + response.id_pergunta + '-pergunta-index').removeClass('btn-success').adicionarTurma('btn-light');
                        $('#' + response.id_pergunta + '-card').removeClass('border-success');
                    }
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
            });
        });

        //Temporizador
        var examId = $('#id_exame').val();
        var intervalo = setIntervalo(temporizador, 1000);

        function temporizador() {
            $.post("temporizador_ajax.php", {
                id_exame: examId
            }).done(function(data, textStatus, jqXHR) {
                var response = JSON.parse(data);
                if (response.restante <= 0) {
                    clearIntervalo(intervalo);
                    $('#temporizador').text('000:00');
                    Swal.fire({
                        title: 'Tempo acabou!',
                        text: 'A redirecionar para o painel em 5 segundos...',
                        icon: 'warning',
                        temporizador: 5000,
                        confirmButtonText: 'Dashboard',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false
                    }).then(function() {
                        window.location = "index.php";
                    })
                } else {
                    var restante = showTemporizador(response.restante);
                    console.log(restante);
                    $('#temporizador').text(restante);
                }
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
            });
        }

        function showTemporizador(tempo) {
            var minutos = Math.floor(tempo / 60);
            var segundos = tempo - minutos * 60;
            var horaFinal = str_pad_left(minutos, '0', 3) + ':' + str_pad_left(segundos, '0', 2);
            return horaFinal;
        }

        function str_pad_left(string, pad, length) {
            return (new Array(length + 1).join(pad) + string).slice(-length);
        }
    })
</script>

<?php require_once('layouts/fim.php') ?>