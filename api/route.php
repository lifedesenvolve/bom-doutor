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
        'validate_callback' => function($param, $request, $key) {
          return is_numeric($param);
        }
      ),
      'especialidade' => array(
        'validate_callback' => function($param, $request, $key) {
          return is_numeric($param);
        }
      ),
      'data' => array(
        'validate_callback' => function($param, $request, $key) {
          return preg_match('/^\d{4}-\d{2}-\d{2}$/', $param);
        }
      ),
    ),
  ));

  register_rest_route('api/v1', '/registrar-paciente/', array(
    'methods'  => 'POST',
    'callback' => 'registrar_paciente',
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
  $dados = $request->get_params();
  if (is_user_logged_in()) {
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

    if ($resultado) {
      return wp_send_json(array(
        'status' => 'sucesso',
        'mensagem' => 'Paciente registrado com sucesso'
      ));
    } else {
      return wp_send_json(array(
        'status' => 'erro',
        'mensagem' => 'Não foi possível registrar o paciente'
      ));
    }
  }
}
//74275703081