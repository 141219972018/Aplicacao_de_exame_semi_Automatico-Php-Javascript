<?php

    require_once('../includes/funcoes.php');

    $response = new \stdClass();
    $formErrors = array();

    if(isset($_POST['id_exame'])){
        $id_exame = $_POST['id_exame'];
        $aluno = alunoConectado();
        $id_turma_aluno = $aluno['id_turma'];
        if(!isDataExists('exames', 'id', $id_exame)){
            array_push($formErrors, 'Suas entradas não são válidas!');
        } else{
            $exame = getExame($id_exame);
            $esta_ativo = $exame['esta_ativo'];
            if($esta_ativo == 1){
                $stmt = $conn->prepare("SELECT * FROM turma_de_axame WHERE id_exame = ? AND id_turma = ? LIMIT 1");
                if($stmt){
                    $stmt->bind_param('ii', $id_exame, $id_turma_aluno);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    if($resultado){
                        $row = $resultado->fetch_assoc();
                        if(!$row){
                            array_push($formErrors, 'O exame ainda não foi iniciado!');
                        }
                    } else{
                        array_push($formErrors, 'Suas entradas não são válidas!');
                    }
                } else{
                    array_push($formErrors, 'Erro. Por favor, tente novamente mais tarde');
                }
            } else{
                array_push($formErrors, 'O exame não está ativo');
            }
        }
    }

    if(empty($formErrors)){
        $restante = temporizadorRestante($id_exame);
        $response -> restante = $restante;
    }

    echo json_encode($response);

?>