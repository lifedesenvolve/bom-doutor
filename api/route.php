<?php

function rotas_bom_doutor()
{
  register_rest_route('api/v1', '/lista-especialidades/', array(
    'methods'  => 'GET',
    'callback' => 'lista_especialidades',
  ));

  register_rest_route('api/v1', '/lista-profissionais/', array(
    'methods'  => 'GET',
    'callback' => 'lista_profissionais',
    'args' => array(
      'unidade' => array(
        'validate_callback' => function ($param, $request, $key) {
          return is_numeric($param);
        }
      ),
      'especialidade' => array(
        'validate_callback' => function ($param, $request, $key) {
          return is_numeric($param);
        }
      ),
      'data' => array(
        'validate_callback' => function ($param, $request, $key) {
          return preg_match('/^\d{4}-\d{2}-\d{2}$/', $param);
        }
      ),
    ),
  ));

  register_rest_route('api/v1', '/registrar-paciente/', array(
    'methods'  => 'POST',
    'callback' => 'registrar_paciente',
  ));

  register_rest_route('api/v1', '/registrar-agendamento/', array(
    'methods'  => 'POST',
    'callback' => 'registrar_agendamento',
  ));
}
add_action('rest_api_init', 'rotas_bom_doutor');

function lista_especialidades()
{

  $api = new Api();
  $lista_especialidades = $api->listEspecialidades();
  return $lista_especialidades;
}

function lista_profissionais($request)
{
  $unidade = $request->get_param('unidade');
  $especialidade = $request->get_param('especialidade');
  $data = $request->get_param('data');

  $api = new Api();

  $lista_profissionais = $api->listProfissionaisHorarios($unidade, $especialidade, $data, $data);
  return $lista_profissionais;
}

function registrar_paciente($request)
{
  $dados = $request->get_json_params();

  $api = new Api();
  $resultado = $api->createPaciente(
    $dados['nome_titular'],
    $dados['cpf_titular'],
    $dados['email_titular'],
    $dados['data_nascimento_titular'],
    $dados['genero_titular'],
    $dados['telefone_titular']
  );

  if ($resultado['status'] == "sucesso") {
    $response = [
      "status" => "sucesso",
      "mensagem" => $resultado['mensagem'],
      "content" => $resultado['content']
    ];

    if (get_field('user_id', 'user_' . $dados['user_id']) === "") {
      update_field('user_id', $resultado['content']['paciente_id'], 'user_' . $dados['user_id']);
    }
  } else {
    $response = [
      "status" => "erro",
      "mensagem" => $resultado['mensagem']
    ];
  }
  echo json_encode($response);
}

function registrar_agendamento($request)
{
  $dados = $request->get_json_params();

  $api = new Api();
  $resultado = $api->createAgendamento(
    $dados['filtro__unidade'],
    $dados['paciente_id'],
    $dados['profissional_id'],
    $dados['procedimento_id'],
    $dados['filtro__especialidades'],
    $dados['filtro__data'],
    $dados['horario_escolhido']
  );

  if ($resultado['status'] == "sucesso") {
    $response = [
      "status" => "sucesso",
      "mensagem" => $resultado['mensagem']
    ];
  } else {
    $response = [
      "status" => "erro",
      "mensagem" => $resultado['mensagem']
    ];
  }
  echo json_encode($response);
  /*  echo json_encode([
    "status" => "erro",
    "mensagem" => 'teste'
  ]); */
}
