<?php require_once('../includes/funcoes.php') ?>

<?php
if (isset($_POST['logout'])) {
    logout();
}
if (!loggedAdmin()) {
    header('location:../index.php');
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
                <div class="row mt-4">
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-primary text-white mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col h4">
                                        Alunos
                                    </div>
                                    <div class="col d-flex justify-content-end">
                                        <i class="fas fa-user-graduate fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="ver_aluno.php">Ver detalhes</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-warning text-white mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col h4">
                                        Professores
                                    </div>
                                    <div class="col d-flex justify-content-end">
                                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="ver_professor.php">Ver detalhes</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-success text-white mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col h4">
                                        Exame
                                    </div>
                                    <div class="col d-flex justify-content-end">
                                        <i class="fas fa-align-right fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="exame.php">Ver detalhes</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="card bg-danger text-white mb-4">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col h4">
                                        Resultados
                                    </div>
                                    <div class="col d-flex justify-content-end">
                                        <i class="fas fa-poll fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between">
                                <a class="small text-white stretched-link" href="resultados.php">Ver detalhes</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table mr-1"></i>
                                Alunos Recentemente Registados
                            </div>
                            <div class="card-body">
                                <?php
                                $alunosRecentes = alunosRecentes();
                                $alunosRecentes = array_slice($alunosRecentes, 0, 6, true);
                                if (count($alunosRecentes)) {
                                ?>
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Número</th>
                                                <th scope="col">Nome</th>                                                
                                                <th scope="col">Turmas</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($alunosRecentes as $aluno) {
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($aluno['numero_aluno'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8'); ?></td>                                                    
                                                    <td><?php echo htmlspecialchars($aluno['nome_turma'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table mr-1"></i>
                                Professores Recentemente Registados 
                            </div>
                            <div class="card-body">
                                <?php
                                $recentesProfessores = recentesProfessores();
                                $recentesProfessores = array_slice($recentesProfessores, 0, 6, true);
                                if (count($recentesProfessores)) {
                                ?>
                                    <table class="table table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th scope="col">Nome</th>
                                                <th scope="col">Telemóvel</th>
                                                <th scope="col">Nome de Utilizador</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($recentesProfessores as $professor) {
                                            ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($professor['nome'], ENT_QUOTES, 'UTF-8');  ?></td>
                                                    <td><?php echo htmlspecialchars($professor['telemovel'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                    <td><?php echo htmlspecialchars($professor['nome_utilizador'], ENT_QUOTES, 'UTF-8'); ?></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php require_once('layouts/footer.php') ?>
    </div>
</div>

<?php require_once('layouts/fim.php') ?>