<?php

class Api
{

    public function __construct()
    {
        /* listing */
        $this->procedimentos = get_option('bom_doutor_api_url') . 'procedures/list';
        $this->atendimentos = get_option('bom_doutor_api_url') . 'company/list-local';
        $this->especialidades = get_option('bom_doutor_api_url') . 'specialties/list';
        $this->profissionais = get_option('bom_doutor_api_url') . 'professional/list';
        $this->profissional = get_option('bom_doutor_api_url') . 'professional/search';
        $this->locais = get_option('bom_doutor_api_url') . 'company/list-local';
        $this->unidades = get_option('bom_doutor_api_url') . 'company/list-unity';

        /* find */
        $this->paciente = get_option('bom_doutor_api_url') . 'patient/search';
        $this->paciente_list = get_option('bom_doutor_api_url') . 'patient/list';
        $this->paciente_search = get_option('bom_doutor_api_url') . 'patient/search';

        /* create */
        $this->create_paciente = get_option('bom_doutor_api_url') . 'patient/create';
        $this->agendamento = get_option('bom_doutor_api_url') . 'appoints/new-appoint';

        $this->paciente_agendamentos = get_option('bom_doutor_api_url') . 'appoints/search';

        $this->disponibilidade_horarios = get_option('bom_doutor_api_url') . 'appoints/available-schedule';
        $this->paciente_create = get_option('bom_doutor_api_url') . 'patient/create';
        $this->dependentes = get_option('bom_doutor_api_url') . 'patient/list-dependents';
        $this->tipos_procedimentos = get_option('bom_doutor_api_url') . 'procedures/types';
    }

    private function connectApi(string $url, string $type = 'GET', $body = ''): array
    {

        if ($type == 'GET') {
            $response = wp_remote_get(
                $url,
                [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'x-access-token' => get_option('bom_doutor_api_token')
                    ]
                ]
            );
        }

        if ($type == 'POST') {
            $response = wp_remote_post(
                $url,
                [
                    'body'    => $body,
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'x-access-token' => get_option('bom_doutor_api_token')
                    ]
                ]
            );
        }

        if (is_wp_error($response)) {
            return ['error' => 'Erro ao chamar a API'];
        }

        $status_code = wp_remote_retrieve_response_code($response);

        if ($status_code !== 200) {
            return ['error' => $status_code];
        }

        $data = wp_remote_retrieve_body($response);

        return json_decode($data, true);
    }

    public function validar_paciente_id($paciente_id){
        $paciente = $this->connectApi($this->paciente . '?paciente_id=' . $paciente_id);
        return $paciente;
    }

    public function validar_paciente_cpf($paciente_cpf, $user_id)
    {
        $paciente = $this->connectApi($this->paciente_search . '?paciente_cpf=' . $paciente_cpf);

        if(isset($paciente['success'])){

            $user_id_feegow = get_field('user_id_feegow', 'user_' . $user_id);

            if(empty($user_id_feegow) ){
                update_field('user_id_feegow', $paciente['content']['id'], 'user_' . $user_id);
                echo json_encode($paciente);
            }else{
                echo json_encode($paciente);
            }

        }else{
            echo json_encode([
                'success' => false
            ]);
        }

    }

    public function js_route_get($route, $body){
        $response = $this->connectApi(get_option('bom_doutor_api_url') . $route.$body);
        return $response;
    }

    public function profissional_search($profissional_id)
    {
        $profissional = $this->connectApi($this->profissional . "?profissional_id=$profissional_id");
        return $profissional;
    }

    public function horarios($procedimento_id, $unidade_id, $data_start, $data_end)
    {
        //$response = $this->connectApi($this->disponibilidade_horarios . '?tipo=P&procedimento_id=11&unidade_id=3&data_start=22-02-2023&data_end=28-03-2023');
        $response = $this->connectApi($this->disponibilidade_horarios . '?tipo=P&procedimento_id=' . $procedimento_id . '&unidade_id=' . $unidade_id . '&data_start=' . $data_start . '&data_end=' . $data_end);
        $data = [];
        if (isset($response['content']['profissional_id'])) {
            foreach ($response['content']['profissional_id'] as $profissional_id => $profissional) {
                $profissional_info = $this->profissional_search($profissional_id)['content']['informacoes'][0];

                $data['info'] = [
                    'profissionais_disponiveis' => count($response['content']['profissional_id'])
                ];
                $data['not_filter'] = [
                    $response['content']['profissional_id']
                ];

                $horarios  = [];
                foreach (reset($profissional) as $dias => $value) {
                    $horarios  = array_merge($horarios , reset($profissional)[$dias]);
                }

                $data_consulta  = [];
                foreach ($horarios as $disponibilidade => $value) {
                    $data_consulta[]  = $disponibilidade;
                }

                $data['profissionais'][] = [
                    'profissional_id' => $profissional_id,
                    'tratamento' => "",
                    'nome' => $profissional_info['nome'],
                    'foto' => $profissional_info['foto'],
                    'sexo' => $profissional_info['sexo'],
                    'conselho' => $profissional_info['conselho'],
                    'documento_conselho' => $profissional_info['documento_conselho'],
                    'data' => $data_consulta,
                    'horarios_disponiveis' => $horarios,
                ];
            }
            return $data;
        }
    }

    public function list($list, $where = false)
    {
        $response = [
            'procedimentos' => $this->connectApi($this->procedimentos)['content'],
            'atendimentos' => $this->connectApi($this->atendimentos)['content'],
            'especialidades' => $this->connectApi($this->especialidades)['content'],
            'profissionais' => $this->connectApi($this->profissionais)['content'],
            'locais' => $this->connectApi($this->locais)['content'],
            'unidades' => $this->connectApi($this->unidades)['content'],
        ];

        if ($where) {
            $listing = $response[$list];
            $param = $where[0];
            $value = $where[1];
            return array_filter($listing, function ($item) use ($param, $value) {
                return isset($item[$param]) && $item[$param] == $value;
            });
        }

        return $response[$list];
    }

    public function paciente_by_cpf($paciente_cpf)
    {
        $paciente = $this->connectApi($this->paciente . '?paciente_cpf=' . $paciente_cpf);
        $dependente = null;

        if (isset($paciente['error'])) {
            if ($paciente['error'] == 409) {
                return;
            } else {
                return [
                    "status" => "erro",
                    "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
                ];
            }
        }

        if (isset($paciente['content'])) {
            $dependente = $this->connectApi($this->dependentes . '?paciente_id=' . $paciente['content']['id'])['content'];
        }

        return [
            'status' => $paciente['success'] ?? false,
            'paciente' => $paciente['content'] ?? null,
            'dependente' => $dependente
        ];
    }

    public function paciente_by_id($paciente_id)
    {
        $paciente = $this->connectApi($this->paciente . '?paciente_id=' . $paciente_id);
        $dependente = null;

        if (isset($paciente['error'])) {
            if ($paciente['error'] == 409) {
                return;
            } else {
                return [
                    "status" => "erro",
                    "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
                ];
            }
        }

        if (isset($paciente['content'])) {
            $dependente = $this->connectApi($this->dependentes . '?paciente_id=' . $paciente_id)['content'];
        }

        return [
            'status' => $paciente['success'] ?? false,
            'paciente' => $paciente['content'] ?? null,
            'dependente' => $dependente
        ];
    }

    public function create_paciente($paciente_nome, $paciente_cpf, $paciente_email = "", $paciente_data_nascimento = "", $paciente_sexo = "", $paciente_telefone = "")
    {

        if (!empty(self::paciente_by_cpf($paciente_cpf)['paciente'])) {
            return [
                "status" => "sucesso",
                "mensagem" => "Paciente já existe"
            ];
        } else {

            $paciente_create = $this->connectApi($this->paciente_create, 'POST', json_encode([
                "nome_completo" => $paciente_nome,
                "cpf" => $paciente_cpf,
                'email' =>  $paciente_email,
                'data_nascimento' => date('Y-m-d', strtotime($paciente_data_nascimento)),
                'sexo' => $paciente_sexo,
                'telefone' => $paciente_telefone,
            ]));

            if (isset($paciente_create['error'])) {
                return [
                    "status" => "erro",
                    "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token ) " . $paciente_create['error']
                ];
            } else {
                return [
                    "status" => "sucesso",
                    "mensagem" => "Paciente criado com sucesso",
                    "content" => $paciente_create['content']
                ];
            }
        }
    }

    public function create_agendamento($local_id, $paciente_id, $profissional_id, $procedimento_id, $especialidade_id, $data, $horario, $valor, $plano = 0)
    {
        $agendamento = $this->connectApi($this->agendamento, 'POST', json_encode([
            "local_id" => $local_id,
            "paciente_id" => $paciente_id,
            'profissional_id' =>  $profissional_id,
            'procedimento_id' =>  $procedimento_id,
            'especialidade_id' =>  $especialidade_id,
            'data' => date('d-m-Y', strtotime($data)),
            'horario' =>  $horario,
            'valor' => $valor,
            'plano' => $plano
        ]));

        if (isset($agendamento['error'])) {
            return [
                "status" => "erro",
                "mensagem" => $agendamento,
            ];
        } else {
            return [
                "status" => "sucesso",
                "mensagem" => "Agendamento realizado com sucesso",
                "content" => $agendamento['content']
            ];
        }
    }








    private function getEspecialidadesByIdArray($procedimentos_especialidades_array, array $especialidades)
    {
        $response = [];

        if (empty($procedimentos_especialidades_array)) {
            return false;
        }

        foreach ($procedimentos_especialidades_array as $procedimento_especialidade) {
            $especialidade_id = $procedimento_especialidade;
            $especialidade_nome = '';

            foreach ($especialidades as $especialidade) {
                if ($procedimento_especialidade == $especialidade['especialidade_id']) {
                    $especialidade_nome = $especialidade['nome'];
                    break;
                }
            }

            if ($especialidade_nome !== '') {
                $response[] = [
                    'especialidade_id' => $especialidade_id,
                    'especialidade_nome' => $especialidade_nome
                ];
            }
        }

        return $response;
    }

    public function listAtendimentos()
    {
        $atendimentos = $this->connectApi($this->atendimentos);

        if (isset($atendimentos['error'])) {
            return [
                "status" => "erro",
                "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
            ];
        }

        $response = [];
        foreach ($atendimentos['content'] as $atendimento) {
            if (!empty($atendimento['local'])) {
                $response[] = [
                    'id' => $atendimento['id'],
                    'local' => $atendimento['local']
                ];
            }
        }

        return $response;
    }

    public function listEspecialidades()
    {
        $especialidades = $this->connectApi($this->especialidades);

        if (isset($especialidades['error'])) {
            return [
                "status" => "erro",
                "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
            ];
        }

        $response = [];
        foreach ($especialidades['content'] as $especialidade) {

            $response[] = [
                'especialidade_id' => $especialidade['especialidade_id'],
                'nome' => $especialidade['nome']
            ];
        }

        return $response;
    }

    public function getEspecialidadeByProcedimentoId($procedimento_id)
    {
        $especialidades = $this->connectApi($this->especialidades);

        if (isset($especialidades['error'])) {
            return [
                "status" => "erro",
                "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
            ];
        }

        $procedimentos_lista = $this->connectApi($this->procedimentos)['content'];

        $response = [];
        foreach ($especialidades['content'] as $especialidade) {

            $response[] = [
                'especialidade_id' => $especialidade['especialidade_id'],
                'nome' => $especialidade['nome']
            ];
        }

        return $procedimentos_lista;
    }

    public function listUnidades()
    {
        $unidades = $this->connectApi($this->unidades);

        if (isset($unidades['error'])) {
            return [
                "status" => "erro",
                "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
            ];
        }

        $response = [];

        foreach ($unidades['content']['unidades'] as $unidade) {

            $endereco = $unidade['endereco'];
            $numero = $unidade['numero'];
            $bairro = $unidade['bairro'];
            $estado = $unidade['estado'];
            $cep = $unidade['cep'];

            $response[] = [
                'id' => $unidade['unidade_id'],
                'local_id' => $this->listLocais($unidade['unidade_id']),
                'cidade' => $unidade['cidade'],
                'endereco' => "$endereco, $numero, $bairro - $estado | $cep"
            ];
        }

        return $response;
    }

    public function listProcedimentos()
    {

        $procedimentos = $this->connectApi($this->tipos_procedimentos)['content'];

        if (isset($procedimentos['error'])) {
            return [
                "status" => "erro",
                "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
            ];
        }
        $procedimentos_lista = $this->connectApi($this->procedimentos)['content'];

        $response = [];
        foreach ($procedimentos as $procedimento) {
            $procedimento_id = '';
            $procedimento_nome = '';
            foreach ($procedimentos_lista as $procedimento_lista) {
                if ($procedimento['id'] == $procedimento_lista['tipo_procedimento'] && $procedimento_lista['especialidade_id']) {

                    $procedimento_id = $procedimento['id'];
                    $procedimento_nome = $procedimento['tipo'];
                }
            }

            //$listarEspecialidadesPorProcedimento = $this->listarEspecialidadesPorProcedimento($procedimento_id);

            if ($procedimento_id != "" && $procedimento_nome != "") {
                $response[] = [
                    'procedimento_id' => $procedimento_id,
                    'procedimento_nome' => $procedimento_nome,
                    //'especialidades' => $listarEspecialidadesPorProcedimento['especialidades']
                ];
            }
        }

        return $response;
    }

    public function listLocais($unidade_id)
    {
        $locais = $this->connectApi($this->locais);

        if (isset($locais['error'])) {
            return [
                "status" => "erro",
                "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
            ];
        }

        $response = [];
        foreach ($locais['content'] as $local) {
            if ($local['unidade_id'] == $unidade_id) {

                return $local['unidade_id'];
            }
        }
        return $response ?: false;
    }

    public function listarProcedimentosPorEspecialidade($especialidade_id, $procedimento_id)
    {
        $procedimentos = $this->connectApi($this->procedimentos);

        if (isset($procedimentos['error'])) {
            return [
                "status" => "erro",
                "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
            ];
        }

        $filteredProcedures = array_filter($procedimentos['content'], function ($item) use ($especialidade_id, $procedimento_id) {
            return (isset($item['tipo_procedimento']) && isset($item['especialidade_id']) && $item['tipo_procedimento'] == $procedimento_id && in_array($especialidade_id, $item['especialidade_id']));
        });

        $filteredProcedure = reset($filteredProcedures);

        if ($filteredProcedure) {
            return [
                'tipo_procedimento' => $filteredProcedure['tipo_procedimento'],
                'valor' => $filteredProcedure['valor'],
                'api_response' => $filteredProcedure
            ];
        } else {
            return [
                'error' => 'Valor não encontrado'
            ];
        }
    }

    public function listarEspecialidadesPorProcedimento($tipo_procedimento)
    {
        $procedimentos = $this->connectApi($this->procedimentos);

        if (isset($procedimentos['error'])) {
            return [
                "status" => "erro",
                "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
            ];
        }

        $filteredProcedures = array_filter($procedimentos['content'], function ($item) use ($tipo_procedimento) {
            return ($item['tipo_procedimento'] == $tipo_procedimento);
        });

        $filteredProcedure = reset($filteredProcedures);
        $especialidades = $this->listEspecialidades();

        $lista_especialidades = [];

        foreach ($filteredProcedures as  $filter) {
            if (is_array($filter['especialidade_id'])) {

                $lista_especialidades[] = $filter['especialidade_id'];
            }
        }

        $filter_array = array();
        array_walk_recursive($lista_especialidades, function ($val) use (&$filter_array) {
            $filter_array[] = $val;
        });

        $listing = $this->getEspecialidadesByIdArray(array_unique($filter_array), $especialidades);

        if ($filteredProcedure) {
            return [
                'tipo_procedimento' => $filteredProcedure['procedimento_id'],
                'especialidades' => $listing
            ];
        } else {
            return [
                'error' => 'Valor não encontrado'
            ];
        }
    }

    public function listEspecialidadeByUnidade($unidade_id)
    {

        $especialidades = $this->connectApi($this->especialidades . '?UnitID=' . $unidade_id);

        if (isset($especialidades['error'])) {
            return [
                "status" => "erro",
                "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
            ];
        }

        $response = [];
        foreach ($especialidades['content'] as $especialidade) {

            $response[] = [
                'especialidade_id' => $especialidade['especialidade_id'],
                'nome' => $especialidade['nome']
            ];
        }

        return $response;
    }

    public function listProfissionaisHorarios($unidade_id, $especialidade_id, $procedimento_id, $data_start, $data_end, $all = null)
    {

        if ($all) {
            return $this->connectApi($this->profissionais);
        }

        $profissionais = [];
        foreach ($especialidade_id as $especialidade) {
            $profissionais[] = [
                'procedimento' => $procedimento_id,
                'profissionais' => $this->connectApi($this->profissionais . "?unidade_id=$unidade_id&especialidade_id=$especialidade")['content']
            ];
            $profissionais['item_especialidade'] = $especialidade;
        }

        $response = [];

        foreach ($profissionais as $profissional_list) {

            if (isset($profissional_list['profissionais'])) {
                foreach ($profissional_list['profissionais'] as $profissional) {

                    $horarios = $this->disponibilidade_horarios(
                        $procedimento_id,
                        $unidade_id,
                        date("d-m-Y", strtotime($data_start)),
                        date("d-m-Y", strtotime($data_start))
                    );

                    $response['profissionais'][] = [
                        'profissional_id' => $profissional['profissional_id'],
                        'tratamento' => $profissional['tratamento'],
                        'nome' => $profissional['nome'],
                        'foto' => $profissional['foto'],
                        'sexo' => $profissional['sexo'],
                        'conselho' => $profissional['conselho'],
                        'documento_conselho' => $profissional['documento_conselho'],
                        'horarios_disponiveis' => $horarios,
                    ];
                }
            }
        }

        $response = [
            'unidade_id' => $unidade_id,
            'especialidade_id' => $especialidade_id,
            'data_start' => $data_start,
            'data_end' => $data_end,
            'profissionais' => $response['profissionais'] ?? 'Nenhum profisional encontrado para essa "unidade/especialidade" ou "unidade/especialidade" não criada'
        ];

        return $response;
    }

    public function getPacienteByCPF($paciente_cpf)
    {
        $paciente = $this->connectApi($this->paciente . '?paciente_cpf=' . $paciente_cpf);
        $dependente = null;

        if (isset($paciente['error'])) {
            if ($paciente['error'] == 409) {
                return;
            } else {
                return [
                    "status" => "erro",
                    "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
                ];
            }
        }

        if (isset($paciente['content'])) {
            $dependente = $this->connectApi($this->dependentes . '?paciente_id=' . $paciente['content']['id'])['content'];
        }

        return [
            'status' => $paciente['success'] ?? false,
            'paciente' => $paciente['content'] ?? null,
            'dependente' => $dependente
        ];
    }

    public function getPacienteByIDOrCpf($paciente_id, $paciente_cpf = null)
    {
        if (empty($paciente_cpf)) {
            $paciente = $this->connectApi($this->paciente . '?paciente_id=' . $paciente_id);
        } else {
            $paciente = $this->connectApi($this->paciente . '?paciente_cpf=' . $paciente_cpf);
        }
        $dependente = null;

        if (isset($paciente['error'])) {
            if ($paciente['error'] == 409) {
                return;
            } else {
                return [
                    "status" => "erro",
                    "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
                ];
            }
        }

        if (isset($paciente['content'])) {
            $dependente = $this->connectApi($this->dependentes . '?paciente_id=' . $paciente_id)['content'];
        }

        return [
            'status' => $paciente['success'] ?? false,
            'paciente' => $paciente['content'] ?? null,
            'dependente' => $dependente
        ];
    }

    public function getPaciente($paciente_id)
    {
        $paciente = $this->connectApi($this->paciente . '?paciente_id=' . $paciente_id);
        $dependente = null;

        if (isset($paciente['error'])) {
            if ($paciente['error'] == 409) {
                return;
            } else {
                return [
                    "status" => "erro",
                    "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
                ];
            }
        }

        return json_encode($paciente);
    }

    public function createPaciente($paciente_nome, $paciente_cpf, $paciente_email = "", $paciente_data_nascimento = "", $paciente_sexo = "", $paciente_telefone = "")
    {

        if (!empty(self::getPacienteByCPF($paciente_cpf)['paciente'])) {
            return [
                "status" => "sucesso",
                "mensagem" => "Paciente já existe"
            ];
        } else {

            $paciente_create = $this->connectApi($this->paciente_create, 'POST', json_encode([
                "nome_completo" => $paciente_nome,
                "cpf" => $paciente_cpf,
                'email' =>  $paciente_email,
                'data_nascimento' => date('Y-m-d', strtotime($paciente_data_nascimento)),
                'sexo' => $paciente_sexo,
                'telefone' => "$paciente_telefone"
            ]));

            if (isset($paciente_create['error'])) {
                return [
                    "status" => "erro",
                    "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token ) " . $paciente_create['error']
                ];
            } else {
                return [
                    "status" => "sucesso",
                    "mensagem" => "Paciente criado com sucesso",
                    "content" => $paciente_create['content']
                ];
            }
        }
    }

    public function createAgendamento($local_id, $paciente_id, $profissional_id, $procedimento_id, $especialidade_id, $data, $horario, $valor, $plano = 0)
    {
        $agendamento = $this->connectApi($this->agendamento, 'POST', json_encode([
            "local_id" => $local_id,
            "paciente_id" => $paciente_id,
            'profissional_id' =>  $profissional_id,
            'procedimento_id' =>  $procedimento_id,
            'especialidade_id' =>  $especialidade_id,
            'data' => date('d-m-Y', strtotime($data)),
            'horario' =>  $horario,
            'valor' => $valor,
            'plano' => $plano
        ]));

        if (isset($agendamento['error'])) {
            return [
                "status" => "erro",
                "mensagem" => $agendamento,
            ];
        } else {
            return [
                "status" => "sucesso",
                "mensagem" => "Agendamento realizado com sucesso",
                "content" => $agendamento['content']
            ];
        }
    }


    public function paciente_agendamentos($paciente_id, $data_start, $date_end)
    {
        return $this->connectApi($this->paciente_agendamentos . "?paciente_id=$paciente_id&data_start=$data_start&data_end=$date_end");
    }
}
