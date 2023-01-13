<?php

class Api
{

    public function __construct()
    {
        /* GET */
        $this->atendimentos = get_option('bom_doutor_api_url') . 'company/list-local';
        $this->especialidades = get_option('bom_doutor_api_url') . 'specialties/list';
        $this->unidades = get_option('bom_doutor_api_url') . 'company/list-unity';
        $this->locais = get_option('bom_doutor_api_url') . 'company/list-local';
        $this->procedimentos = get_option('bom_doutor_api_url') . 'procedures/list';
        $this->profissionais = get_option('bom_doutor_api_url') . 'professional/list';
        $this->disponibilidade_horarios = get_option('bom_doutor_api_url') . 'appoints/available-schedule';
        $this->paciente = get_option('bom_doutor_api_url') . 'patient/search';
        $this->paciente_create = get_option('bom_doutor_api_url') . 'patient/create';
        $this->dependentes = get_option('bom_doutor_api_url') . 'patient/list-dependents';
        $this->agendamento = get_option('bom_doutor_api_url') . 'appoints/new-appoint';
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

    private function availableSchedule($profissional_id, $especialidade_id, $unidade_id, $data_start, $data_end)
    {
        $disponibilidade_horarios = $this->connectApi($this->disponibilidade_horarios . '?tipo=E&especialidade_id=' . $especialidade_id . '&unidade_id=' . $unidade_id . '&data_start=' . $data_start . '&data_end=' . $data_end);


        if (isset($disponibilidade_horarios['error'])) {
            return [
                "status" => "erro",
                "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
            ];
        }

        $response = [];

        foreach ($disponibilidade_horarios['content'] as $disponibilidade) {
            $response = $disponibilidade[$profissional_id]['local_id'];
        }

        return array_shift($response);
    }

    private function getEspecialidadesByIdArray($procedimentos_especialidades_array, array $especialidades): array
    {
        $response = [];

        if (empty($procedimentos_especialidades_array)) {
            $response[] = [
                'especialidade_id' => '',
                'especialidade_nome' => 'Nenhuma especialidade atribuída',
                'error' => true,
                'message' => 'Nenhuma especialidade atribuída a este procedimento: https://app2.feegow.com/v8/?P=Procedimentos&Pers=Follow'
            ];
            return $response;
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
                    'especialidade_nome' => $especialidade_nome,
                    'error' => false,
                    'message' => ''
                ];
            } else {
                $response[] = [
                    'especialidade_id' => $especialidade_id,
                    'especialidade_nome' => '',
                    'error' => true,
                    'message' => 'Especialidade não atribuída a um profissional: https://app2.feegow.com/v8/?P=Profissionais'
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

        $procedimentos = $this->connectApi($this->procedimentos);

        if (isset($procedimentos['error'])) {
            return [
                "status" => "erro",
                "mensagem" => "Erro na consulta verifique os campos ( API Url e API Token )"
            ];
        }

        $response = [];
        $especialidades = self::listEspecialidades();

        foreach ($procedimentos['content'] as $procedimento) {

            $response[] = [
                'tipo_procedimento' => $procedimento['tipo_procedimento'],
                'procedimento_id' => $procedimento['procedimento_id'],
                'procedimento_nome' => $procedimento['nome'],
                'valor' => $procedimento['valor'],
                'especialidades' => $this->getEspecialidadesByIdArray($procedimento['especialidade_id'], $especialidades)
            ];
        }

        return $response;
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

    public function listProfissionaisHorarios($unidade_id, $especialidade_id, $data_start, $data_end, $all = null)
    {

        if ($all) {
            return $this->connectApi($this->profissionais);
        }

        $profissionais = $this->connectApi($this->profissionais . "?unidade_id=$unidade_id&especialidade_id=$especialidade_id");

        if (isset($profissionais['error'])) {
            echo "Erro na consulta verifique os campos ( API Url e API Token ) " . $profissionais['error'];
            return;
        }

        $response = [];

        foreach ($profissionais['content'] as $profissional) {

            $response['profissionais'][] = [
                'profissional_id' => $profissional['profissional_id'],
                'tratamento' => $profissional['tratamento'],
                'nome' => $profissional['nome'],
                'foto' => $profissional['foto'],
                'sexo' => $profissional['sexo'],
                'conselho' => $profissional['conselho'],
                'documento_conselho' => $profissional['documento_conselho'],
                'horarios_disponiveis' => $this->availableSchedule(
                    $profissional['profissional_id'],
                    $especialidade_id,
                    $unidade_id,
                    $data_start,
                    $data_end
                )
            ];
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
            'paciente' => $paciente['content'] ?? null,
            'dependente' => $dependente
        ];
    }

    public function getPacienteByID($paciente_id)
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
}
