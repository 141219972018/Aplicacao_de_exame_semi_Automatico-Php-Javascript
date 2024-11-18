<?php 
    require_once('../includes/funcoes.php');

    $response = new \stdClass();
    $formErrors = array();
    
    if(isset($_POST['id_pergunta']) && isset($_POST['opcao_selecionada'])){
        $id_utilizador = $_SESSION['id_utilizador'];
        $id_pergunta = $_POST['id_pergunta'];
        $opcao_selecionada = $_POST['opcao_selecionada'];
        $opcoes_validas = array('opcao_a', 'opcao_b', 'opcao_c', 'opcao_d', 'pular');

        if(!in_array($opcao_selecionada, $opcoes_validas)){
            array_push($formErrors, 'A opção selecionada não é válida');
        }

        if(!isDataExists('perguntas', 'id', $id_pergunta)){
            array_push($formErrors, 'O ID da pergunta não é válido');
        } else{
            $aluno = alunoConectado();
            $id_turma_aluno = $aluno['id_turma'];

            $pergunta = getPergunta($id_pergunta);
            $id_exame_pergunta  = $pergunta['id_exame'];

            $stmt = $conn->prepare("SELECT * FROM turma_de_axame WHERE id_exame = ? AND id_turma = ? LIMIT 1");
            if($stmt){
                $stmt->bind_param('ii', $id_exame_pergunta , $id_turma_aluno);
                $stmt->execute();
                $resultado = $stmt->get_result();
                if($resultado){
                    $row = $resultado->fetch_assoc();
                    if(!$row){
                        array_push($formErrors, 'Suas entradas não são válidas!');
                    }
                } else{
                    array_push($formErrors, 'Suas entradas não são válidas!');
                }
            } else{
                array_push($formErrors, 'Erro. Por favor, tente novamente mais tarde');
            }
        }

        if(empty($formErrors)){
            //verifique se o tempo não acabou
            if(temporizadorRestante($id_exame_pergunta ) > 0){
            //se a resposta já existir, atualize senão insira
                $stmt = $conn->prepare("SELECT * FROM respostas WHERE id_aluno = ? AND id_pergunta = ?");
                if($stmt){
                    $stmt->bind_param('ii', $id_utilizador, $id_pergunta);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    if($resultado){
                        $row = $resultado->fetch_assoc();
                        if($row){
                            //Atualizar
                            $stmt = $conn->prepare("UPDATE respostas SET opcao_respondida = ? WHERE id_aluno = ? AND id_pergunta = ?");
                            if($stmt){
                                $stmt->bind_param('sii', $opcao_selecionada, $id_utilizador, $id_pergunta);
                                if($stmt->execute()){
                                    $response->message = 'Updated';
                                    $response->id_pergunta = $id_pergunta;
                                    $response->opcao_selecionada = $opcao_selecionada;
                                }
                            } else{
                                array_push($formErrors, 'Erro. Por favor, tente novamente mais tarde');
                            }
                        } else{
                            //inserir
                            $stmt = $conn->prepare("INSERT INTO respostas(id_aluno, id_pergunta, opcao_respondida) VALUES(?, ? ,?)");
                            if($stmt){
                                $stmt->bind_param('iis', $id_utilizador, $id_pergunta, $opcao_selecionada);
                                if($stmt->execute()){
                                    $response->message = 'Inserted';
                                    $response->id_pergunta = $id_pergunta;
                                    $response->opcao_selecionada = $opcao_selecionada;
                                }
                            } else{
                                array_push($formErrors, 'Erro. Por favor, tente novamente mais tarde');
                            }
                        }
                    } else{
                        array_push($formErrors, 'Suas entradas não são válidas!');
                    }
                }
            }
        }
    } else{
        array_push($formErrors, 'Suas entradas não são válidas!');
    }

    $response->formErrors = $formErrors;
    echo json_encode($response);
?>
