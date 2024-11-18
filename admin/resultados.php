<?php require_once('../includes/funcoes.php') ?>

<?php
    if(!loggedAdmin()){
        header('location:../index.php');
    }
    if(isset($_POST['logout'])){
        logout();
    }
    if(isset($_POST['id_exame'])){
        makeLive($_POST['id_exame']);
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
                <h1 class="mt-3 h5"><span class="badge badge-pill badge-primary">Resultados</span></h1>

                <div class="card mt-3 mb-4">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <i class="fas fa-table mr-1"></i>
                                Exames criados
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php
                            $listas = getAdminExamesAtribuido();
                            if($listas){
                        ?>
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nome do exame</th>
                                            <th>Nome da turma</th>                                       
                                            <th>Data</th>
                                            <th>Resultados</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach($listas as $lista) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($lista['nome_exame'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td><?php echo htmlspecialchars($lista['nome_turma'], ENT_QUOTES, 'UTF-8'); ?></td>                                       
                                            <td><?php echo htmlspecialchars($lista['data'], ENT_QUOTES, 'UTF-8'); ?></td>
                                            <td>
                                                <a href="turma_resultados.php?exame=<?php echo $lista['id_exame'] ?>&&turma=<?php echo $lista['id_turma'] ?>" turma="mx-1">Visualizar</a>
                                            </td>
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

