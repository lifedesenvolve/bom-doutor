<?php

function page_usuario_agendamento_shortcode()
{ ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <section class="lista-agendamentos container-fluid">
    </section>

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
                        response.content.forEach((agendamento) => {

                            lista_agendamentos.innerHTML = `
                            <div class="row py-3" data-agendamento-id="${agendamento.agendamento_id}">
                            <div class="col-6">
                                <div class="card-title tipo-procedimento-nome"></div>
                                <p class="anotacoes">${agendamento.notas}</p>
                            </div>

                            <div class="col-6">
                                <div class="card-title procedimento-nome"></div>
                                <div class="container-fluid p-0">
                                    <div class="row py-1">
                                        <div class="col-1"><i class="fab fa-facebook"></i></div>
                                        <div class="col-11 profissional-nome"></div>
                                    </div>
                                    <div class="row py-1">
                                        <div class="col-1"><i class="fab fa-facebook"></i></div>
                                        <div class="col-3 data">${agendamento.data}</div>
                                        <div class="col-1"><i class="fab fa-facebook"></i></div>
                                        <div class="col-3 horario">${agendamento.horario}</div>
                                    </div>
                                    <div class="row py-1">
                                        <div class="col-1"><i class="fab fa-facebook"></i></div>
                                        <div class="col-11 endereco">${agendamento.local_id}</div>
                                    </div>
                                    <div class="row py-3">
                                        <div class="col-3"><button class="btn btn-sm btn-info">Reagendar</button></div>
                                        <div class="col-3"><button class="btn btn-sm btn-info">Cancelar</button></div>
                                        <div class="col-6"></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            </div>
                            `;

                            const lista_atual = document.querySelector(`[data-agendamento-id="${agendamento.agendamento_id}"]`);
                            const profissional = js_route_get('professional/search', `?profissional_id=${agendamento.profissional_id}`);
                            const procedimento = js_route_get('procedures/list', `?procedimento_id=${agendamento.procedimento_id}`);

                            profissional.then(response => {
                                let profissional_nome = response.content.informacoes[0].nome;
                                lista_atual.querySelector('.profissional-nome').innerHTML = profissional_nome;
                            }).catch(err => console.error(err));

                            procedimento.then(response => {
                                let procedimento_nome = response.content[0].nome;
                                let tipo_procedimento = response.content[0].tipo_procedimento;
                                let tipo_procedimento_nome = lista_atual.querySelector('.tipo-procedimento-nome');

                                if (tipo_procedimento == 2) {
                                    tipo_procedimento_nome.innerHTML = 'Consulta';
                                } else if (tipo_procedimento == 3) {
                                    tipo_procedimento_nome.innerHTML = 'Procedimento';
                                } else if (tipo_procedimento == 9) {
                                    tipo_procedimento_nome.innerHTML = 'Retorno';
                                }

                                lista_atual.querySelector('.procedimento-nome').innerHTML = procedimento_nome;
                            }).catch(err => console.error(err));

                        });

                    }
                })
                .catch(err => console.error(err));
        }

        buscarAgendamentos(149, '13-02-2023');

        function cancelar_agendamento() {
            //https://docs.feegow.com/#cancelar-agendamento
        }

        function remarcar_agendamento() {
            //https://docs.feegow.com/#remarcar-agendamento
        }
    </script>

<?php }
add_shortcode('page_usuario_agendamento', 'page_usuario_agendamento_shortcode');
