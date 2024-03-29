<?php
function page_agendamento_shortcode()
{
    wp_enqueue_style('page-agendamento-css');

    if (is_user_logged_in()) {
        $usuario = wp_get_current_user();
        $email = $usuario->user_email;
        $user_id_feegow = get_field('user_id_feegow', 'user_' . $usuario->ID);
        $usuario_id = $usuario->ID;
    }else{
        $usuario_id = -1;
    }

    if (empty($user_id_feegow)) {
        $user_id_feegow = -1;
    }
?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
<style>
    [disabled="disabled"] {
    background: #f1f1f1 !important;
}
</style>
    <div class="loader active" id="loader">
        <svg role="img" aria-label="Mouth and eyes come from 9:00 and rotate clockwise into position, right eye blinks, then all parts rotate and merge into 3:00" class="smiley" viewBox="0 0 128 128" width="128px" height="128px">
            <defs>
                <clipPath id="smiley-eyes">
                    <circle class="smiley__eye1" cx="64" cy="64" r="8" transform="rotate(-40,64,64) translate(0,-56)"></circle>
                    <circle class="smiley__eye2" cx="64" cy="64" r="8" transform="rotate(40,64,64) translate(0,-56)"></circle>
                </clipPath>
                <linearGradient id="smiley-grad" x1="0" y1="0" x2="0" y2="1">
                    <stop offset="0%" stop-color="#000"></stop>
                    <stop offset="100%" stop-color="#fff"></stop>
                </linearGradient>
                <mask id="smiley-mask">
                    <rect x="0" y="0" width="128" height="128" fill="url(#smiley-grad)"></rect>
                </mask>
            </defs>
            <g stroke-linecap="round" stroke-width="12" stroke-dasharray="175.93 351.86">
                <g>
                    <rect fill="#2A2860" width="128" height="64" clip-path="url(#smiley-eyes)"></rect>
                    <g fill="none" stroke="#62B5A8">
                        <circle class="smiley__mouth1" cx="64" cy="64" r="56" transform="rotate(180,64,64)"></circle>
                        <circle class="smiley__mouth2" cx="64" cy="64" r="56" transform="rotate(0,64,64)"></circle>
                    </g>
                </g>
                <g mask="url(#smiley-mask)">
                    <rect fill="#62B5A8" width="128" height="64" clip-path="url(#smiley-eyes)"></rect>
                    <g fill="none" stroke="#62B5A8">
                        <circle class="smiley__mouth1" cx="64" cy="64" r="56" transform="rotate(180,64,64)"></circle>
                        <circle class="smiley__mouth2" cx="64" cy="64" r="56" transform="rotate(0,64,64)"></circle>
                    </g>
                </g>
            </g>
        </svg>
    </div>

    <h1 class="titulo-especialidade" id="tituloEspecialidade"></h1>
    <?php if (is_user_logged_in()) { ?>
        <div class="logged"></div>
    <?php  } ?>

    <!-- inicio do loop -->
    <h3 class="info-data" style="display:none"></h3>
    <div class="lista-profissionais" id="listaProfissionais"></div>
    <div id="pagination"></div>
    <!-- fim do loop -->

    <!-- Modal -->
    <div class="modal fade modal-xl" id="modalAgendamento" tabindex="-1" aria-labelledby="stepModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="stepModal">Dados do Paciente</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="step-1">
                        <div class="steps mb-5">
                            <img src="<?php echo PLUGIN_URL . "/assets/image/etapa-1.png" ?>">
                        </div>

                        <input hidden class="form-control" id="horario_escolhido" name="horario_escolhido" type="text">
                        <input hidden class="form-control" id="profissional_escolhido" name="profissional_escolhido" type="text">
                        <input hidden class="form-control" id="valor_procedimento" name="valor_procedimento" type="text">
                        <input hidden class="form-control" id="id_user_feegow" name="id_user_feegow" type="text">

                        <div class="mb-3 px-5 row">
                            <label for="cpfTitular" class="col-sm-3 col-form-label">CPF</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control-plaintext" id="cpfTitular" name="cpf_titular" maxlength="14" placeholder="123.456.789-00">
                            </div>
                        </div>
                        <div class="mb-3 px-5 row">
                            <label for="nomeTitular" class="col-sm-3 col-form-label">Nome completo</label>
                            <div class="col-sm-9">
                                <input type="text" name="nome_titular" class="form-control-plaintext" id="nomeTitular" placeholder="Nome Completo">
                            </div>
                        </div>

                        <div class="mb-3 px-5 row">
                            <label for="genero" class="col-sm-3 col-form-label">Gênero</label>
                            <div class="col-sm-9">
                                <select required class="form-control" name="genero_titular" type="date" id="genero">
                                    <option value="">Não desejo informar</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Feminino</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 px-5 row">
                            <label for="data_aniversario" class="col-sm-3 col-form-label">Data Aniversário</label>
                            <div class="col-sm-9">
                                <input class="form-control" name="data_aniversario" type="date" id="data_aniversario">
                            </div>
                        </div>
                        <div class="mb-3 px-5 row">
                            <label for="telefone" class="col-sm-3 col-form-label">Telefone do titular</label>
                            <div class="col-sm-9">
                                <input class="form-control" name="telefone_titular" type="phone" id="telefone">
                            </div>
                        </div>

                        <input hidden class="form-control" name="email_titular" value="<?php echo $email; ?>" type="text">

                        <div class="d-flex px-5 justify-content-end">
                            <button type="button" class="btn btn-default cta" id="step1">Proxima Etapa</button>
                        </div>

                    </div>
                    <div class="step-2" style="display:none;">
                        <div class="steps mb-5"><img src="<?php echo PLUGIN_URL . "/assets/image/etapa-2.png" ?>"></div>
                        <div class="d-flex justify-content-center mb-5">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="pagamentoLocal">
                                <label class="form-check-label" for="pagamentoLocal">Pagamento na Clinica</label>
                            </div>

                        </div>
                        <div id="msgPagamento" class="d-flex justify-content-center mb-5"></div>
                        <div class="d-flex px-5 justify-content-end">
                            <button type="button" class="btn btn-default cta" id="step2">Proxima Etapa</button>
                        </div>
                    </div>
                    <div class="step-3" style="display:none;">
                        <div class="steps mb-5"><img src="<?php echo PLUGIN_URL . "/assets/image/etapa-3.png" ?>"></div>
                        <div class="dados-agendamento px-5" id="dadosAgendamento"></div>
                        <div id="mgsModal" class="d-flex justify-content-center m-5"></div>

                        <div class="d-flex px-5 justify-content-end">
                            <button type="button" class="btn btn-default cta" id="step3">confirmar agendamento</button>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row"><span class="col-sm-3"></span><span class="col-sm-9"></span></div>
    <script>
        filtro = JSON.parse(localStorage.getItem('@@bomdoutor:dados_filtro'));

        const lt_procedimentos = JSON.parse(localStorage.getItem('@@bomdoutor:dados_lista_procedimentos'));
        const idUserFeegow = <?php echo $user_id_feegow; ?>;
        if (idUserFeegow !== -1) {
            document.querySelector(`#id_user_feegow`).value = idUserFeegow
        }

        const urlPlugin = "<?php echo PLUGIN_URL; ?>"
        const listaProfissionais = document.querySelector(`#listaProfissionais`);
        const tituloEspecialidade = document.querySelector(`#tituloEspecialidade`);

        const url = `<?php echo home_url() ?>`;
        const dataValues = {};

        if (idUserFeegow !== -1) {
            const options = {
                headers: {
                    'Content-Type': 'application/json'
                }
            };
            fetch(`${url}/wp-json/api/v1/paciente/?paciente_id=${idUserFeegow}`, options)
                .then(response => response.json())
                .then(data => {
                    dadosPaciente = data;
                    //console.log(dadosPaciente);

                    if (dadosPaciente.status !== 'error') {
                        const nomeTitular = document.querySelector('[name=nome_titular]');
                        const cpfTitular = document.querySelector('[name=cpf_titular]');
                        const emailTitular = document.querySelector('[name=email_titular]');
                        const generoTitular = document.querySelector('[name=genero_titular]');
                        const telefoneTitular = document.querySelector('[name=telefone_titular]');
                        const dataAniversario = document.querySelector('[name=data_aniversario]');

                        nomeTitular.value = dadosPaciente?.paciente.nome;
                        cpfTitular.value = dadosPaciente?.paciente.documentos.cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
                        telefoneTitular.value = dadosPaciente?.paciente.telefones[0].replace(/(\d{2})(\d{4})(\d{4})/, "($1) $2-$3");
                        generoTitular.value = dadosPaciente.paciente.sexo[0];
                        emailTitular.value = dadosPaciente?.paciente.email[0];
                        dataAniversario.value = dadosPaciente?.paciente.nascimento.replace(/(\d+)-(\d+)-(\d+)/, "$3-$2-$1");

                        dataValues.nome_titular = nomeTitular.value;
                        dataValues.cpf_titular = cpfTitular.value;
                        dataValues.genero_titular = generoTitular.value;
                        dataValues.telefone_titular = telefoneTitular.value;
                        dataValues.data_aniversario = dataAniversario.value;

                        nomeTitular.setAttribute('disabled', 'disabled');
                        cpfTitular.setAttribute('disabled', 'disabled');
                        generoTitular.setAttribute('disabled', 'disabled');
                        telefoneTitular.setAttribute('disabled', 'disabled');
                        dataAniversario.setAttribute('disabled', 'disabled');

                        nomeTitular.value = nomeTitular.value.substr(0, 3) + "****";
                        cpfTitular.value = cpfTitular.value.substr(0, 3) + "****";
                        telefoneTitular.value = telefoneTitular.value.substr(0, 4) + "****";

                        let partesData = dataAniversario.value.split("-");
                        dataAniversario.setAttribute("type", "text");
                        dataAniversario.value = partesData[2] + "****";
                        generoTitular.value = dadosPaciente.paciente.sexo[0];

                    }
                })
                .catch(err => console.error(err));
        }

        /* function getpacienteByCpf() {
            document.querySelector('[name=nome_titular]').addEventListener("focus", (paciente) => {
                const url = `<?php echo home_url() ?>`;
                let paciente_cpf = document.getElementById('cpfTitular').value.replace(/[^\d]/g, "");

                const options = {
                    headers: {
                        'Content-Type': 'application/json'
                    }
                };

                fetch(`${url}/wp-json/api/v1/paciente/?paciente_cpf=${paciente_cpf}`, options)
                    .then(response => response.json())
                    .then(data => {
                        dadosPaciente = data;
                        console.log(dadosPaciente);

                        if (dadosPaciente.status !== 'error') {
                            const nomeTitular = document.querySelector('[name=nome_titular]');
                            const cpfTitular = document.querySelector('[name=cpf_titular]');
                            const emailTitular = document.querySelector('[name=email_titular]');
                            const generoTitular = document.querySelector('[name=genero_titular]');
                            const telefoneTitular = document.querySelector('[name=telefone_titular]');
                            const dataAniversario = document.querySelector('[name=data_aniversario]');

                            console.log(dataAniversario);

                            nomeTitular.value = dadosPaciente?.paciente.nome;
                            cpfTitular.value = dadosPaciente?.paciente.documentos.cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
                            telefoneTitular.value = dadosPaciente?.paciente.telefones[0].replace(/(\d{2})(\d{4})(\d{4})/, "($1) $2-$3");
                            generoTitular.value = dadosPaciente?.paciente.sexo[0];
                            emailTitular.value = dadosPaciente?.paciente.email[0];
                            dataAniversario.value = dadosPaciente?.paciente.nascimento.replace(/(\d+)-(\d+)-(\d+)/, "$3-$2-$1");

                        }
                    })
                    .catch(err => console.error(err));
            });
        } */
        //getpacienteByCpf();

        function capturarDados() {
    let cpfTitular = '';
    let nomeTitular = '';
    let generoTitular = '';
    let telefoneTitular = '';
    let dataAniversario = '';

    if (<?php echo $user_id_feegow; ?> !== -1) {
        cpfTitular = dataValues.cpf_titular.replace(/\D/g, "");
        nomeTitular = dataValues.nome_titular;
        generoTitular = dataValues.genero_titular;
        telefoneTitular = dataValues.telefone_titular.replace(/\D/g, "");
        dataAniversario = dataValues.data_aniversario;
    } else {
        cpfTitular = document.querySelector('[name=cpf_titular]').value.replace(/[^\d]/g, "");
        nomeTitular = document.querySelector('[name=nome_titular]').value;
        generoTitular = document.querySelector('[name=genero_titular]').value;
        telefoneTitular = document.querySelector('[name=telefone_titular]').value;
        dataAniversario = document.querySelector('[name=data_aniversario]').value;
    }

    const emailTitular = document.querySelector('[name=email_titular]').value;
    const horarioEscolhido = document.querySelector('[name=horario_escolhido]').value;

    const dados = {
        nome_titular: nomeTitular,
        cpf_titular: cpfTitular,
        email_titular: emailTitular,
        genero_titular: generoTitular,
        telefone_titular: telefoneTitular,
        horario_escolhido: horarioEscolhido,
        dataAniversario: dataAniversario
    };

    let camposFaltantes = [];
    for (let campo in dados) {
        if (!dados[campo]) {
            camposFaltantes.push(campo);
        }
    }

    if (camposFaltantes.length) {
        console.log("Campos faltantes: ", camposFaltantes);
        return false;
    }

    return dados;
}


        function confirmacaoConsulta() {
            const procedimento_id = filtro.filtro__procedimento_id;

            const infoProcedimento = lt_procedimentos.filter((procedimento) => procedimento.procedimento_id == procedimento_id);

            const nomeProcedimento = lt_procedimentos.filter((procedimento) => procedimento.procedimento_id == procedimento_id)[0].nome;
            const data = `${document.querySelector(`.info-data`).textContent} às ${document.querySelector('[name=horario_escolhido]').value}`;
            const nomeMedico = document.querySelector('#profissional_escolhido').textContent;
            const valorProcedimento = String(infoProcedimento[0].valor).replace(/([0-9]{2})$/g, ",$1");

            let nomePaciente= '';

            if (<?php echo $user_id_feegow; ?> !== -1) {
                nomePaciente = dataValues.nome_titular;
            }else{
                nomePaciente = document.querySelector('[name=nome_titular]').value;
            }

            const dadosConfirmacaoAgendamento = {
                "procedimento_id": procedimento_id,
                "nomePaciente": nomePaciente,
                "nomeMedico": nomeMedico,
                "nomeProcedimento": nomeProcedimento,
                "valorProcedimento": valorProcedimento,
                "infoProcedimento": infoProcedimento,
                "dataAgendada": data
            }

            localStorage.setItem('@@bomdoutor:dados_confirmacao_agendamento', JSON.stringify(dadosConfirmacaoAgendamento));

            document.querySelector(`#dadosAgendamento`).innerHTML = `
            <div class="row"><b class="col-sm-3">Paciente: </b><span class="col-sm-9">${nomePaciente}</span></div>
            <div class="row"><b class="col-sm-3">Médico: </b><span class="col-sm-9">${nomeMedico}</span></div>
            <div class="row"><b class="col-sm-3">Especialidade: </b><span class="col-sm-9">${nomeProcedimento}</span></div>
            <div class="row"><b class="col-sm-3">Valor: </b><span class="col-sm-9">R$ ${valorProcedimento}</span></div>
            <div class="row"><b class="col-sm-3">Local: </b><span class="col-sm-9">Av. Afonso Pena, nº 955, Loja 03 - Centro, Belo Horizonte, MG.</span></div>
            <div class="row"><b class="col-sm-3">Data: </b><span class="col-sm-9">${data}</span></div>
            `;
        }

        function carrega_profissionais() {


            const filtro = JSON.parse(localStorage.getItem(`@@bomdoutor:dados_filtro`));

            console.log(filtro);

            //console.log(`todos dados`)
            const procedimento_id = filtro.filtro__procedimento_id;
            const infoProcedimento = lt_procedimentos.filter((procedimento) => procedimento.procedimento_id == procedimento_id)[0];
            //console.log(infoProcedimento)

            const params = {
                "unidade": filtro.filtro__unidade_id,
                "especialidadesArray": infoProcedimento.especialidade_id,
                "data": filtro.filtro__data,
                "procedimento_id": procedimento_id,
            }

            fetch(`<?php echo home_url() . '/wp-json/api/v1/lista-profissionais?=' ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(params)
                })
                .then(response => response.json())
                .then(response => {
                    const {
                        profissionais
                    } = response

                    //console.log(response);


                    profissionaisUnicos = profissionais.reduce((acc, current) => {
                        acc[JSON.stringify(current)] = current;
                        return acc;
                    }, []);

                    return Object.values(profissionaisUnicos);
                })
                .then(profissionais => {
                        //console.log(`profissionais`, profissionais)

                        const novoJson = [...new Set([].concat(...profissionais.map(item => item.data)))];
                        novoJson.sort((a, b) => new Date(a) - new Date(b));

                        const result = {};
                        novoJson.forEach(data => {
                            result[data] = [];
                            profissionais.forEach(profissional => {
                                if (profissional.data.includes(data)) {
                                    result[data].push(profissional);
                                }
                            });
                        });

                        //console.log(result);

                        listaProfissionais.innerHTML = Object.keys(result).map(data => {

                            const [ano, mes, dia] = data.split('-').map(Number);
                            const responseData = new Date(ano, mes - 1, dia);
                            const opcoes = {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            };
                            const dataFormatada = responseData.toLocaleDateString('pt-BR', opcoes);

                            return `<div class="card-data">
                            <h3 class="info-data">${dataFormatada}</h3>
                            ${result[data].map(profissional => {
                                const {
                                    horarios_disponiveis
                                } = profissional;
                                const dias_disponiveis = Object.values(horarios_disponiveis[data]);
                                const horarios = Object.values(dias_disponiveis);
                                return `<div class="card-profissional" style="display:flex;">
                                <div class="card-imagem">
                                <img src="${profissional.foto === null ? `${urlPlugin}assets/image/avatar-${profissional.sexo.toLowerCase()}.png` : profissional.foto}" alt="" class="foto-especialista" width="100">
                                </div>
                                <div class="card-informacoes">
                                <h3 class="nome-especialista">${profissional.tratamento === null ? `${profissional.nome}` : `${profissional.tratamento} ${profissional.nome}`} </h3>
                                <span class="crm-especialista">${profissional.documento_conselho === `` ? `` : `${profissional.conselho} ${profissional.documento_conselho}`}</span>
                                <div class="div-quadro-horarios">
                                <h4 class="select">Selecione um horário</h4>
                                <div class="quadro-horarios" data-id-profissional="${profissional.profissional_id}" data-nome-profissional="${profissional.nome}">
                                ${horarios.map(horario => {
                                    return `<button type="button" data-position="${profissional.profissional_id}_${horario.substr(0,5)}_${data}" class="btn-horario">${horario.substr(0,5)}</button>` }
                                    )}
                                </div>
                                <hr>
                                </div>
                                </div>
                                </div>`
                            }).join('')}
                            </div>`
                        }).join('').replaceAll(`,`, ``);



        const select = document.querySelector('#filtro__especialidades');
        document.querySelector(`#tituloEspecialidade`).innerText = infoProcedimento.nome;

        const botoes = document.querySelectorAll('.btn-horario');
        botoes.forEach(botao => {
        botao.addEventListener('click', function() {

            let logged = document.querySelectorAll('.logged');

            if (logged.length > 0) {

                const modalAgendamento = document.querySelector('#modalAgendamento');
                const modal = new bootstrap.Modal(modalAgendamento);

                document.querySelector('.info-data').textContent = this.closest('.card-profissional').parentNode.querySelector('.info-data').textContent;

                const cardInfo = document.querySelector('.btn-horario').parentNode.parentNode.parentNode;
                cardInfo.querySelector(`.nome-especialista`).textContent;
                document.querySelector('#horario_escolhido').value = this.innerHTML;
                document.querySelector('#profissional_escolhido').textContent = this.parentElement.getAttribute("data-nome-profissional");

                var professionalId = this.parentElement.getAttribute("data-id-profissional");
                document.querySelector('#profissional_escolhido').value = professionalId;
                localStorage.setItem('@@processo_horario_atendimento', this.getAttribute('data-position'));

                confirmacaoConsulta();
                modal.show();

            } else {
                elementorProFrontend.modules.popup.showPopup({
                    id: 1376
                });
                localStorage.setItem('@@processo_horario_atendimento', this.getAttribute('data-position'));
            }

        });
        });

        })
        .catch(err => {
            console.error(err);
            document.querySelector(`#loader`).removeAttribute(`class`, `active`);
            document.querySelector(`#loader`).style.display = "none";
            document.querySelector(`#tituloEspecialidade`).innerText = "Nenhum horário disponível";
        }).finally(() => {


            const cards = document.querySelectorAll(".card-data");

            if (cards.length > 0) {
                const itemsPerPage = 3;
                const numPages = Math.ceil(cards.length / itemsPerPage);
                for (let i = 0; i < numPages; i++) {
                    const btn = document.createElement("button");
                    btn.innerHTML = i + 1;
                    btn.onclick = function() {
                        const currentPage = parseInt(this.innerHTML);
                        showPage(currentPage, itemsPerPage, cards);
                    };
                    document.querySelector("#pagination").appendChild(btn);

                    function showPage(pageNumber, itemsPerPage, cards) {
                        for (let i = 0; i < cards.length; i++) {
                            if (i >= (pageNumber - 1) * itemsPerPage && i < pageNumber * itemsPerPage) {
                                cards[i].style.display = "block";
                            } else {
                                cards[i].style.display = "none";
                            }
                        }
                    }
                }
                const botoes = document.querySelectorAll('#pagination button');

                botoes.forEach((botao) => {
                    botao.addEventListener('click', () => {
                        document.querySelector('.info-data').scrollIntoView({
                            behavior: 'smooth'
                        });
                        botoes.forEach((b) => b.classList.remove('active'));
                        botao.classList.add('active');
                    });
                });

                let logged_processo = document.querySelectorAll('.logged');
                if (logged_processo.length > 0) {
                    let proceso_horario = localStorage.getItem('@@processo_horario_atendimento');
                    if (proceso_horario != '') {
                        if (document.querySelectorAll(`button[data-position="${proceso_horario}"]`).length > 0) {
                            document.querySelector(`button[data-position="${proceso_horario}"]`).click();
                        }
                    }
                }

                showPage(1, itemsPerPage, cards);
                document.querySelector(`#loader`).removeAttribute(`class`, `active`);
                document.querySelector(`#loader`).style.display = "none";
            }

        });
        }

        function cadastrarAgendamento() {

            const consultaData = localStorage.getItem('@@processo_horario_atendimento');
            const posicaoSeparador = consultaData.indexOf("_", consultaData.indexOf("_") + 1);
            const dataCompleta = consultaData.substring(posicaoSeparador + 1);
            const data_consulta = dataCompleta.substring(0, 10);


            const unidade_id = filtro.filtro__unidade_id;
            const paciente_id = document.querySelector(`#id_user_feegow`).value;
            const profissional_id = document.querySelector('#profissional_escolhido').value;
            const procedimento_id = filtro.filtro__procedimento_id;
            const horario_agendado = document.querySelector('#horario_escolhido').value + ":00";
            const {
                valor,
                especialidade_id
            } = lt_procedimentos.filter((procedimento) => procedimento.procedimento_id == procedimento_id)[0];

            const bodyCadastro = {
                "local_id": unidade_id,
                "paciente_id": paciente_id,
                "profissional_id": profissional_id,
                "procedimento_id": procedimento_id,
                "especialidade_id": especialidade_id[0],
                "data": data_consulta,
                "horario": horario_agendado,
                "valor": valor,
                "plano": 0
            }

            //console.log(bodyCadastro);

            const base_url = '<?php echo home_url(); ?>';
            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(bodyCadastro)
            };

            fetch(`${base_url}/wp-json/api/v1/registrar-agendamento`, options)
                .then(response => response.json())
                .then(response => {
                    if (response.status === 'sucesso') {
                        document.querySelector(`.step-2`).style.display = 'none';
                        document.querySelector(`.step-3`).style.display = 'block';

                        window.location.href = `${window.location.origin}/confirmacao-de-agendamento/`
                    } else {
                        document.querySelector(`#mgsModal`).textContent = `Não foi possível realizar o agendamento. Favor entrar em contato com nossa equipe pelo whatsapp.`;
                    }
                })
                .catch(err => console.error(err));
        }

        function cadastraPaciente() {

            let cpfTitular = '';
            let generoTitular = '';
            let telefoneTitular = '';
            let dataAniversario = '';

            if (<?php echo $user_id_feegow; ?> !== -1) {
                cpfTitular = dataValues.cpf_titular.replace(/\D/g, "");
                nomeTitular = dataValues.nome_titular;
                generoTitular = dataValues.genero_titular;
                telefoneTitular = dataValues.telefone_titular.replace(/\D/g, "");
                dataAniversario = dataValues.data_aniversario;
            }else{
                cpfTitular = document.querySelector('[name=cpf_titular]').value.replace(/[^\d]/g, "");
                nomeTitular = document.querySelector('[name=nome_titular]').value;
                generoTitular = document.querySelector('[name=genero_titular]').value;
                telefoneTitular = document.querySelector('[name=telefone_titular]').value;
                dataAniversario = document.querySelector('[name=data_aniversario]').value;
            }

            const emailTitular = document.querySelector('[name=email_titular]').value;


            let user_id = <?php echo $user_id_feegow ?>;

            const bodyCadastro = {
                "nome_titular": nomeTitular,
                "cpf_titular": cpfTitular,
                "email_titular": emailTitular,
                "genero_titular": generoTitular,
                "data_nascimento": dataAniversario,
                "telefone_titular": document.querySelector('[name=telefone_titular]').value,
                "user_id": user_id
            }
            //console.log(bodyCadastro)

            const base_url = '<?php echo home_url(); ?>';
            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(bodyCadastro)
            };

            if (capturarDados()) {
                fetch(`${base_url}/wp-json/api/v1/registrar-paciente`, options)
                    .then(response => response.json())
                    .then(response => {
                        if (response.status === 'sucesso') {
                            document.querySelector(`.step-1`).style.display = 'none';
                            document.querySelector(`.step-2`).style.display = 'block';
                            document.querySelector(`#id_user_feegow`).value = response.content.paciente_id
                            document.querySelector(`#stepModal`).innerText = "Forma de Pagamento";
                        }
                        document.querySelector(`.step-1`).style.display = 'none';
                        document.querySelector(`.step-2`).style.display = 'block';
                        //console.log(response.mensagem);
                    })
                    .catch(err => console.error(err));
            }

        }

        function validar_cpf(cpf, user_id){
            const base_url = '<?php echo home_url(); ?>';
            const bodyCadastro = {
                "cpf": cpf,
                "user_id": user_id
            }
            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(bodyCadastro)
            };
            fetch(`${base_url}/wp-json/api/v1/validar-cpf`, options)
                    .then(response => response.json())
                    .then(response => {
                        if(response.success){

                            const dadosPaciente = response.content;

                            const nomeTitular = document.querySelector('[name=nome_titular]');
                            const cpfTitular = document.querySelector('[name=cpf_titular]');
                            const emailTitular = document.querySelector('[name=email_titular]');
                            const generoTitular = document.querySelector('[name=genero_titular]');
                            const telefoneTitular = document.querySelector('[name=telefone_titular]');
                            const dataAniversario = document.querySelector('[name=data_aniversario]');

                            nomeTitular.value = dadosPaciente.nome;
                            cpfTitular.value = dadosPaciente.documentos.cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
                            telefoneTitular.value = dadosPaciente.telefones[0].replace(/(\d{2})(\d{4})(\d{4})/, "($1) $2-$3");
                            generoTitular.value = dadosPaciente.sexo[0];
                            emailTitular.value = dadosPaciente.email[0];
                            dataAniversario.value = dadosPaciente.nascimento.replace(/(\d+)-(\d+)-(\d+)/, "$3-$2-$1");

                            document.querySelector(`.step-1`).style.display = 'none';
                            document.querySelector(`.step-2`).style.display = 'block';
                            document.querySelector(`#id_user_feegow`).value = dadosPaciente.id
                            document.querySelector(`#stepModal`).innerText = "Forma de Pagamento";

                        }else{
                            //o usuario não foi encontrado então o cadastro segue normalmente
                            cadastraPaciente();
                        }

                    })
                    .catch(err => console.error(err));
        }

        function maskCPF(e) {
            let cpf = e.target.value;
            cpf = cpf.replace(/\D/g, "")
            cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2")
            cpf = cpf.replace(/(\d{3})(\d)/, "$1.$2")
            cpf = cpf.replace(/(\d{3})(\d{1,2})$/, "$1-$2")
            e.target.value = cpf;
        }

        document.querySelector("#cpfTitular").onkeypress = function(e) {
            maskCPF(e)
        };

        document.querySelector("#step1").addEventListener("click", function() {
            let inputs = document.querySelectorAll("input:not([hidden])");
            let valid = true;
            let mgsAlert = document.getElementById('mgsAlert');

            for (let i = 0; i < inputs.length; i++) {
                if (!inputs[i].value) {
                    inputs[i].style.border = "1px solid red";
                    valid = false;
                } else {
                    inputs[i].style.border = "1px solid #ccc";
                }
            }

        });

        window.onload = function() {
            setStorange();

            if (window.location.hash.includes('#redirect')) {

                var novaHash = window.location.hash.replace('#redirect', '');
                history.replaceState(null, null, window.location.pathname + window.location.search + novaHash);
                document.querySelector('#btn-filtro').click();
            }

            carrega_profissionais();

            document.querySelector(`#step1`).onclick = function() {
                if (capturarDados()) {
                    const cpfTitular = document.querySelector('[name=cpf_titular]').value.replace(/[^\d]/g, "");
                    validar_cpf(cpfTitular, <?php echo $usuario_id ?>);
                } else {
                    console.log('preencha todos os campos');
                }
            }

            document.querySelector(`#step2`).onclick = function() {
                const msg = document.querySelector(`#msgPagamento`)
                if (document.querySelector(`#pagamentoLocal`).checked === false) {
                    msg.style.color = "red"
                    msg.textContent = `Selecione a forma de pagamento`
                } else {
                    document.querySelector(`.step-2`).style.display = 'none';
                    document.querySelector(`.step-3`).style.display = 'block';
                    document.querySelector(`#stepModal`).innerText = "Dados da consulta";
                    confirmacaoConsulta();
                }
            }

            document.querySelector(`#step3`).onclick = function() {
                cadastrarAgendamento();
            }

        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
    </body>
<?php
}
add_shortcode('page_agendamento', 'page_agendamento_shortcode');