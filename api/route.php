<?php

function rotas_bom_doutor()
{
    register_rest_route('api/v1', '/get-especialidades/', array(
        'methods'  => 'GET',
        'callback' => 'getEspecialidadeByProcedimentoId',
    ));

    register_rest_route('api/v1', '/lista-procedimentos/', array(
        'methods'  => 'GET',
        'callback' => 'lista_procedimentos',
    ));

    register_rest_route('api/v1', '/lista-profissionais/', array(
        'methods'  => 'POST',
        'callback' => 'lista_profissionais'
    ));

    register_rest_route('api/v1', '/paciente/', array(
        'methods'  => 'GET',
        'callback' => 'get_paciente_by_ID_or_CPF',
        'args' => array(
            'paciente_id' => array(
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric($param);
                }
            ),
        ),
    ));

    register_rest_route('api/v1', '/registrar-paciente/', array(
        'methods'  => 'POST',
        'callback' => 'registrar_paciente',
    ));

    register_rest_route('api/v1', '/listar-agendamento/', array(
        'methods'  => 'GET',
        'callback' => 'listar_agendamento',
    ));

    register_rest_route('api/v1', '/registrar-agendamento/', array(
        'methods'  => 'POST',
        'callback' => 'registrar_agendamento',
    ));

    register_rest_route('api/v1', '/procedimento-valor/', array(
        'methods'  => 'GET',
        'callback' => 'procedimento_valor',
    ));

    register_rest_route('api/v1', '/listar-especialidades/', array(
        'methods'  => 'GET',
        'callback' => 'listar_especialidades',
    ));

    register_rest_route('api/v1', '/paciente-agendamentos/', array(
        'methods'  => 'GET',
        'callback' => 'paciente_agendamentos',
    ));

    register_rest_route('api/v1', '/js-route-get/', array(
        'methods'  => 'POST',
        'callback' => 'js_route_get',
    ));
}
add_action('rest_api_init', 'rotas_bom_doutor');

function getEspecialidadeByProcedimentoId($request)
{
    $api = new Api();
    $lista_especialidades = $api->getEspecialidadeByProcedimentoId($request['procedimento_id']);
    return $lista_especialidades;
}

function lista_procedimentos()
{
    $api = new Api();
    $lista_especialidades = $api->listProcedimentos();
    return $lista_especialidades;
}

function lista_profissionais($request)
{
    $procedimento_id = $request->get_param('procedimento_id');
    $unidade = $request->get_param('unidade');
    $date_start = date("d-m-Y", strtotime($request->get_param('data')));

    $date_end = new DateTime($date_start);
    $date_end->modify("+15 days");

    $api = new Api();
    $lista_profissionais = $api->horarios($procedimento_id, $unidade, $date_start, $date_end->format("d-m-Y"));
    echo json_encode($lista_profissionais);
}

function registrar_paciente($request)
{
    $dados = $request->get_json_params();

    $api = new Api();
    $resultado = $api->createPaciente(
        $dados['nome_titular'],
        $dados['cpf_titular'],
        $dados['email_titular'],
        $dados['data_nascimento'],
        $dados['genero_titular'],
        $dados['telefone_titular']
    );

    if ($resultado['status'] == "sucesso") {
        $response = [
            "status" => "sucesso",
            "mensagem" => $resultado['mensagem'],
            "content" => $resultado['content']
        ];

        if (get_field('user_id_feegow', 'user_' . $dados['user_id']) === "") {
            update_field('user_id_feegow', $resultado['content']['paciente_id'], 'user_' . $dados['user_id']);
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
        $dados['local_id'],
        $dados['paciente_id'],
        $dados['profissional_id'],
        $dados['procedimento_id'],
        $dados['especialidade_id'],
        $dados['data'],
        $dados['horario'],
        $dados['valor'],
        $dados['plano'],
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
}
function get_paciente_by_ID_or_CPF($request)
{
    $api = new Api();

    if (!empty($request->get_param('paciente_cpf'))) {
        $paciente = $request->get_param('paciente_cpf');
        $resultado = $api->getPacienteByIDOrCpf(
            "",
            $paciente
        );
    } else {
        $paciente = $request->get_param('paciente_id');
        $resultado = $api->getPacienteByIDOrCpf(
            $paciente
        );
    }

    echo json_encode($resultado);
}
function listar_agendamento()
{
    $api = new Api();
    $resultado = $api->listProcedimentos();
    echo json_encode($resultado);
}

function procedimento_valor($request)
{
    $api = new Api();
    $resultado = $api->listarProcedimentosPorEspecialidade($request['especialidade_id'], $request['tipo_procedimento']);
    echo json_encode($resultado);
}

function listar_especialidades($request)
{
    $api = new Api();
    $resultado = $api->listarEspecialidadesPorProcedimento($request['tipo_procedimento']);
    echo json_encode($resultado);
}

function paciente_agendamentos($request)
{
    $paciente_id = $request['paciente_id'];
    $data_start = $request['data_start'];
    $date_end = new DateTime($date_start);
    $date_end->modify("+30 days");

    $api = new Api();
    $resultado = $api->paciente_agendamentos($paciente_id, $data_start, $date_end->format("d-m-Y"));
    echo json_encode($resultado);
}


function js_route_get($request)
{
    $api = new Api();
    $response = $api->js_route_get($request['route'], $request['body']);
    echo json_encode($response);
}