<?php require_once('../includes/funcoes.php') ?>

<?php
    if(empty($_GET['turma'])){
        header('location:index.php');
    } else{
        $id_turma = $_GET['turma'];
        if(isDataExists('turmas', 'id', $id_turma)){
            $turma = getTurma($id_turma);
        } else{
            header('location:index.php');
        }
    }
    if(!loggedAdmin()){
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
                <h1 class="mt-3 h5"> <span class="badge badge-pill badge-primary"><?php echo htmlspecialchars($turma['nome'], ENT_QUOTES, 'UTF-8'); ?> </span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Alunos
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                            $alunos = alunosdaTurma($id_turma);
                            if($alunos){
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Número Aluno</th>                                         
                                            <th>Nome de Utilizador</th>
                                            <th>Perfil</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach($alunos as $aluno) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['numero_aluno'], ENT_QUOTES, 'UTF-8'); ?></td>                                         
                                            <td><?php echo htmlspecialchars($aluno['nome_utilizador'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><a href="perfil_aluno.php?aluno=<?php echo $aluno['id'] ?>">Visita</a></td>
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

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>

<?php require_once('layouts/fim.php') ?>

