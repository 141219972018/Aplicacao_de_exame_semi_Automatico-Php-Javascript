<?php require_once('../includes/funcoes.php') ?>

<?php
    if(!loggedAdmin()){
        header('location:../index.php');
    }
    if(isset($_POST['logout'])){
        logout();
    }
    if(isset($_POST['submit'])){
        addProfessor();
    }
    if(isset($_POST['delete']) && isset($_POST['id_aluno'])){
        deleteAluno($_POST['id_aluno']);
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
                                Alunos Registados Recentemente
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
                                            <th>Nome completo</th>
                                            <th>Nº Aluno</th>
                                            <th>Turma</th>                                           
                                            <th>Nome de Utilizador</th>
                                            <th>Apagar</th>
                                            <th>Perfil</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach($alunos as $aluno) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($aluno['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['numero_aluno'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($aluno['nome_turma'], ENT_QUOTES, 'UTF-8'); ?></td>                                           
                                            <td><?php echo htmlspecialchars($aluno['nome_utilizador'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <i class="fas fa-trash mx-1 hover-pointer" data-toggle="modal" data-target="#deleteModal" data-aluno-id="<?php echo $aluno['id'] ?>"></i>
                                            </td>
                                            
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

<!-- Modal -->
<div class="modal fade" id="deleteModal" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Tem certeza?</h5>
        <span aria-hidden="true" class="close hover-pointer" data-dismiss="modal" aria-label="Close">&times;</span>
      </div>
      <div class="modal-body">
      Vai apagar o aluno e todos os dados relacionados permanentemente.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <form action="" method="post">
            <input type="hidden" name="id_aluno">
            <button type="submit" name="delete" class="btn btn-danger">Apagar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var id_aluno = button.data('id_aluno')
        var modal = $(this)
        modal.find('input[nome="id_aluno"]').val(id_aluno)
    })
</script>

<?php require_once('layouts/fim.php') ?>

