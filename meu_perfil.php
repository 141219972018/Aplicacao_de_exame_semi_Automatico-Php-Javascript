<?php require_once('../includes/funcoes.php') ?>

<?php
if (!professorConectado()) {
    header('location:../index.php');
} else {
    $professor = professorConectado();
}
if (isset($_POST['logout'])) {
    logout();
}
if (isset($_POST['submit'])) {
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
                <h1 class="mt-3 h5"> <span class="badge badge-pill badge-primary"> Meu perfil </span> </h1>

                <div class="card mb-4 mt-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2">
                                <?php
                                if ($professor['avatar']) {
                                    $url = '../uploads/avatars/' . $professor['avatar'];
                                } else {
                                    $url = '../includes/placeholders/150.png';
                                }
                                ?>
                                <img class="img-fluid" src="<?php echo $url; ?>">
                            </div>
                            <div class="col-md-3">
                                <div>Nome: <?php echo htmlspecialchars($professor['nome'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="mt-1">Telemóvel: <?php echo htmlspecialchars($professor['telemovel'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="mt-1">Nome de Utilizador: <?php echo htmlspecialchars($professor['nome_utilizador'], ENT_QUOTES, 'UTF-8') ?></div>
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