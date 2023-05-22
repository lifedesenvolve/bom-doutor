<?php

function page_usuario_agendamento_shortcode()
{
    $plugin_dir_path = plugin_dir_path(dirname(__FILE__));
    $image_path = plugins_url('assets/image/', $plugin_dir_path);
    wp_enqueue_style('dados-usuario-css');
    if (is_user_logged_in()) {
        $usuario = wp_get_current_user();
        $user_id_feegow = get_field('user_id_feegow', 'user_' . $usuario->ID);

        $dataAtual = date('Y-m-d');
        $dataSubtraida = date('Y-m-d', strtotime('-1 month', strtotime($dataAtual)));
        $dataFormatada = date('d-m-Y', strtotime($dataSubtraida));
?>


        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <section class="lista-agendamentos container-fluid m-0">
        </section>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.0/js/bootstrap.min.js"></script>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/6.0.0/bootbox.js" integrity="sha512-kwtW9vT4XIHyDa+WPb1m64Gpe1jCeLQLorYW1tzT5OL2l/5Q7N0hBib/UNH+HFVjWgGzEIfLJt0d8sZTNZYY6Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            function js_route_get(route, content) {
                const base_url = '<?php echo home_url(); ?>';
                const options = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        route: route,
                        body: content
                    })
                };

                return fetch(`${base_url}/wp-json/api/v1/js-route-get`, options)
                    .then(response => response.json());
            }

            function buscarAgendamentos(paciente_id, data_start) {

                const base_url = '<?php echo home_url(); ?>';
                const options = {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                };

                fetch(`${base_url}/wp-json/api/v1/paciente-agendamentos?paciente_id=${paciente_id}&data_start=${data_start}`, options)
                    .then(response => response.json())
                    .then(response => {
                        const lista_agendamentos = document.querySelector('.lista-agendamentos');
                        if (response.success) {

                            const agendamento_data = response.content;

                            fetch('<?php echo home_url(); ?>/wp-json/feegow/table-data')
                                .then(response => response.json())
                                .then(data => {

                                    agendamento_data.forEach((agendamento) => {

                                        const tipo_procedimentos = data.tipo_procedimento;
                                        const procedimentos = data.procedimento;
                                        const especialistas = data.especialista;

                                        const procedimento = procedimentos.filter((item) => item.id_procedimento == agendamento.procedimento_id);
                                        const tipo_procedimento = tipo_procedimentos.filter((item) => item.id_tipo_procedimento == procedimento[0].id_tipo_procedimento);
                                        const especialista = especialistas.filter((item) => item.id_especialista == agendamento.profissional_id);

                                        let html_tipo_procedimento = tipo_procedimento[0].nome;
                                        let html_procedimento = procedimento[0].nome;
                                        let html_especialista = especialista[0].nome;

                                        lista_agendamentos.innerHTML += `
                                        <div class="container-fluid py-3 px-5 py-sm-4 py-md-5 m-0 meus-agendamentos">
                                            <div class="row py-3" data-agendamento-id="${agendamento.agendamento_id}">
                                                <div class="col-12 col-md-6">
                                                    <h2 class="card-title tipo-procedimento-nome">${html_tipo_procedimento}</h2>
                                                    <p class="anotacoes">${agendamento.notas}</p>
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <h3 class="card-title procedimento-nome">${html_procedimento}</h3>
                                                    <div class="container-fluid p-0">
                                                        <div class="row py-1">
                                                            <div class="col-1"><img src="<?= $image_path . 'user.png' ?>" alt=""></div>
                                                            <div class="col-11 profissional-nome">${html_especialista}</div>
                                                        </div>
                                                        <div class="row py-1">
                                                            <div class="col-1"><img src="<?= $image_path . 'calendar.png' ?>" alt=""></div>
                                                            <div class="col-md-3 data">${agendamento.data}</div>
                                                            <div class="col-1"><img src="<?= $image_path . 'clock.png' ?>" alt=""></div>
                                                            <div class="col-md-3 horario">${agendamento.horario}</div>
                                                        </div>
                                                        <div class="row py-1">
                                                            <div class="col-1"><img src="<?= $image_path . 'map-pin.png' ?>" alt=""></div>
                                                            <div class="col-11 endereco"></div>
                                                        </div>
                                                        ${
                                                            agendamento.status_id !== 2 &&
                                                            agendamento.status_id !== 3 &&
                                                            agendamento.status_id !== 5 &&
                                                            agendamento.status_id !== 6 &&
                                                            agendamento.status_id !== 11 &&
                                                            agendamento.status_id !== 105
                                                            ? `
                                                            <div class="row py-3">
                                                                <div class="col-md-3"><button onClick="remarcar_agendamento('<?php echo date('Y-m-d'); ?>', '${procedimento[0].id}', '${tipo_procedimento[0].id}', '3', ${agendamento.agendamento_id})" class="btn btn-sm btn-info">Reagendar</button></div>
                                                                <div class="col-md-3"><button onClick="remarcar_agendamento('<?php echo date('Y-m-d'); ?>', '${procedimento[0].id}', '${tipo_procedimento[0].id}', '3', ${agendamento.agendamento_id})" class="btn btn-sm btn-info">Cancelar</button></div>
                                                                <div class="col-md-6"></div>
                                                            </div>
                                                         ` : `
                                                            <div class="row py-3">
                                                                <span class="status_agendamento">
                                                                Status: 
                                                                ${ agendamento.status_id == 2 ? 'Em atendimento' : '' }
                                                                ${ agendamento.status_id == 3 ? 'Atendido' : '' }
                                                                ${ agendamento.status_id == 5 ? 'Chamando | atendimento' : '' }
                                                                ${ agendamento.status_id == 6 ? 'Não compareceu' : '' }
                                                                ${ agendamento.status_id == 11 ? 'Desmarcado pelo paciente' : '' }
                                                                ${ agendamento.status_id == 105 ? 'Chamando | Triagem' : '' }
                                                                </span>
                                                            </div>
                                                         `}
                                                    </div>
                                                </div>
                                                <hr class="col-12">
                                            </div>
                                        </div>
                                        `;

                                        const lista_atual = document.querySelector(`[data-agendamento-id="${agendamento.agendamento_id}"]`);
                                        const endereco = js_route_get('company/list-unity', `?unidade_id=${agendamento.unidade_id}`);

                                        document.querySelector('.meus-agendamentos').style = "display:none";

                                        endereco.then(response => {
                                            const enderecoFormatado = formatarEndereco(response.content.unidades[0]);
                                            lista_atual.querySelector('.endereco').innerHTML = enderecoFormatado;
                                            console.log(enderecoFormatado);
                                            document.querySelector('.meus-agendamentos').style = "display:block";

                                        }).catch(err => console.error(err));

                                    });
                                })
                                .catch(error => console.error(error));

                                if(response.content == ''){
                                    document.querySelector('.lista-agendamentos').innerHTML = `
                                        <div class="container-fluid py-3 px-5 py-sm-4 py-md-5 m-0 meus-agendamentos">
                                            <h1>Agendamentos</h1>
                                            <div class="container">
                                                <div class="row d-flex justify-content-center text-center">
                                                    <h4>Você não tem agendamentos</h4>
                                                    <button class="btn btn-primary w-25">
                                                        Agende uma consulta
                                                    <button>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }
                        }
                    })
                    .catch(err => console.error(err));
            }

            function formatarEndereco(endereco) {
                const {
                    numero,
                    complemento,
                    endereco: logradouro,
                    bairro,
                    cidade,
                    estado
                } = endereco;
                const enderecoFormatado = `${logradouro}, nº ${numero}, ${complemento ? complemento + ' - ' : ''}${bairro}, ${cidade}, ${estado}.`;
                return enderecoFormatado;
            }

            buscarAgendamentos(<?php echo $user_id_feegow; ?>, '<?php echo $dataFormatada; ?>');


            function cancelar_agendamento(agendamento_id) {
                const base_url = '<?php echo home_url(); ?>';
                const options = {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        agendamento_id: agendamento_id
                    })
                };

                return fetch(`${base_url}/wp-json/api/v1/cancel-appoint/`, options)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => console.log(data))
                    .catch(error => console.error('There has been a problem with your fetch operation: ', error));
            }

            function remarcar_agendamento(data, procedimento_id, tipo_procedimento_id, unidade_id, agendamento_id) {

                const dados_filtro = {
                    filtro__data: data,
                    filtro__procedimento_id: procedimento_id,
                    filtro__tipo_procedimento: tipo_procedimento_id,
                    filtro__unidade_id: unidade_id
                }

                localStorage.setItem('@@bomdoutor:dados_filtro', JSON.stringify(dados_filtro));

                bootbox.confirm({
                    title: 'Você está prestes a encerrar esse atendimento',
                    message: 'tem certeza que deseja encerrar?',
                    buttons: {
                        cancel: {
                            label: '<i class="fa fa-times"></i> Cancelar essa ação'
                        },
                        confirm: {
                            label: '<i class="fa fa-check"></i> Sim, tenho certeza!'
                        }
                    },
                    callback: function(result) {
                        const base_url = '<?php echo home_url(); ?>';
                        if (result) {
                            cancelar_agendamento(agendamento_id);
                            window.location.href = `${base_url}/agendar/#redirect`;
                        }
                    }
                });

            }
        </script>

<?php
    }else{
        header('location: '.site_url());
    }
}
add_shortcode('page_usuario_agendamento', 'page_usuario_agendamento_shortcode');
