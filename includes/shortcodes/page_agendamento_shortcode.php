<?php
function page_agendamento_shortcode()
{
    wp_enqueue_style('page-agendamento-css');

    if (is_user_logged_in()) {
        $usuario = wp_get_current_user();
        $email = $usuario->user_email;
        $user_id_feegow = get_field('user_id_feegow', 'user_' . $usuario->ID);
    }

    if (empty($user_id_feegow)) {
        $user_id_feegow = -1;
    }
?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

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
    <h3 class="info-data"><?php echo $date; ?></h3>

    <div class="lista-profissionais" id="listaProfissionais"></div>


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
                            <button type="button" class="btn btn-default cta" id="step3">Enviar</button>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row"><span class="col-sm-3"></span><span class="col-sm-9"></span></div>
    <script>
        const filtro__data = localStorage.getItem('@@bomdoutor:filtro__data');
        const filtro__especialidades = localStorage.getItem('@@bomdoutor:filtro__especialidades');
        const filtro__unidade = localStorage.getItem('@@bomdoutor:filtro__unidade');
        const filtro__procedimento = localStorage.getItem('@@bomdoutor:filtro__procedimento');
        document.querySelector(".info-data").innerHTML = new Date(filtro__data.replaceAll(`-`, ` `)).toLocaleDateString('pt-BR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        const idUserFeegow = <?php echo $user_id_feegow; ?>;
        if (idUserFeegow !== -1) {
            document.querySelector(`#id_user_feegow`).value = idUserFeegow
        }

        const urlPlugin = "<?php echo PLUGIN_URL; ?>"
        const listaProfissionais = document.querySelector(`#listaProfissionais`);
        const tituloEspecialidade = document.querySelector(`#tituloEspecialidade`);

        const url = `<?php echo home_url() ?>`;
        let dadosPaciente;
        console.log(idUserFeegow);
        if (idUserFeegow !== -1) {
            fetch(`${url}/wp-json/api/v1/paciente/?paciente_id=${idUserFeegow}`)
                .then(response => response.json())
                .then(data => {
                    dadosPaciente = data;
                    const nomeTitular = document.querySelector('[name=nome_titular]');
                    const cpfTitular = document.querySelector('[name=cpf_titular]');
                    const emailTitular = document.querySelector('[name=email_titular]');
                    const generoTitular = document.querySelector('[name=genero_titular]');
                    const telefoneTitular = document.querySelector('[name=telefone_titular]');

                    nomeTitular.value = dadosPaciente?.paciente.nome;
                    cpfTitular.value = dadosPaciente?.paciente.documentos.cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4");
                    telefoneTitular.value = dadosPaciente?.paciente.telefones[0].replace(/(\d{2})(\d{4})(\d{4})/, "($1) $2-$3");
                    generoTitular.value = dadosPaciente?.paciente.sexo[0];
                    emailTitular.value = dadosPaciente?.paciente.email[0];
                })
                .catch(error => console.log(error));
        }




        function capturarDados() {
            const nomeTitular = document.querySelector('[name=nome_titular]').value;
            const cpfTitular = document.querySelector('[name=cpf_titular]').value.replace(/\D/g, "");
            const emailTitular = document.querySelector('[name=email_titular]').value;
            const generoTitular = document.querySelector('[name=genero_titular]').value;
            const telefoneTitular = document.querySelector('[name=telefone_titular]').value.replace(/\D/g, "");
            const horarioEscolhido = document.querySelector('[name=horario_escolhido]').value;

            const dados = {
                nome_titular: nomeTitular,
                cpf_titular: cpfTitular,
                email_titular: emailTitular,
                genero_titular: generoTitular,
                telefone_titular: telefoneTitular,
                horario_escolhido: horarioEscolhido
            };

            if (!nomeTitular || !cpfTitular || !emailTitular || !telefoneTitular || !horarioEscolhido) {
                return false;
            }

            return dados;
        }

        function procedimento_valor(especialidade_id, tipo_procedimento) {
            const base_url = '<?php echo home_url(); ?>';
            const options = {
                method: 'GET'
            };
            fetch(`${base_url}/wp-json/api/v1/procedimento-valor/?especialidade_id=${especialidade_id}&tipo_procedimento=${tipo_procedimento}`, options)
                .then(response => response.json())
                .then(response => {
                    document.querySelector("#valor_procedimento").innerText = response.api_response.valor;
                    document.querySelector("#valor_procedimento").value = response.api_response.procedimento_id;
                    console.log(response)
                })
                .catch(err => console.error(err));
        }

        function confirmacaoConsulta() {
            const tipo_procedimento = filtro__procedimento;
            const id_especialidades = filtro__especialidades;

            procedimento_valor(id_especialidades, tipo_procedimento);

            const nomeTitular = document.querySelector('[name=nome_titular]').value;
            const especialidade = document.querySelector(`#tituloEspecialidade`).textContent;
            const data = `${document.querySelector(`.info-data`).textContent} às ${document.querySelector('[name=horario_escolhido]').value}`;
            const nomeMedico = document.querySelector('#profissional_escolhido').textContent
            const valorProcedimento = document.querySelector("#valor_procedimento").textContent.replace(/([0-9]{2})$/g, ",$1")

            document.querySelector(`#dadosAgendamento`).innerHTML = `
            <div class="row"><b class="col-sm-3">Paciente: </b><span class="col-sm-9">${nomeTitular}</span></div>
            <div class="row"><b class="col-sm-3">Médico: </b><span class="col-sm-9">${nomeMedico}</span></div>
            <div class="row"><b class="col-sm-3">Especialidade: </b><span class="col-sm-9">${especialidade}</span></div>
            <div class="row"><b class="col-sm-3">Valor: </b><span class="col-sm-9">R$ ${valorProcedimento}</span></div>
            <div class="row"><b class="col-sm-3">Local: </b><span class="col-sm-9">Av. Afonso Pena, nº 955, Loja 03 - Centro, Belo Horizonte, MG.</span></div>
            <div class="row"><b class="col-sm-3">Data: </b><span class="col-sm-9">${data}</span></div>
            `;
        }

        const params = {
            unidade: filtro__unidade,
            especialidade: filtro__especialidades,
            data: filtro__data,
        };

        const queryString = new URLSearchParams(params).toString();
        const options = {
            method: 'GET'
        };

        fetch(`<?php echo home_url('/wp-json/api/v1/lista-profissionais/') ?>?${queryString}`, options)
            .then(response => response.json())
            .then(response => {
                console.log(response)
                const {
                    profissionais
                } = response;

                console.log(`response:`, response)

                listaProfissionais.innerHTML = profissionais.map(profissional => {

                    const {
                        horarios_disponiveis
                    } = profissional;

                    const dias_disponiveis = Object.values(horarios_disponiveis);
                    const horarios = Object.values(dias_disponiveis[0]);
                    return `<div class="card-profissional" style="display:flex;">
                    <div class="card-imagem"><img src="${urlPlugin}assets/image/avatar-${profissional.sexo.toLowerCase()}.png" alt="" class="foto-especialista" width="100"></div>
                        <div class="card-informacoes">
                            <h3 class="nome-especialista">${profissional.tratamento === null ? `${profissional.nome}` : `${profissional.tratamento} ${profissional.nome}`} </h3>
                            <span class="crm-especialista">${profissional.documento_conselho === `` ? `` : `CRM ${profissional.documento_conselho}`}</span>
                            <div class="div-quadro-horarios">
                            <h4 class="select">Selecione um horário</h4>
                            <div class="quadro-horarios" data-id-profissional="${profissional.profissional_id}" >
                            ${horarios.map(horario => { return `<button type="button" class="btn-horario" data-bs-toggle="modal" data-bs-target="#modalAgendamento">${horario.substr(0,5)}</button>` })}
                            </div>
                            <hr>
                            </div>
                        </div>
                    </div>
                    `
                }).join().replaceAll(`,`, ``);

                const select = document.querySelector('#filtro__especialidades');
                document.querySelector(`#tituloEspecialidade`).innerText = select.options[select.selectedIndex].text;

                const botoes = document.querySelectorAll('.btn-horario');
                botoes.forEach(botao => {
                    botao.addEventListener('click', function() {
                        const cardInfo = document.querySelector('.btn-horario').parentNode.parentNode.parentNode;
                        cardInfo.querySelector(`.nome-especialista`).textContent;

                        document.querySelector('#horario_escolhido').value = this.innerHTML;
                        document.querySelector('#profissional_escolhido').textContent = cardInfo.querySelector(`.nome-especialista`).textContent;

                        var professionalId = this.parentElement.getAttribute("data-id-profissional");
                        document.querySelector('#profissional_escolhido').value = professionalId;

                        confirmacaoConsulta();
                    });
                });

            })
            .catch(err => {
                console.error(err)
                document.querySelector(`#tituloEspecialidade`).innerText = "Nenhum horário disponível";
            }).finally(() => {
                document.querySelector(`#loader`).removeAttribute(`class`, `active`)
                document.querySelector(`#loader`).style.display = "none"
            });


        function cadastrarAgendamento() {

            const unidade_id = filtro__unidade;
            const paciente_id = document.querySelector(`#id_user_feegow`).value;
            const profissional_id = document.querySelector('#profissional_escolhido').value;
            const especialidade_id = filtro__especialidades;
            const data_consulta = filtro__data;
            const procedimento_id = document.querySelector("#valor_procedimento").value;
            const horario_agendado = document.querySelector('#horario_escolhido').value + ":00";
            const valor_consulta = document.querySelector("#valor_procedimento").innerText;

            const bodyCadastro = {
                "local_id": unidade_id,
                "paciente_id": paciente_id,
                "profissional_id": profissional_id,
                "procedimento_id": procedimento_id,
                "especialidade_id": especialidade_id,
                "data": data_consulta,
                "horario": horario_agendado,
                "valor": valor_consulta,
                "plano": 0
            }

            console.log(bodyCadastro);

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

                        document.querySelector(`#mgsModal`).style.color = "green";
                        document.querySelector(`#mgsModal`).textContent = response.mensagem;
                    } else {
                        console.log(response.mensagem);
                    }
                })
                .catch(err => console.error(err));
        }

        function cadastraPaciente() {
            const nomeTitular = document.querySelector('[name=nome_titular]').value;
            const cpfTitular = document.querySelector('[name=cpf_titular]').value.replace(/[^\d]/g, "");
            const emailTitular = document.querySelector('[name=email_titular]').value;
            const generoTitular = document.querySelector('[name=genero_titular]').value;
            const telefoneTitular = document.querySelector('[name=telefone_titular]').value.replace(/[^\d]/g, "");
            let user_id = <?php echo get_current_user_id() ?>;

            const bodyCadastro = {
                "nome_titular": nomeTitular,
                "cpf_titular": cpfTitular,
                "email_titular": emailTitular,
                "genero_titular": generoTitular,
                "telefone_titular": telefoneTitular,
                "user_id": user_id
            }
            console.log(bodyCadastro)

            const base_url = '<?php echo home_url(); ?>';
            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(bodyCadastro)
            };

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
                    console.log(response.mensagem);
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

        window.onload = function() {

            document.querySelector(`#step1`).onclick = function() {
                if (capturarDados()) {

                    cadastraPaciente();

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
                    document.querySelector(`#stepModal`).innerText = "Confirmação de Consulta";
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
