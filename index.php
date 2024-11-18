<?php
require_once('includes/funcoes.php');
if (isset($_POST['login'])) {
    login();
}
?>

<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Projecto de Final de Curso" />
    <meta name="author" content="Mbalu Júnior" />
    <title>Exame Semi-Automático</title>
    <link href="sbadmin/css/styles.css" rel="stylesheet" />
    <script src="/sbadmin/js/all.min.js" crossorigin="anonymous"></script>
</head>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4">Login Exame Semi-Automático</h3>
                                </div>
                                <div class="card-body">
                                    <form action="" method="post">
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputFuncao">Fução</label>
                                            <select class="form-control" name="funcao" id="inputFuncao">
                                                <option disabled selected>Selecione a função <!---funcao(função)---></option>
                                                <option value="professor" <?php echo (isset($_POST['funcao']) && $_POST['funcao'] == 'professor') ? 'selected' : null ?>>Professor</option>
                                                <option value="aluno" <?php echo (isset($_POST['funcao']) && $_POST['funcao'] == 'aluno') ? 'selected' : null ?>>Aluno</option>                                                
                                                <option value="admin" <?php echo (isset($_POST['funcao']) && $_POST['funcao'] == 'admin') ? 'selected' : null ?>>Admin</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputnome_utilizador">Nome de Utilizador</label>
                                            <input class="form-control" name="nome_utilizador" value="<?php echo isset($_POST['nome_utilizador']) ? $_POST['nome_utilizador'] : null ?>" id="inputnome_utilizador" type="text" placeholder="Digite o nome de Utilizador" />
                                        </div>
                                        <div class="form-group">
                                            <label class="small mb-1" for="inputPassword">Senha</label>
                                            <input class="form-control" name="password" value="<?php echo isset($_POST['password']) ? $_POST['password'] : '' ?>" id="inputPassword" type="password" placeholder="Digite a Senha" />
                                        </div>
                                        <div class="form-group d-flex justify-content-center mt-4 mb-0">
                                            <button class="btn btn-primary px-5" type="submit" name="login"> <i class="fas fa-sign-in-alt"></i> Login</button>
                                        </div>
                                        <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
                                    </form>

                                    <div class="row mt-3">
                                        <div class="col">
                                            <?php require_once('includes/erros_de_formulario.php') ?>
                                        </div>
                                    </div>

                                </div>
                                <div class="card-footer text-center">
                                    <div class="small"><a href="">Exame Online</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">&copy; 2023 Mbalu Júnior</div>
                        <div>
                            <a href="#">Política de Privacidade</a>
                            &middot;
                            <a href="#">Termos e Condições</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</body>

</html>