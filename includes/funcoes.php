<?php

require_once('db.php');
session_start();

//token csrf
if (empty($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}
$token = $_SESSION['token'];

function login()
{
    $funcao = $nome_utilizador = $password = null;
    global $formErrors;
    $formErrors = array();
    $funcaosArray = array('aluno', 'professor', 'admin');

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if (empty($_POST["funcao"])) {
        array_push($formErrors, 'A Função é obrigatório!');
    } else {
        if (!in_array($_POST['funcao'], $funcaosArray)) {
            array_push($formErrors, 'A função é inválida!');
        }
    }
    if (empty($_POST["nome_utilizador"])) {
        array_push($formErrors, 'O Nome de utilizador é necessário!');
    }
    if (empty($_POST["password"])) {
        array_push($formErrors, 'A Senha é necessária!');
    }
    if (empty($formErrors)) {
        global $conn;
        $funcao = $_POST["funcao"];
        $nome_utilizador = $_POST["nome_utilizador"];
        $password = $_POST["password"];
        $table = $funcao . 's';

        $stmt = $conn->prepare("SELECT * FROM $table WHERE nome_utilizador = ? LIMIT 1");
        $stmt->bind_param('s', $nome_utilizador);
        $stmt->execute();
        if ($stmt) {
            $resultado = $stmt->get_result();
            if ($resultado) {
                $row = $resultado->fetch_assoc();
                if ($row) {
                    $hashed_password = $row['password'];
                    if (password_verify($password, $hashed_password)) {
                        $_SESSION['id_utilizador'] = $row['id'];
                        header('location:' . $funcao . '/index.php');
                    } else {
                        array_push($formErrors, 'Senha é inválida!');
                    }
                } else {
                    array_push($formErrors, 'Nome de utilizador Inválido!');
                }
            } else {
                array_push($formErrors, 'Nome de utilizador ou senha é inválido!');
            }
        } else {
            array_push($formErrors, 'Por favor, tente novamente mais tarde!');
        }
    }
}

function logout()
{
    session_unset();
    session_destroy();
    header('location:../index.php');
}

function loggedAdmin()
{
    if (isset($_SESSION['id_utilizador'])) {
        global $conn;
        $id_utilizador = $_SESSION['id_utilizador'];
        $stmt = $conn->prepare("SELECT * FROM admins WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id_utilizador);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            $row = $resultado->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }
}

function professorConectado()
{
    if (isset($_SESSION['id_utilizador'])) {
        global $conn;
        $id_utilizador = $_SESSION['id_utilizador'];
        $stmt = $conn->prepare("SELECT * FROM professors WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $id_utilizador);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($resultado) {
                $row = $resultado->fetch_assoc();
                return $row;
            } else {
                return false;
            }
        }
    }
}

function alunoConectado()
{
    if (isset($_SESSION['id_utilizador'])) {
        global $conn;
        $id_utilizador = $_SESSION['id_utilizador'];
        $stmt = $conn->prepare("SELECT * FROM alunos WHERE id = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $id_utilizador);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($resultado) {
                $row = $resultado->fetch_assoc();
                return $row;
            } else {
                return false;
            }
        }
    }
}

function addProfessor()
{
    $nome = $telemovel = $nome_utilizador = $password = $avatar = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if (empty($_POST["nome"])) {
        array_push($formErrors, 'O nome é obrigatório!');
    } else {
        if (!preg_match("/^[a-zA-z ]*$/", $_POST["nome"])) {
            array_push($formErrors, 'Somente letras e espaços em branco são permitidos para Nome!');
        }
        if (strlen($_POST["nome"]) > 50) {
            array_push($formErrors, 'O nome pode ter no máximo 50 caracteres!');
        }
    }

    if (empty($_POST["telemovel"])) {
        array_push($formErrors, 'O número do telemóvel é obrigatório!');
    } else {
        if (!preg_match("/^[0-9]*$/", $_POST["telemovel"])) {
            array_push($formErrors, 'Apenas o valor numérico é permitido para o número do telemóvel!');
        }
        if (strlen($_POST["telemovel"]) < 9 || strlen($_POST["telemovel"]) > 9) {
            array_push($formErrors, 'O telemóvel deve ter 9 dígitos!');
        }
        if (isDataExists('professors', 'telemovel', $_POST["telemovel"])) {
            array_push($formErrors, 'O número do telemóvel já existe!');
        }
    }

    if (empty($_POST["nome_utilizador"])) {
        array_push($formErrors, 'Nome de utilizador é necessário!');
    } else {
        if (isDataExists('professors', 'nome_utilizador', $_POST["nome_utilizador"])) {
            array_push($formErrors, 'Nome de utilizador já utilizado!');
        }
        if (strlen($_POST["nome_utilizador"]) < 6 || strlen($_POST["nome_utilizador"]) > 20) {
            array_push($formErrors, 'O nome de utilizador deve ter de 6 a 20 caracteres!');
        }
    }

    if (empty($_POST["password"])) {
        array_push($formErrors, 'Senha necessária!');
    } else {
        if (strlen($_POST["password"]) < 6 || strlen($_POST["password"]) > 20) {
            array_push($formErrors, 'A senha deve ter de 6 a 20 caracteres!');
        }
    }

    if (file_exists($_FILES['avatar']['tmp_name'])) {
        $file_name = $_FILES['avatar']['nome'];
        $file_size = $_FILES['avatar']['size'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_type = $_FILES['avatar']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensions = array("jpeg", "jpg", "png");
        if (in_array($file_ext, $extensions) === false) {
            array_push($formErrors, 'Escolha um arquivo JPEG ou PNG para o avatar.');
        }
        if ($file_size > 200000) {
            array_push($formErrors, 'O tamanho do arquivo deve ser inferior a 200 KB');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $nome = $_POST["nome"];
        $telemovel = $_POST["telemovel"];
        $nome_utilizador = $_POST["nome_utilizador"];
        $password = $_POST["password"];
        $password = password_hash($password, PASSWORD_DEFAULT);
        $directory = "../uploads/avatars/";
        if (isset($file_tmp) && isset($file_name) && isset($file_ext)) {
            $file_name = uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $directory . $file_name);
        } else {
            $file_name = null;
        }

        $stmt = $conn->prepare("INSERT INTO professors(nome, telemovel, nome_utilizador, password, avatar) VALUES(?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param('sisss', $nome, $telemovel, $nome_utilizador, $password, $file_name);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Dados inseridos com sucesso!';
                header('location:adicionar_professor.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
                exit();
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

function editProfessor()
{
    $nome = $id_professor = $telemovel = $nome_utilizador = $password = $avatar = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if (empty($_POST["id_professor"])) {
        array_push($formErrors, 'A identificação do professor é obrigatório!');
    } else {
        if (!isDataExists('professors', 'id', $_POST["id_professor"])) {
            array_push($formErrors, ' A identificação do professor é inválida!');
        } else {
            $professor = getProfessor($_POST["id_professor"]);
        }
    }

    if (empty($_POST["nome"])) {
        array_push($formErrors, 'O nome é obrigatório!');
    } else {
        if (!preg_match("/^[a-zA-z ]*$/", $_POST["nome"])) {
            array_push($formErrors, 'Somente letras e espaços em branco são permitidos para Nome!');
        }
        if (strlen($_POST["nome"]) > 50) {
            array_push($formErrors, 'O nome pode ter no máximo 50 caracteres!');
        }
    }

    if (empty($_POST["telemovel"])) {
        array_push($formErrors, 'O número do telemóvel é obrigatório!');
    } else {
        if ($professor['telemovel'] != $_POST['telemovel']) {
            if (!preg_match("/^[0-9]*$/", $_POST["telemovel"])) {
                array_push($formErrors, 'Apenas o valor numérico é permitido para o número do celular!');
            }
            if (strlen($_POST["telemovel"]) < 10 || strlen($_POST["telemovel"]) > 10) {
                array_push($formErrors, 'O telemóvel deve ter 9 dígitos!');
            }
            if (isDataExists('professors', 'telemovel', $_POST["telemovel"])) {
                array_push($formErrors, 'O número do telemóvel já existe!');
            }
        }
    }

    if (empty($_POST["nome_utilizador"])) {
        array_push($formErrors, 'Nome de utilizador é requerido!');
    } else {
        if ($professor['nome_utilizador'] != $_POST['nome_utilizador']) {
            if (isDataExists('professors', 'nome_utilizador', $_POST["nome_utilizador"])) {
                array_push($formErrors, 'Nome de utilizador já utilizado!');
            }
            if (strlen($_POST["nome_utilizador"]) < 6 || strlen($_POST["nome_utilizador"]) > 20) {
                array_push($formErrors, 'O nome de utilizador deve ter de 6 a 20 caracteres!');
            }
        }
    }

    if (file_exists($_FILES['avatar']['tmp_name'])) {
        $file_name = $_FILES['avatar']['nome'];
        $file_size = $_FILES['avatar']['size'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_type = $_FILES['avatar']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensions = array("jpeg", "jpg", "png");
        if (in_array($file_ext, $extensions) === false) {
            array_push($formErrors, 'Escolha um arquivo JPEG ou PNG para o avatar.');
        }
        if ($file_size > 200000) {
            array_push($formErrors, 'O tamanho do arquivo deve ser inferior a 200 KB');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $id_professor = $_POST['id_professor'];
        $nome = $_POST["nome"];
        $telemovel = $_POST["telemovel"];
        $nome_utilizador = $_POST["nome_utilizador"];

        $directory = "../uploads/avatars/";
        if (isset($file_tmp) && isset($file_name) && isset($file_ext)) {
            $file_name = uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $directory . $file_name);
            unlink('../uploads/avatars/' . $professor['avatar']);
        } else {
            $file_name = $professor['avatar'];
        }

        $stmt = $conn->prepare("UPDATE professors SET nome = ?, telemovel = ?, nome_utilizador = ?, avatar = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('sissi', $nome, $telemovel, $nome_utilizador, $file_name, $id_professor);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Professor atualizado com sucesso!';
                header('location:perfil_professor.php?professor=' . $id_professor);
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

function isDataExists($table, $column, $data)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM $table WHERE $column = ? LIMIT 1");
    $stmt->bind_param('s', $data);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado) {
        $row = $resultado->fetch_assoc();
        if ($row) {
            return true;
        }
    }
    return false;
}

function todosProfessores()
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM professors");
    if ($stmt) {
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            return $resultado;
        }
    }
    return false;
}

function adicionarTurma()
{
    $turma = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if (empty($_POST["turma"])) {
        array_push($formErrors, 'O nome da turma é obrigatório!');
    } else {
        if (isDataExists('turmas', 'nome', $_POST["turma"])) {
            array_push($formErrors, 'A turma já existe!');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $turma = htmlspecialchars($_POST["turma"]);

        $stmt = $conn->prepare("INSERT INTO turmas(nome) VALUES(?)");

        if ($stmt) {
            $stmt->bind_param('s', $turma);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Dados inseridos com sucesso!';
                header('location:turma.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
                exit();
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

function todasTurmas()
{
    global $conn;
    $stmt = $conn->prepare("SELECT turmas.*, COUNT(alunos.id) AS total_aluno FROM turmas LEFT JOIN alunos ON turmas.id=alunos.id_turma GROUP BY turmas.id");
    if ($stmt) {
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            return $resultado;
        }
    }
    return false;
}

function addAluno()
{
    $nome = $id_turma = $nome_utilizador = $password = $avatar = $numero_aluno = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if (empty($_POST["nome"])) {
        array_push($formErrors, 'O nome é obrigatório!');
    } else {
        if (!preg_match("/^[a-zA-z ]*$/", $_POST["nome"])) {
            array_push($formErrors, 'Somente letras e espaços em branco são permitidos para o nome!');
        }
        if (strlen($_POST["nome"]) > 50) {
            array_push($formErrors, 'O nome pode ter no máximo 50 caracteres!');
        }
    }

    if (empty($_POST["id_turma"])) {
        array_push($formErrors, 'a Turma é necessária!');
    } else {
        if (!isDataExists('turmas', 'id', $_POST["id_turma"])) {
            array_push($formErrors, ' A Turma é inválida!');
        }
    }

    if (empty($_POST["numero_aluno"])) {
        array_push($formErrors, 'O número é Obrigatório');
    } else {
        if (isRollnoExists($_POST["id_turma"], $_POST["numero_aluno"])) {
            array_push($formErrors, 'O número já existe!');
        }
    }

    if (empty($_POST["nome_utilizador"])) {
        array_push($formErrors, 'Nome de utilizador é Obrigatório!');
    } else {
        if (isDataExists('alunos', 'nome_utilizador', $_POST["nome_utilizador"])) {
            array_push($formErrors, 'Nome de utilizador já utilizado!');
        }
        if (strlen($_POST["nome_utilizador"]) < 6 || strlen($_POST["nome_utilizador"]) > 20) {
            array_push($formErrors, 'O nome de utilizador deve ter de 6 a 20 caracteres!');
        }
    }

    if (empty($_POST["password"])) {
        array_push($formErrors, 'Senha necessária');
    } else {
        if (strlen($_POST["password"]) < 6 || strlen($_POST["password"]) > 20) {
            array_push($formErrors, 'A senha deve ter de 6 a 20 caracteres!');
        }
    }

    if (file_exists($_FILES['avatar']['tmp_name'])) {
        $file_name = $_FILES['avatar']['nome'];
        $file_size = $_FILES['avatar']['size'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_type = $_FILES['avatar']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensions = array("jpeg", "jpg", "png");
        if (in_array($file_ext, $extensions) === false) {
            array_push($formErrors, 'Escolha um arquivo JPEG ou PNG para o avatar.');
        }
        if ($file_size > 200000) {
            array_push($formErrors, 'O tamanho do arquivo deve ser inferior a 200 KB');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $nome = $_POST["nome"];
        $id_turma = $_POST["id_turma"];
        $nome_utilizador = $_POST["nome_utilizador"];
        $password = $_POST["password"];
        $numero_aluno = $_POST["numero_aluno"];
        $password = password_hash($password, PASSWORD_DEFAULT);
        $directory = "../uploads/avatars/";
        if (isset($file_tmp) && isset($file_name) && isset($file_ext)) {
            $file_name = uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $directory . $file_name);
        } else {
            $file_name = null;
        }

        $stmt = $conn->prepare("INSERT INTO alunos(nome, id_turma, numero_aluno, nome_utilizador, password, avatar) VALUES(?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param('sissss', $nome, $id_turma, $numero_aluno, $nome_utilizador, $password, $file_name);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Dados inseridos com sucesso!';
                header('location:adicionar_aluno.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

function editAluno()
{
    $nome = $id_turma = $id_aluno = $nome_utilizador = $avatar = $numero_aluno = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if (empty($_POST["id_aluno"])) {
        array_push($formErrors, 'A identificação do aluno é obrigatória!');
    } else {
        if (!isDataExists('alunos', 'id', $_POST["id_aluno"])) {
            array_push($formErrors, ' A identificação do aluno é inválida!');
        } else {
            $aluno = getAluno($_POST["id_aluno"]);
        }
    }

    if (empty($_POST["nome"])) {
        array_push($formErrors, 'O nome é obrigatório!');
    } else {
        if (!preg_match("/^[a-zA-z ]*$/", $_POST["nome"])) {
            array_push($formErrors, 'Somente letras e espaços em branco são permitidos para o nome!');
        }
        if (strlen($_POST["nome"]) > 50) {
            array_push($formErrors, 'O nome pode ter no máximo 50 caracteres!');
        }
    }

    if (empty($_POST["id_turma"])) {
        array_push($formErrors, 'A turma é necessária!');
    } else {
        if (!isDataExists('turmas', 'id', $_POST["id_turma"])) {
            array_push($formErrors, ' A Turma é inválida!');
        }
    }

    if (empty($_POST["numero_aluno"])) {
        array_push($formErrors, 'Número é necessário!');
    } else {
        if ($aluno['numero_aluno'] != $_POST["numero_aluno"]) {
            if (isRollnoExists($_POST["id_turma"], $_POST["numero_aluno"])) {
                array_push($formErrors, 'Número já existe!');
            }
        }
    }

    if (empty($_POST["nome_utilizador"])) {
        array_push($formErrors, 'nome_utilizador is required!');
    } else {
        if ($aluno['nome_utilizador'] != $_POST['nome_utilizador']) {
            if (isDataExists('alunos', 'nome_utilizador', $_POST["nome_utilizador"])) {
                array_push($formErrors, 'Nome de utilizador já utilizado!');
            }
            if (strlen($_POST["nome_utilizador"]) < 6 || strlen($_POST["nome_utilizador"]) > 20) {
                array_push($formErrors, 'O nome de utilizador deve ter de 6 a 20 caracteres!');
            }
        }
    }

    if (file_exists($_FILES['avatar']['tmp_name'])) {
        $file_name = $_FILES['avatar']['nome'];
        $file_size = $_FILES['avatar']['size'];
        $file_tmp = $_FILES['avatar']['tmp_name'];
        $file_type = $_FILES['avatar']['type'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $extensions = array("jpeg", "jpg", "png");
        if (in_array($file_ext, $extensions) === false) {
            array_push($formErrors, 'Escolha um arquivo JPEG ou PNG para o avatar.');
        }
        if ($file_size > 200000) {
            array_push($formErrors, 'O tamanho do arquivo deve ser inferior a 200 KB');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $id_aluno = $_POST['id_aluno'];
        $nome = $_POST["nome"];
        $id_turma = $_POST["id_turma"];
        $nome_utilizador = $_POST["nome_utilizador"];
        $numero_aluno = $_POST["numero_aluno"];
        $directory = "../uploads/avatars/";
        if (isset($file_tmp) && isset($file_name) && isset($file_ext)) {
            $file_name = uniqid() . '.' . $file_ext;
            move_uploaded_file($file_tmp, $directory . $file_name);
            unlink('../uploads/avatars/' . $aluno['avatar']);
        } else {
            $file_name = $aluno['avatar'];
        }

        $stmt = $conn->prepare("UPDATE alunos SET nome = ?, id_turma = ?, numero_aluno = ?, nome_utilizador = ?, avatar = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('sisssi', $nome, $id_turma, $numero_aluno, $nome_utilizador, $file_name, $id_aluno);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Aluno atualizado com sucesso!';
                header('location:perfil_aluno.php?aluno=' . $id_aluno);
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

function isRollnoExists($id_turma, $numero_aluno)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM alunos WHERE id_turma = ? AND numero_aluno = ? LIMIT 1");
    $stmt->bind_param('is', $id_turma, $numero_aluno);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado) {
        $row = $resultado->fetch_assoc();
        if ($row) {
            return true;
        }
    }
    return false;
}

// Retorna 10 alunos registado recentemente
function alunosRecentes()
{
    global $conn;
    $rows = array();
    $stmt = $conn->prepare("SELECT alunos.*, turmas.nome AS nome_turma FROM alunos INNER JOIN turmas ON alunos.id_turma=turmas.id ORDER BY alunos.id DESC LIMIT 10");
    if ($stmt) {
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Retorna 10 professors registados recentemente
function recentesProfessores()
{
    global $conn;
    $rows = array();
    $stmt = $conn->prepare("SELECT * FROM professors ORDER BY id DESC LIMIT 10");
    if ($stmt) {
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Retorna uma Turma específica
function getTurma($id_turma)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM turmas WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $id_turma);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            $row = $resultado->fetch_assoc();
            return $row;
        }
    }
    return false;
}

// Retorna todos os alunos de uma turma específica
function  alunosdaTurma($id_turma)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM alunos WHERE id_turma = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id_turma);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            return $resultado;
        }
    }
    return false;
}

// Criar exame
function criarExame()
{
    $exame = $total_de_perguntas = $total_notas = $tempo_total = $data = $passar_notas = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if (empty($_POST["exame"])) {
        array_push($formErrors, 'O nome do exame é obrigatório!');
    } else {
        if (strlen($_POST["exame"]) > 50) {
            array_push($formErrors, 'O nome do exame pode ter no máximo 50 caracteres!');
        }
    }

    if (empty($_POST["total_de_perguntas"])) {
        array_push($formErrors, 'O total de  pergunta é obrigatória!');
    } else {
        if (!is_numeric($_POST["total_de_perguntas"])) {
            array_push($formErrors, 'O total da pergunta deve ser numérico!');
        }
    }

    if (empty($_POST["total_notas"])) {
        array_push($formErrors, 'Total de Notas é necessária!');
    } else {
        if (!is_numeric($_POST["total_notas"])) {
            array_push($formErrors, 'As notas totais devem ser numéricas!');
        } else {
            if (empty($_POST["passar_notas"])) {
                array_push($formErrors, 'Notas de aprovação são necessárias!');
            } else {
                if (!is_numeric($_POST["passar_notas"])) {
                    array_push($formErrors, 'As Notas de aprovação devem ser numéricas!');
                } else {
                    if ($_POST["total_notas"] < $_POST["passar_notas"]) {
                        array_push($formErrors, 'As notas de aprovação devem ser menores que as notas totais!');
                    }
                }
            }
        }
    }

    if (empty($_POST["tempo_total"])) {
        array_push($formErrors, 'O tempo total é obrigatório!');
    } else {
        if (!is_numeric($_POST["tempo_total"])) {
            array_push($formErrors, 'O tempo total deve ser numérico!');
        }
    }

    if (empty($_POST["data"])) {
        array_push($formErrors, 'A data é obrigatória!');
    } else {
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST["data"])) {
            array_push($formErrors, 'A data não é válida!');
        } else {
            $dateExploded = explode("-", $_POST["data"]);
            if (count($dateExploded) != 3) {
                array_push($formErrors, 'O formato da data não é válido!');
            } else {
                $day = $dateExploded[2];
                $month = $dateExploded[1];
                $year = $dateExploded[0];
                if (!checkdate($month, $day, $year)) {
                    array_push($formErrors, 'A data não é válida!');
                }
            }
        }
    }


    if (empty($formErrors)) {
        global $conn;
        $exame = $_POST["exame"];
        $total_de_perguntas = $_POST["total_de_perguntas"];
        $total_notas = $_POST["total_notas"];
        $tempo_total = $_POST["tempo_total"];
        $data = $_POST["data"];
        $passar_notas = $_POST["passar_notas"];
        $id_utilizador = $_SESSION['id_utilizador'];

        $stmt = $conn->prepare("INSERT INTO exames(nome_exame, id_professor_criado, total_de_perguntas, total_notas, passar_notas, tempo_total, data) VALUES(?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param('siiiiis', $exame, $id_utilizador, $total_de_perguntas, $total_notas, $passar_notas, $tempo_total, $data);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Exame criado com sucesso!';
                header('location:ver_exame.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

// atualização de exame
function atualizarExame()
{
    $id_exame = $nome_exame = $total_de_perguntas = $total_notas = $tempo_total = $data_exame = $passar_notas = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if (empty($_POST["nome_exame"])) {
        array_push($formErrors, 'O nome do exame é obrigatório!');
    } else {
        if (strlen($_POST["nome_exame"]) > 50) {
            array_push($formErrors, 'O nome do exame pode ter no máximo 50 caracteres!');
        }
    }

    if (empty($_POST["total_de_perguntas"])) {
        array_push($formErrors, 'O Total de pergunta é necessária!');
    } else {
        if (!is_numeric($_POST["total_de_perguntas"])) {
            array_push($formErrors, 'O total da pergunta deve ser numérico!');
        }
    }

    if (empty($_POST["total_notas"])) {
        array_push($formErrors, 'Total de Notas é obrigatório!');
    } else {
        if (!is_numeric($_POST["total_notas"])) {
            array_push($formErrors, 'O total de notas  devem ser numéricas!');
        } else {
            if (empty($_POST["passar_notas"])) {
                array_push($formErrors, 'Notas de aprovação são necessárias!');
            } else {
                if (!is_numeric($_POST["passar_notas"])) {
                    array_push($formErrors, 'As notas de aprovação devem ser numéricas!');
                } else {
                    if ($_POST["total_notas"] < $_POST["passar_notas"]) {
                        array_push($formErrors, 'As notas de aprovação devem ser menores que as notas totais!');
                    }
                }
            }
        }
    }

    if (empty($_POST["tempo_total"])) {
        array_push($formErrors, 'O tempo total é obrigatório!');
    } else {
        if (!is_numeric($_POST["tempo_total"])) {
            array_push($formErrors, 'O tempo total deve ser numérico!');
        }
    }

    if (empty($_POST["data_exame"])) {
        array_push($formErrors, 'A data é obrigatória!');
    } else {
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $_POST["data_exame"])) {
            array_push($formErrors, 'A data não é válida!');
        } else {
            $dateExploded = explode("-", $_POST["data_exame"]);
            if (count($dateExploded) != 3) {
                array_push($formErrors, 'O formato da data não é válido!');
            } else {
                $day = $dateExploded[2];
                $month = $dateExploded[1];
                $year = $dateExploded[0];
                if (!checkdate($month, $day, $year)) {
                    array_push($formErrors, 'A data não é válida!');
                }
            }
        }
    }


    if (empty($formErrors)) {
        global $conn;
        $id_exame = $_POST["id_exame"];
        $nome_exame = $_POST["nome_exame"];
        $total_de_perguntas = $_POST["total_de_perguntas"];
        $total_notas = $_POST["total_notas"];
        $tempo_total = $_POST["tempo_total"];
        $data_exame = $_POST["data_exame"];
        $passar_notas = $_POST["passar_notas"];
        $id_utilizador = $_SESSION['id_utilizador'];


        $stmt = $conn->prepare("SELECT * FROM exames WHERE id = ? AND id_professor_criado = ? LIMIT 1");

        if ($stmt) {
            $stmt->bind_param('ii', $id_exame, $id_utilizador);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($resultado->num_rows > 0) {
                $stmt = $conn->prepare("UPDATE exames SET nome_exame = ?, total_de_perguntas = ?, total_notas = ?, passar_notas = ?, tempo_total = ?, data = ? WHERE id = ?");
                if ($stmt) {
                    $stmt->bind_param('siiiisi', $nome_exame, $total_de_perguntas, $total_notas, $passar_notas, $tempo_total, $data_exame, $id_exame);
                    if ($stmt->execute()) {
                        $_SESSION['feedbackSuccess'] = 'Dados do exame atualizados com sucesso!';
                        header('location:ver_exame.php');
                        exit();
                    }
                }
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

// Retorna todos os exames criados por um determinado professor
function professorExames($id_professor)
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM exames WHERE id_professor_criado = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id_professor);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Insira uma pergunta
function addPergunta()
{
    $pergunta = $opcao_a = $opcao_b = $opcao_c = $opcao_d = $opcao_correta = $id_exame = $notas = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    $id_utilizador = $_SESSION['id_utilizador'];

    if (empty($_POST["id_exame"])) {
        array_push($formErrors, 'A identidade do exame é obrigatório!');
    } else {
        if (!isDataExists('exames', 'id', $_POST["id_exame"])) {
            array_push($formErrors, 'A identidade do  exame é inválida!');
        }
        //verifique se o exame se foi criado por este professor ou não
        $exames = professorExames($id_utilizador);
        $valid = false;
        foreach ($exames as $exame) {
            if ($exame['id'] == $_POST["id_exame"]) {
                if ($exame['id_professor_criado'] == $id_utilizador) {
                    $valid = true;
                }
            }
        }
        if (!$valid) {
            array_push($formErrors, 'A identidade do  exame é inválida!');
        }

        $check = contagemPerguntasNotas($_POST["id_exame"]);
        $exame = getExame($_POST["id_exame"]);

        if ($exame['total_de_perguntas'] == $check['COUNT(*)']) {
            array_push($formErrors, 'O total da pergunta foi excedido!');
        }
        if ($exame['total_notas'] == $check['SUM(notas)']) {
            array_push($formErrors, 'O total de marcas foi excedido!');
        }
    }

    if (!isset($_POST["pergunta"]) || trim($_POST["pergunta"]) == null) {
        array_push($formErrors, 'A pergunta é obrigatória!');
    } else {
        if (strlen($_POST["pergunta"]) > 16777215) {
            array_push($formErrors, 'A pergunta pode ter no máximo 16777215 caracteres!');
        }
    }

    if (!isset($_POST["opcao_a"]) || trim($_POST["opcao_a"]) == null) {
        array_push($formErrors, 'A opção A é obrigatória!');
    } else {
        if (strlen($_POST["opcao_a"]) > 16777215) {
            array_push($formErrors, 'A opção A pode ter no máximo 16777215 caracteres!');
        }
    }

    if (!isset($_POST["opcao_b"]) || trim($_POST["opcao_b"]) == null) {
        array_push($formErrors, 'A opção B é obrigatória!');
    } else {
        if (strlen($_POST["opcao_b"]) > 16777215) {
            array_push($formErrors, 'A opção B pode ter no máximo 16777215 caracteres!');
        }
    }

    if (!isset($_POST["opcao_c"]) || trim($_POST["opcao_c"]) == null) {
        array_push($formErrors, 'A opção C é obrigatória!');
    } else {
        if (strlen($_POST["opcao_c"]) > 16777215) {
            array_push($formErrors, 'A opção C pode ter no máximo 16777215 caracteres!');
        }
    }

    if (!isset($_POST["opcao_d"]) || trim($_POST["opcao_d"]) == null) {
        array_push($formErrors, 'A opção D é obrigatória!');
    } else {
        if (strlen($_POST["opcao_d"]) > 16777215) {
            array_push($formErrors, 'A opção D pode ter no máximo 16777215 caracteres!');
        }
    }

    if (!isset($_POST["opcao_correta"])) {
        array_push($formErrors, 'Selecione a opção correta!');
    } else {
        $opcoes_validas = array('opcao_a', 'opcao_b', 'opcao_c', 'opcao_d');
        if (!in_array($_POST["opcao_correta"], $opcoes_validas)) {
            array_push($formErrors, 'Selecione uma opção correta válida!');
        }
    }

    if (empty($_POST["notas"])) {
        array_push($formErrors, 'Notas são necessárias!');
    } else {
        if (!is_numeric($_POST["notas"])) {
            array_push($formErrors, 'As notas devem ser numéricas!');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $pergunta = $_POST["pergunta"];
        $opcao_a = $_POST["opcao_a"];
        $opcao_b = $_POST["opcao_b"];
        $opcao_c = $_POST["opcao_c"];
        $opcao_d = $_POST["opcao_d"];
        $id_exame = $_POST["id_exame"];
        $notas = $_POST["notas"];
        $opcao_correta = $_POST["opcao_correta"];

        $stmt = $conn->prepare("INSERT INTO perguntas(pergunta, opcao_a, opcao_b, opcao_c, opcao_d, id_exame, opcao_correta, notas) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt) {
            $stmt->bind_param('sssssisi', $pergunta, $opcao_a, $opcao_b, $opcao_c, $opcao_d, $id_exame, $opcao_correta, $notas);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Pergunta adicionada com sucesso!';
                header('location:adicionar_perguntas.php?exame=' . $id_exame);
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

// Atualizar uma pergunta
function atualizarPergunta()
{
    $pergunta = $opcao_a = $opcao_b = $opcao_c = $opcao_d = $opcao_correta = $id_pergunta = $notas = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    $id_utilizador = $_SESSION['id_utilizador'];

    if (empty($_POST["id_pergunta"])) {
        array_push($formErrors, 'O titulo da pergunta é obrigatório!');
    } else {
        if (!isDataExists('perguntas', 'id', $_POST["id_pergunta"])) {
            array_push($formErrors, 'O titulo da pergunta é inválido!');
        } else {
            $pergunta = getPergunta($id_pergunta);
            $id_exame = $pergunta['id_exame'];
            $exame = getExame($id_exame);
            if ($exame) {
                if (!$id_utilizador == $exame['id_professor_criado']) {
                    array_push($formErrors, 'Você não está autorizado a modificar isso!');
                } else {
                    $check = contagemPerguntasNotas($id_exame);
                    if ($exame['total_de_perguntas'] == $check['COUNT(*)']) {
                        array_push($formErrors, 'O total de pergunta foi excedida!');
                    }
                    if ($exame['total_notas'] == $check['SUM(notas)']) {
                        array_push($formErrors, 'O total de notas foi excedido!');
                    }
                }
            }
        }
    }

    if (!isset($_POST["pergunta"]) || trim($_POST["pergunta"]) == null) {
        array_push($formErrors, 'A pergunta é obrigatória!');
    } else {
        if (strlen($_POST["pergunta"]) > 16777215) {
            array_push($formErrors, 'A pergunta pode ter no máximo 16777215 caracteres!');
        }
    }

    if (!isset($_POST["opcao_a"]) || trim($_POST["opcao_a"]) == null) {
        array_push($formErrors, 'A opção A é obrigatória!');
    } else {
        if (strlen($_POST["opcao_a"]) > 16777215) {
            array_push($formErrors, 'A opção A pode ter no máximo 16777215 caracteres!');
        }
    }

    if (!isset($_POST["opcao_b"]) || trim($_POST["opcao_b"]) == null) {
        array_push($formErrors, 'A opção B é obrigatória!');
    } else {
        if (strlen($_POST["opcao_b"]) > 16777215) {
            array_push($formErrors, 'A opção B pode ter no máximo 16777215 caracteres!');
        }
    }

    if (!isset($_POST["opcao_c"]) || trim($_POST["opcao_c"]) == null) {
        array_push($formErrors, 'A opção C é obrigatória!');
    } else {
        if (strlen($_POST["opcao_c"]) > 16777215) {
            array_push($formErrors, 'A opção C pode ter no máximo 16777215 caracteres!');
        }
    }

    if (!isset($_POST["opcao_d"]) || trim($_POST["opcao_d"]) == null) {
        array_push($formErrors, 'A opção D é necessária!');
    } else {
        if (strlen($_POST["opcao_d"]) > 16777215) {
            array_push($formErrors, 'A opção D pode ter no máximo 16777215 caracteres!');
        }
    }

    if (!isset($_POST["opcao_correta"])) {
        array_push($formErrors, 'Selecione a opção correta!');
    } else {
        $opcoes_validas = array('opcao_a', 'opcao_b', 'opcao_c', 'opcao_d');
        if (!in_array($_POST["opcao_correta"], $opcoes_validas)) {
            array_push($formErrors, 'Selecione uma opção correta válida!');
        }
    }

    if (empty($_POST["notas"])) {
        array_push($formErrors, 'Notas são necessárias!');
    } else {
        if (!is_numeric($_POST["notas"])) {
            array_push($formErrors, 'As notas devem ser numéricas!');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $pergunta = $_POST["pergunta"];
        $opcao_a = $_POST["opcao_a"];
        $opcao_b = $_POST["opcao_b"];
        $opcao_c = $_POST["opcao_c"];
        $opcao_d = $_POST["opcao_d"];
        $id_pergunta = $_POST["id_pergunta"];
        $notas = $_POST["notas"];
        $opcao_correta = $_POST["opcao_correta"];

        $dadosPergunta = getPergunta($id_pergunta);
        $id_exame = $dadosPergunta['id_exame'];

        $stmt = $conn->prepare("UPDATE perguntas SET pergunta = ?, opcao_a = ?, opcao_b = ?, opcao_c = ?, opcao_d = ?, opcao_correta = ?, notas = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('ssssssii', $pergunta, $opcao_a, $opcao_b, $opcao_c, $opcao_d, $opcao_correta, $notas, $id_pergunta);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Pergunta atualizada com sucesso!';
                header('location:ver_perguntas.php?exame=' . $id_exame);
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

// Retorna um exame específico
function getExame($id_exame)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM exames WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $id_exame);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            $row = $resultado->fetch_assoc();
            return $row;
        }
    }
    return false;
}

// Retorna todas as perguntas de um determinado exame
function getPerguntas($id_exame)
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM perguntas WHERE id_exame = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id_exame);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Retorna o número de questões inseridas de um determinado exame
function contagemPerguntasNotas($id_exame)
{
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*), SUM(notas) FROM perguntas WHERE id_exame = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id_exame);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            $row = $resultado->fetch_assoc();
            return $row;
        }
    }
    return false;
}

// Ver as perguntas inseridas por um determinado professor
function verPerguntas($id_exame)
{
    $id_utilizador = $_SESSION['id_utilizador'];
    $exames = professorExames($id_utilizador);
    $valid = false;
    foreach ($exames as $exame) {
        if ($exame['id'] == $id_exame) {
            if ($exame['id_professor_criado'] == $id_utilizador) {
                $valid = true;
            }
        }
    }
    if ($valid) {
        return getperguntas($id_exame);
    } else {
        return false;
    }
}

// Atribuir exame à turma
function assignExam()
{
    $id_exame = $id_turma = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    $id_utilizador = $_SESSION['id_utilizador'];

    if (empty($_POST["id_exame"])) {
        array_push($formErrors, 'O exame é obrigatório!');
    } else {
        if (!isDataExists('exames', 'id', $_POST["id_exame"])) {
            array_push($formErrors, 'A identidade do  exame é inválida!');
        }
        //verifique se o exame foi criado por este professor ou não
        $exames = professorExames($id_utilizador);
        $valid = false;
        foreach ($exames as $exame) {
            if ($exame['id'] == $_POST["id_exame"]) {
                if ($exame['id_professor_criado'] == $id_utilizador) {
                    $valid = true;
                }
            }
        }
        if (!$valid) {
            array_push($formErrors, 'A identidade do  exame é inválida!');
        }
    }

    if (empty($_POST["id_turma"])) {
        array_push($formErrors, 'A turma é necessária!');
    } else {
        if (!isDataExists('turmas', 'id', $_POST["id_turma"])) {
            array_push($formErrors, 'A indentidade da turma é inválida!');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $id_exame = $_POST["id_exame"];
        $id_turma = $_POST["id_turma"];

        // Verifique se já existe
        $stmt = $conn->prepare("SELECT id FROM turma_de_axame WHERE id_exame = ? AND id_turma = ?");
        if ($stmt) {
            $stmt->bind_param('ii', $id_exame, $id_turma);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($resultado) {
                $row = $resultado->fetch_assoc();
                if ($row) {
                    $_SESSION['feedbackFailed'] = 'Fracassado! O exame já está atribuído à turma.';
                    header('location:atribuir_exame.php');
                    exit();
                } else {
                    $stmt = $conn->prepare("INSERT INTO turma_de_axame(id_exame, id_turma) VALUES(?, ?)");

                    if ($stmt) {
                        $stmt->bind_param('ii', $id_exame, $id_turma);
                        if ($stmt->execute()) {
                            $_SESSION['feedbackSuccess'] = 'Exame atribuído à turma com sucesso!';
                            header('location:atribuir_exame.php');
                            exit();
                        } else {
                            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
                        }
                    } else {
                        $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
                    }
                }
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        }
    }
}

// Obtenha todos os exames atribuídos por um determinado professor
function getExamesAtribuidos()
{
    //Pendência 
    //Impedir a inserção do mesmo ID de exame e ID de Turma ao atribuir exame()
    $id_utilizador = $_SESSION['id_utilizador'];
    $rows = array();
    global $conn;

    $stmt = $conn->prepare("SELECT exames.nome_exame AS nome_exame, exames.id AS id_exame, exames.data, turmas.nome AS nome_turma, 
        turmas.id AS id_turma FROM exames INNER JOIN turma_de_axame ON exames.id = turma_de_axame.id_exame INNER JOIN turmas ON 
        turmas.id = turma_de_axame.id_turma WHERE exames.id_professor_criado = ?");

    if ($stmt) {
        $stmt->bind_param('i', $id_utilizador);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Obtenha todos os exames ao vivo de uma Turma específica
function getExamesAoVivo($id_turma)
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT exames.* FROM exames INNER JOIN turma_de_axame ON exames.id = turma_de_axame.id_exame WHERE turma_de_axame.id_turma = ? AND exames.esta_ativo = 1");
    if ($stmt) {
        $stmt->bind_param('i', $id_turma);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Retorna uma pergunta específica
function getPergunta($id_pergunta)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM perguntas WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $id_pergunta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            $row = $resultado->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }
}

// Retorna todas as respostas de um exame para um aluno
function getRespostas($id_exame, $id_aluno)
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT respostas.*, perguntas.* FROM respostas INNER JOIN perguntas ON respostas.id_pergunta = perguntas.id WHERE perguntas.id_exame = ? AND respostas.id_aluno = ?");
    if ($stmt) {
        $stmt->bind_param('ii', $id_exame, $id_aluno);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
}

// Iniciar o temporizador para aluno conectado
function iniciarTemporizador($id_exame)
{
    global $conn;
    $id_aluno = $_SESSION['id_utilizador'];

    if (isDataExists('exames', 'id', $id_exame)) {
        if (isValidExamClass($id_exame)) {
            // verifique se o temporizador existe
            if (!getTemporizador($id_exame)) {
                // Insira o temporizador

                $stmt = $conn->prepare("INSERT INTO temporizador(id_aluno, id_exame, hora_inicio) VALUES(?, ?, ?)");
                if ($stmt) {
                    $tempo = time();
                    $stmt->bind_param('iis', $id_aluno, $id_exame, $tempo);
                    $stmt->execute();
                }
            }
        }
    }
}

// Verifique se o exame é válido para aluno conectado
function isValidExamClass($id_exame)
{
    global $conn;
    $aluno = alunoConectado();
    $id_turma_aluno  = $aluno['id_turma'];
    $exame = getExame($id_exame);
    $esta_ativo = $exame['esta_ativo'];
    if ($esta_ativo == 1) {
        $stmt = $conn->prepare("SELECT * FROM turma_de_axame WHERE id_exame = ? AND id_turma = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('ii', $id_exame, $id_turma_aluno);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($resultado) {
                $row = $resultado->fetch_assoc();
                if ($row) {
                    return true;
                }
            }
        }
    }
    return false;
}

// Retorna o temporizador do aluno logado para um exame
function getTemporizador($id_exame)
{
    $id_aluno = $_SESSION['id_utilizador'];
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM temporizador WHERE id_aluno = ? AND id_exame = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('ii', $id_aluno, $id_exame);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            $row = $resultado->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }
}

// Retorna o tempo restante do aluno conectado para uma prova
function temporizadorRestante($id_exame)
{
    $id_aluno = $_SESSION['id_utilizador'];
    $temporizador = getTemporizador($id_exame);
    if ($temporizador) {
        $tempo = time();
        $exame = getExame($id_exame);

        $tempo_total = $exame['tempo_total'] * 60;
        $gasto = $tempo - $temporizador['hora_inicio'];
        $restante = $tempo_total - $gasto;

        return $restante;

    }
    return false;
}

// Retorna resultados
function resultados($id_turma, $id_exame)
{
    global $conn;
    $rows = array();
    $stmt = $conn->prepare("SELECT SUM(CASE WHEN respostas.opcao_respondida=perguntas.opcao_correta THEN perguntas.notas ELSE 0 END) AS obtido, alunos.id AS id_aluno, alunos.nome AS nome_aluno, alunos.numero_aluno FROM perguntas INNER JOIN respostas ON respostas.id_pergunta = perguntas.id INNER JOIN alunos ON respostas.id_aluno=alunos.id WHERE perguntas.id_exame = ? AND alunos.id_turma = ? GROUP BY respostas.id_aluno");
    if ($stmt) {
        $stmt->bind_param('ii', $id_exame, $id_turma);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        } else {
            return false;
        }
    }
}

// Verifique se o acesso aos resultados é válido para professor conectado
function eProfessordeAcessoaResultadosValidos($id_exame, $id_turma)
{
    global $conn;
    $id_professor = $_SESSION['id_utilizador'];
    $exame = getExame($id_exame);
    if ($exame) {
        if ($id_professor == $exame['id_professor_criado']) {
            $stmt = $conn->prepare("SELECT * FROM turma_de_axame WHERE id_exame = ? AND id_turma = ? LIMIT 1");
            if ($stmt) {
                $stmt->bind_param('ii', $id_exame, $id_turma);
                $stmt->execute();
                $resultado = $stmt->get_result();
                if ($resultado) {
                    $row = $resultado->fetch_assoc();
                    if ($row) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    return false;
}

// Retorna um aluno
function getAluno($id_aluno)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM alunos WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $id_aluno);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            $row = $resultado->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }
}

// Retorna um professor
function getProfessor($id_professor)
{
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM professors WHERE id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $id_professor);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            $row = $resultado->fetch_assoc();
            return $row;
        } else {
            return false;
        }
    }
}


// Exclui um exame de um professor
function excluirProfessordeExame($id_exame)
{
    global $conn;
    $id_professor = $_SESSION['id_utilizador'];
    $exame = getExame($id_exame);

    if ($exame) {
        if ($id_professor == $exame['id_professor_criado']) {
            $stmt = $conn->prepare("DELETE FROM exames WHERE id = ?");

            if ($stmt) {
                $stmt->bind_param('i', $id_exame);
                if ($stmt->execute()) {
                    $_SESSION['feedbackSuccess'] = 'Exame excluído com sucesso!';
                    header('location:ver_exame.php');
                    exit();
                } else {
                    $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
                }
            }
        }
        $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
    }
    $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
}

// Exclui um exame atribuído por um professor
function deleteAssignedExam($id_exame, $id_turma)
{
    global $conn;
    $id_professor = $_SESSION['id_utilizador'];
    $exame = getExame($id_exame);

    if ($exame) {
        if ($id_professor == $exame['id_professor_criado']) {
            $stmt = $conn->prepare("DELETE FROM turma_de_axame WHERE id_exame = ? AND id_turma = ?");
            if ($stmt) {
                $stmt->bind_param('ii', $id_exame, $id_turma);
                if ($stmt->execute()) {
                    $_SESSION['feedbackSuccess'] = 'Exame atribuído, excluído com sucesso!';
                    header('location:atribuir_exame.php');
                    exit();
                } else {
                    $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
                }
            }
        }
        $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
    }
    $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
}


function getExamsAdmin()
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT exames.*, professors.nome FROM exames LEFT JOIN professors ON exames.id_professor_criado = professors.id");
    if ($stmt) {
        // $stmt->bind_param('i', $id_turma);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Retorna todas as Turmas atribuídas para um determinado exame
function getAtribuirTurma($id_exame)
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT turmas.* FROM turmas INNER JOIN turma_de_axame ON turma_de_axame.id_turma = turmas.id WHERE turma_de_axame.id_exame = ? ORDER BY turmas.nome");
    if ($stmt) {
        $stmt->bind_param('i', $id_exame);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Ativa um exame
function makeLive($id_exame)
{
    global $conn;
    $stmt = $conn->prepare("UPDATE exames SET esta_ativo = CASE WHEN esta_ativo = 0 THEN 1 ELSE 0 END WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id_exame);
        if ($stmt->execute()) {
            $_SESSION['feedbackSuccess'] = 'Acesso atualizado com sucesso!';
            header('location:exame.php');
            exit();
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
    $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
}

function getAdminExamesAtribuido()
{
    $rows = array();
    global $conn;

    $stmt = $conn->prepare("SELECT exames.nome_exame AS nome_exame, exames.id AS id_exame, exames.data, turmas.nome AS nome_turma, 
        turmas.id AS id_turma FROM exames INNER JOIN turma_de_axame ON exames.id = turma_de_axame.id_exame INNER JOIN turmas ON 
        turmas.id = turma_de_axame.id_turma");

    if ($stmt) {
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// Exclui uma pergunta de um professor
function deletePergunta($id_pergunta)
{
    global $conn;
    $id_professor = $_SESSION['id_utilizador'];
    $pergunta = getPergunta($id_pergunta);

    if (isset($pergunta['id_exame'])) {
        $id_exame = $pergunta['id_exame'];
        $exame = getExame($id_exame);
    }

    if (isset($exame)) {
        if ($id_professor == $exame['id_professor_criado']) {
            $stmt = $conn->prepare("DELETE FROM perguntas WHERE id = ?");

            if ($stmt) {
                $stmt->bind_param('i', $id_pergunta);
                if ($stmt->execute()) {
                    $_SESSION['feedbackSuccess'] = 'Pergunta apagada com sucesso!';
                    header('location:ver_perguntas.php?exame=' . $id_exame);
                    exit();
                } else {
                    $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
                }
            }
        }
        $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
    }
    $_SESSION['feedbackFailed'] = 'Fracassado! Pergunta não encontrada!.';
}

function deleteProfessor($id_professor)
{
    global $conn;
    if (isDataExists('professors', 'id', $id_professor)) {
        $professor = getProfessor($id_professor);

        $stmt = $conn->prepare("DELETE FROM professors WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $id_professor);
            if ($stmt->execute()) {
                if ($professor['avatar']) {
                    unlink('../uploads/avatars/' . $professor['avatar']);
                }
                $_SESSION['feedbackSuccess'] = 'Professor excluído com sucesso!';
                header('location:ver_professor.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        }
        $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
    }
}

function deleteAluno($id_aluno)
{
    global $conn;
    if (isDataExists('alunos', 'id', $id_aluno)) {
        $aluno = getAluno($id_aluno);
        $stmt = $conn->prepare("DELETE FROM alunos WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param('i', $id_aluno);
            if ($stmt->execute()) {
                if ($aluno['avatar']) {
                    unlink('../uploads/avatars/' . $aluno['avatar']);
                }
                $_SESSION['feedbackSuccess'] = 'Aluno excluído com sucesso!';
                header('location:ver_aluno.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        }
        $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
    }
}

function atribuirProfessor()
{
    $id_exame = $id_professor = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if (empty($_POST["id_exame"])) {
        array_push($formErrors, 'O ID do exame é obrigatório!');
    } else {
        if (!isDataExists('exames', 'id', $_POST["id_exame"])) {
            array_push($formErrors, 'A identidade do  exame é inválida!');
        }
    }

    if (empty($_POST["id_professor"])) {
        array_push($formErrors, 'A indentificação do professor é obrigatório!');
    } else {
        if (!isDataExists('professors', 'id', $_POST["id_professor"])) {
            array_push($formErrors, 'A identificação do professor é inválida!');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $id_professor = $_POST["id_professor"];
        $id_exame = $_POST["id_exame"];

        $stmt = $conn->prepare("UPDATE exames SET id_professor_criado = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('ii', $id_professor, $id_exame);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Professor atribuído com sucesso!';
                header('location:exame.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

function deleteTurma($id_turma)
{
    global $conn;
    if (isDataExists('turmas', 'id', $id_turma)) {
        $stmt1 = $conn->prepare("DELETE FROM turmas WHERE id = ?");
        $stmt2 = $conn->prepare("SELECT avatar FROM alunos WHERE id_turma = ?");
        if ($stmt1 && $stmt2) {
            $stmt1->bind_param('i', $id_turma);
            $stmt2->bind_param('i', $id_turma);
            if ($stmt1->execute()) {
                $stmt2->execute();
                $resultado = $stmt2->get_result();
                if ($resultado) {
                    while ($row = $resultado->fetch_assoc()) {
                        if ($row['avatar']) {
                            unlink('../uploads/avatars/' . $row['avatar']);
                        }
                    }
                }
                $_SESSION['feedbackSuccess'] = 'Turma excluída com sucesso!';
                header('location:turma.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        }
        $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
    }
}

function editTurma()
{
    $turma = null;
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if (empty($_POST["nome_turma"])) {
        array_push($formErrors, 'O nome da turma é obrigatório!');
    } else {
        if (isDataExists('turmas', 'nome', $_POST["turma"])) {
            array_push($formErrors, 'A Turma já existe!');
        }
    }

    if (empty($_POST["id_turma"])) {
        array_push($formErrors, 'A identidade da turma é obrigatório!');
    } else {
        if (!isDataExists('turmas', 'id', $_POST["id_turma"])) {
            array_push($formErrors, 'A indentidade da turma é inválida!');
        }
    }

    if (empty($formErrors)) {
        global $conn;
        $turma = htmlspecialchars($_POST["nome_turma"]);
        $id = htmlspecialchars($_POST["id_turma"]);

        $stmt = $conn->prepare("UPDATE turmas SET nome = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('si', $turma, $id);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Turma atualizada com sucesso!';
                header('location:turma.php');
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
                exit();
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

function changeStudentPasswordByAdmin()
{
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if (empty($_POST["id_aluno"])) {
        array_push($formErrors, 'A identificação do aluno é obrigatória!');
    } else {
        if (!isDataExists('alunos', 'id', $_POST["id_aluno"])) {
            array_push($formErrors, ' A identificação do aluno é inválida!');
        }
    }

    if (empty($_POST["password"])) {
        array_push($formErrors, 'Senha necessária!');
    } else {
        if (strlen($_POST["password"]) < 6 || strlen($_POST["password"]) > 20) {
            array_push($formErrors, 'A senha deve ter de 6 a 20 caracteres!');
        }
    }
    if (empty($formErrors)) {
        global $conn;
        $id_aluno = $_POST['id_aluno'];
        $password = $_POST["password"];
        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE alunos SET password = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('si', $password, $id_aluno);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Senha atualizada com sucesso!';
                header('location:perfil_aluno.php?aluno=' . $id_aluno);
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

function changeTeacherPasswordByAdmin()
{
    global $formErrors;
    $formErrors = array();

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if (empty($_POST["id_professor"])) {
        array_push($formErrors, 'A indentificação do professor é obrigatório!');
    } else {
        if (!isDataExists('professors', 'id', $_POST["id_professor"])) {
            array_push($formErrors, 'A identificação do professor é inválida!');
        }
    }

    if (empty($_POST["password"])) {
        array_push($formErrors, 'Senha necessária!');
    } else {
        if (strlen($_POST["password"]) < 6 || strlen($_POST["password"]) > 20) {
            array_push($formErrors, 'A senha deve ter de 6 a 20 caracteres!');
        }
    }
    if (empty($formErrors)) {
        global $conn;
        $id_professor = $_POST['id_professor'];
        $password = $_POST["password"];
        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE professors SET password = ? WHERE id = ?");

        if ($stmt) {
            $stmt->bind_param('si', $password, $id_professor);
            if ($stmt->execute()) {
                $_SESSION['feedbackSuccess'] = 'Senha atualizada com sucesso!';
                header('location:perfil_professor.php?professor=' . $id_professor);
                exit();
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        } else {
            $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
        }
    }
}

// resultados do perfil
function perfilResultados($id_aluno)
{
    global $conn;
    $rows = array();


    $stmt = $conn->prepare("SELECT SUM(CASE WHEN respostas.opcao_respondida=perguntas.opcao_correta THEN perguntas.notas ELSE 0 END) AS obtido, exames.nome_exame, exames.total_notas, exames.passar_notas FROM exames INNER JOIN perguntas ON exames.id=perguntas.id_exame INNER JOIN respostas ON respostas.id_pergunta=perguntas.id WHERE respostas.id_aluno = ? GROUP BY exames.id");



    if ($stmt) {
        $stmt->bind_param('i', $id_aluno);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        } else {
            return false;
        }
    }
}

function changeMyPassword($table)
{
    global $formErrors;
    global $conn;
    $formErrors = array();
    $id_utilizador = $_SESSION['id_utilizador'];

    if (empty($_POST['csrf_token'])) {
        array_push($formErrors, 'Token CSRF é necessário!');
    } else {
        if (!hash_equals($_SESSION['token'], $_POST['csrf_token'])) {
            array_push($formErrors, 'Token CSRF inválido!');
        }
    }

    if ($id_utilizador) {
        if (empty($_POST["current_password"])) {
            array_push($formErrors, 'A senha atual é!');
        } else {
            $stmt = $conn->prepare("SELECT * FROM $table WHERE id = ?");
            if ($stmt) {
                $stmt->bind_param('i', $id_utilizador);
                $stmt->execute();
                $resultado = $stmt->get_result();
                if ($resultado) {
                    $row = $resultado->fetch_assoc();
                    $hashed_password = $row['password'];
                    if (!password_verify($_POST["current_password"], $hashed_password)) {
                        array_push($formErrors, 'A senha atual não corresponde!');
                    }
                } else {
                    array_push($formErrors, 'Algo está errado!');
                }
            }
        }

        if (empty($_POST["password"])) {
            array_push($formErrors, 'Senha necessária!');
        } else {
            if (strlen($_POST["password"]) < 6 || strlen($_POST["password"]) > 20) {
                array_push($formErrors, 'A senha deve ter de 6 a 20 caracteres!');
            } else {
                if (empty($_POST["confirm_password"])) {
                    array_push($formErrors, 'Confirmação de  senha é necessária!');
                } else {
                    if ($_POST["password"] != $_POST["confirm_password"]) {
                        array_push($formErrors, 'A senha e a senha de confirmação não correspondem!');
                    }
                }
            }
        }

        if (empty($formErrors)) {
            global $conn;
            $password = $_POST["password"];
            $password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE $table SET password = ? WHERE id = ?");

            if ($stmt) {
                $stmt->bind_param('si', $password, $id_utilizador);
                if ($stmt->execute()) {
                    $_SESSION['feedbackSuccess'] = 'Senha atualizada com sucesso!';
                    header('location:index.php');
                    exit();
                } else {
                    $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
                }
            } else {
                $_SESSION['feedbackFailed'] = 'Fracassado! Por favor, tente novamente mais tarde.';
            }
        } else {
            $passwordErrors = null;
            foreach ($formErrors as $errors) {
                $passwordErrors = $passwordErrors . $errors . '<br>';
            }
            $_SESSION['feedbackFailed'] = $passwordErrors;
        }
    }
}

//obter todos os temporizadores de um determinado aluno
function getTemporizadores($id_aluno)
{
    $rows = array();
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM temporizador WHERE id_aluno = ?");
    if ($stmt) {
        $stmt->bind_param('i', $id_aluno);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            while ($row = $resultado->fetch_assoc()) {
                array_push($rows, $row);
            }
            return $rows;
        }
    }
    return false;
}

// obter o resultado de um exame para um determinado aluno
function examResult($id_aluno, $id_exame)
{
    global $conn;

    $stmt = $conn->prepare("SELECT SUM(CASE WHEN respostas.opcao_respondida=perguntas.opcao_correta THEN perguntas.notas ELSE 0 END) AS obtido, exames.nome_exame, exames.total_notas, exames.passar_notas, exames.data FROM exames INNER JOIN perguntas ON exames.id=perguntas.id_exame INNER JOIN respostas ON respostas.id_pergunta=perguntas.id WHERE respostas.id_aluno = ? AND exames.id = ? GROUP BY exames.id");

    if ($stmt) {
        $stmt->bind_param('ii', $id_aluno, $id_exame);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado) {
            $row = $resultado->fetch_assoc();
            if(isset($row)){
                return $row;
            }
            else {
                $stmt = $conn->prepare("SELECT '0' AS obtido, exames.nome_exame, exames.total_notas, exames.passar_notas, exames.data FROM exames WHERE exames.id = ? ");

                if($stmt){
                    $stmt->bind_param('i', $id_exame);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    if ($resultado) {
                        $row = $resultado->fetch_assoc();
                        return $row;
                    }
                }
            }
        }
    }
}

// Obter resultado para este exame para este aluno
function getMeusResultados()
{
    $rows = array();
    $id_utilizador = $_SESSION['id_utilizador'];
    $temporizadores = getTemporizadores($id_utilizador);

    foreach ($temporizadores as $temporizador) {
        $id_exame = $temporizador['id_exame'];
        $tempoRestante = temporizadorRestante($id_exame);

        if ($tempoRestante <= 0) {
            $resultado = examResult($id_utilizador, $id_exame);
            if($resultado){
                array_push($rows, $resultado);
            }
        }
    }
    return $rows;
}
