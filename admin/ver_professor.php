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
    if(isset($_POST['update'])){
        editProfessor();
    }
    if(isset($_POST['delete']) && isset($_POST['id_professor'])){
        deleteProfessor($_POST['id_professor']);
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Professores</h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <i class="fas fa-table mr-1"></i>
                        Mesa do Professor
                    </div>
                    <div class="card-body">
                        <?php
                            $professors = todosProfessores();
                            if($professors){
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nome completo</th>
                                            <th>Telemóvel</th>
                                            <th>Nome de Utilizador</th>
                                            <th>Apagar</th>
                                            <th>Perfil</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach($professors as $professor) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($professor['nome'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($professor['telemovel'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($professor['nome_utilizador'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <i class="fas fa-trash mx-1 hover-pointer" data-toggle="modal" data-target="#deleteModal" data-professor-id="<?php echo $professor['id'] ?>"></i>
                                            </td>
                                            
                                            <td><a href="perfil_professor.php?professor=<?php echo $professor['id'] ?>">Ver</a></td>
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
      Isso vai apagar o professor permanentemente.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <form action="" method="post">
            <input type="hidden" name="id_professor">
            <button type="submit" name="delete" class="btn btn-danger">Apagar</button>
        </form>
      </div>
    </div>
  </div>
</div>


<script>
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var id_professor = button.data('professor-id')
        var modal = $(this)
        modal.find('input[nome="id_professor"]').val(id_professor)
    })
</script>

<?php require_once('layouts/fim.php') ?>

