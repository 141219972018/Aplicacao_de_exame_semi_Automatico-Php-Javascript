<?php require_once('../includes/funcoes.php') ?>

<?php
    if(!professorConectado()){
        header('location:../index.php');
    }
    if(isset($_POST['logout'])){
        logout();
    }
    if(isset($_POST['submit'])){
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Alunos</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Alunos recentemente registados
                            </div>
                            <div class="col-md-3 offset-md-3">
                                <form action="alunos_turma.php" method="get">
                                    <div class="input-group input-group-sm">
                                    <select class="form-control" name="turma">
                                        <option disabled selected>selecionar</option>
                                        <?php 
                                            $turmas = todasTurmas();
                                            if($turmas){
                                                foreach($turmas as $turma){
                                                    $selected = null;
                                                    if(isset($_POST['id_turma'])){
                                                        if($_POST['id_turma']==$turma['id']){
                                                            $selected = 'selected';
                                                        }
                                                    }
                                                    echo "<option value='{$turma['id']}' {$selected}>".htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8')."</option>";
                                                }
                                                
                                            }
                                        ?>
                                    </select>
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit"><i class="fas fa-eye"></i> Visualizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                            $alunos = alunosRecentes();
                            if($alunos){
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Nº Aluno</th>
                                            <th>Turma</th>                                           
                                            <th>Nome de Utilizador</th>
                                            <th>Perfil</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach($alunos as $aluno) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?php echo htmlspecialchars($aluno['numero_aluno'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><?php echo htmlspecialchars($aluno['nome_turma'], ENT_QUOTES, 'UTF-8') ?></td>                                           
                                            <td><?php echo htmlspecialchars($aluno['nome_utilizador'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><a href="perfil_aluno.php?aluno=<?php echo $aluno['id'] ?>">Ver</a></td>
                                        </tr>   
                                        <?php } ?>                                        
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                
            </div>
        </main>
        
        <?php require_once('layouts/footer.php') ?>
    </div>
</div>

<?php require_once('layouts/fim.php') ?>

