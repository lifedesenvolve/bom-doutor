<?php
function page_agendamento_shortcode()
{
    $user_id = 1;
    if (is_user_logged_in()) {
        $usuario = wp_get_current_user();
        $email = $usuario->user_email;
        $user_id = $usuario->ID;
    }
    if (get_field('user_id', 'user_' . get_current_user_id()) == "") {


?>
        <script>
            //window.location.assign("/agendar");
        </script>
    <?php }


    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');

    if (isset($_GET['filtro__data'])) {
        $filtro_data = $_GET['filtro__data'];
        $date = strftime('%A, %d de %B de %Y', strtotime($filtro_data));
    } else {
        $date = strftime('%A, %d de %B de %Y', strtotime('today'));
    }

    ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <style>
        .card-profissional {
            display: flex;
            padding-top: 32px;
            margin-bottom: 28px;
        }

        .nome-especialista {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 500;
            font-size: 20px;
            line-height: 24px;
            color: #2A2860;
        }

        .crm-especialista {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 400;
            font-size: 14px;
            line-height: 17px;
            text-align: center;
            color: #4D4D4D;
        }

        .card-informacoes {
            width: 100%;
            padding-left: 20px;
        }

        .select {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 500;
            font-size: 18px;
            line-height: 22px;
            color: #2A2860;
        }

        .btn-horario {
            display: flex;
            border-radius: 5px;
            border: 1px solid #D6D6D6;
            color: #4D4D4D;
            cursor: pointer;
            justify-content: space-evenly;
            align-items: center;
            font-size: 0.8em;
        }

        .btn-horario::after {
            content: "";
            display: block;
            background-image: url('<?php echo PLUGIN_URL . "/assets/image/icon-seta.png" ?>');
            background-repeat: no-repeat;
            background-position: center right;
            width: 20px;
            height: 20px;
        }

        .div-quadro-horarios {
            padding-top: 37px;
        }

        .quadro-horarios {
            display: grid;
            gap: 8px;
            grid-template-columns: repeat(auto-fit, minmax(70px, auto));
            margin-bottom: 28px
        }

        .card-profissional:first-child {
            padding-top: 0;
        }

        .titulo-especialidade {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 700;
            font-size: 24px;
            line-height: 29px;
            color: #2A2860;
        }

        .lista-profissionais hr {
            border: 0;
            border-bottom: 1px solid #D6D6D6;
        }

        .cta {
            background: #2A2860;
            color: #fff;
        }

        .steps {
            display: flex;
            justify-content: space-around;
            max-width: 400px;
            margin: auto;
        }

        .info-data {
            font-family: 'Inter';
            font-style: normal;
            font-weight: 500;
            font-size: 16px;
            line-height: 19px;

            color: #383838;
            padding-bottom: 32px;
        }

        #dataNascimento {
            width: 100%;
            background: #fff;
            border: 1px solid var(--base-color);
            padding: 13px 15px;
            outline: none;
            line-height: 1;
        }

        #modalAgendamento input {
            border-radius: 0.375rem;
        }

        :root {
            --hue: 223;
            --bg: hsl(var(--hue), 90%, 90%);
            --fg: hsl(var(--hue), 90%, 10%);
            --trans-dur: 0.3s;
            font-size: calc(16px + (20 - 16) * (100vw - 320px) / (1280 - 320));
        }

        .loader {
            color: var(--fg);
            font: 1em/1.5 sans-serif;
            height: 100vh;
            place-items: center;
            transition: background-color var(--trans-dur), color var(--trans-dur);
            margin-top: -15%;
            display: none;
        }

        .loader.active {
            display: grid;
        }

        .smiley {
            width: 8em;
            height: 8em;
        }

        .smiley__eye1,
        .smiley__eye2,
        .smiley__mouth1,
        .smiley__mouth2 {
            animation: eye1 3s ease-in-out infinite;
        }

        .smiley__eye1,
        .smiley__eye2 {
            transform-origin: 64px 64px;
        }

        .smiley__eye2 {
            animation-name: eye2;
        }

        .smiley__mouth1 {
            animation-name: mouth1;
        }

        .smiley__mouth2 {
            animation-name: mouth2;
            visibility: hidden;
        }

        /* Dark theme */
        @media (prefers-color-scheme: dark) {
            :root {
                --bg: hsl(var(--hue), 90%, 10%);
                --fg: hsl(var(--hue), 90%, 90%);
            }
        }

        /* Animations */
        @keyframes eye1 {
            from {
                transform: rotate(-260deg) translate(0, -56px);
            }

            50%,
            60% {
                animation-timing-function: cubic-bezier(0.17, 0, 0.58, 1);
                transform: rotate(-40deg) translate(0, -56px) scale(1);
            }

            to {
                transform: rotate(225deg) translate(0, -56px) scale(0.35);
            }
        }

        @keyframes eye2 {
            from {
                transform: rotate(-260deg) translate(0, -56px);
            }

            50% {
                transform: rotate(40deg) translate(0, -56px) rotate(-40deg) scale(1);
            }

            52.5% {
                transform: rotate(40deg) translate(0, -56px) rotate(-40deg) scale(1, 0);
            }

            55%,
            70% {
                animation-timing-function: cubic-bezier(0, 0, 0.28, 1);
                transform: rotate(40deg) translate(0, -56px) rotate(-40deg) scale(1);
            }

            to {
                transform: rotate(150deg) translate(0, -56px) scale(0.4);
            }
        }

        @keyframes eyeBlink {

            from,
            25%,
            75%,
            to {
                transform: scaleY(1);
            }

            50% {
                transform: scaleY(0);
            }
        }

        @keyframes mouth1 {
            from {
                animation-timing-function: ease-in;
                stroke-dasharray: 0 351.86;
                stroke-dashoffset: 0;
            }

            25% {
                animation-timing-function: ease-out;
                stroke-dasharray: 175.93 351.86;
                stroke-dashoffset: 0;
            }

            50% {
                animation-timing-function: steps(1, start);
                stroke-dasharray: 175.93 351.86;
                stroke-dashoffset: -175.93;
                visibility: visible;
            }

            75%,
            to {
                visibility: hidden;
            }
        }

        @keyframes mouth2 {
            from {
                animation-timing-function: steps(1, end);
                visibility: hidden;
            }

            50% {
                animation-timing-function: ease-in-out;
                visibility: visible;
                stroke-dashoffset: 0;
            }

            to {
                stroke-dashoffset: -351.86;
            }
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
                        <input hidden class="form-control" id="valor_procedimento" name="valor_procedimento" type="text" value="8900">

                        <div class="mb-3 px-5 row">
                            <label for="cpfTitular" class="col-sm-3 col-form-label">CPF</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control-plaintext" 
                                id="cpfTitular" name="cpf_titular" 
                                maxlength="14"
                                placeholder="123.456.789-00">
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
        const urlPlugin = "<?php echo PLUGIN_URL; ?>"
        const listaProfissionais = document.querySelector(`#listaProfissionais`);
        const tituloEspecialidade = document.querySelector(`#tituloEspecialidade`);
        const url = `<?php echo home_url() ?>`;
        let dadosPaciente;
            fetch(`${url}/wp-json/api/v1/paciente/?paciente_id=<?php echo $user_id ?>`)
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


        function capturarDados() {
            const nomeTitular = document.querySelector('[name=nome_titular]').value;
            const cpfTitular = document.querySelector('[name=cpf_titular]').value.replace(/\D/g,"");
            const emailTitular = document.querySelector('[name=email_titular]').value;
            const generoTitular = document.querySelector('[name=genero_titular]').value;
            const telefoneTitular = document.querySelector('[name=telefone_titular]').value.replace(/\D/g,"");
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

        function confirmacaoConsulta() {
            const nomeTitular = document.querySelector('[name=nome_titular]').value;
            const especialidade = document.querySelector(`#tituloEspecialidade`).textContent;
            const data = `${document.querySelector(`.info-data`).textContent} às ${document.querySelector('[name=horario_escolhido]').value}`;
            const nomeMedico = document.querySelector('#profissional_escolhido').textContent
            const valorProcedimento = document.querySelector("#valor_procedimento").value.replace(/([0-9]{2})$/g, ",$1")

            document.querySelector(`#dadosAgendamento`).innerHTML = `
            <div class="row"><b class="col-sm-3">Paciente: </b><span class="col-sm-9">${nomeTitular}</span></div>
            <div class="row"><b class="col-sm-3">Médico: </b><span class="col-sm-9">${nomeMedico}</span></div>
            <div class="row"><b class="col-sm-3">Especialidade: </b><span class="col-sm-9">${especialidade}</span></div>
            <div class="row"><b class="col-sm-3">Valor: </b><span class="col-sm-9">R$ ${valorProcedimento}</span></div>
            <div class="row"><b class="col-sm-3">Local: </b><span class="col-sm-9">Av. Afonso Pena, nº 955, Loja 03 - Centro, Belo Horizonte, MG.</span></div>
            <div class="row"><b class="col-sm-3">Data: </b><span class="col-sm-9">${data}</span></div>
            `;
        }

        const searchURL = new URLSearchParams(window.location.search);
        const params = {
            unidade: searchURL.get('filtro__unidade'),
            especialidade: searchURL.get('filtro__especialidades'),
            data: searchURL.get('filtro__data'),
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
                      ${horarios.map(horario => { return `<button type="button" class="btn-horario" data-bs-toggle="modal" data-bs-target="#modalAgendamento">${horario.substr(0,5)}</button>` })
            }
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
            }).finally(()=>{
                document.querySelector(`#loader`).removeAttribute(`class`, `active`)
                document.querySelector(`#loader`).style.display = "none"
            });

        
            function cadastrarAgendamento() {

            const searchURL = new URLSearchParams(window.location.search);

            const filtro__unidade = searchURL.get('filtro__unidade');
            const paciente_id = "<?php echo $user_id ?>";
            const profissional_id = document.querySelector('#profissional_escolhido').value;
            const procedimento_id = "4";
            const filtro__especialidades = searchURL.get('filtro__especialidades');
            const filtro__data = searchURL.get('filtro__data');
            const bodyCadastro = 
                {
                    "local_id": filtro__unidade,
                    "paciente_id": paciente_id,
                    "profissional_id": profissional_id,
                    "procedimento_id": procedimento_id,
                    "especialidade_id": filtro__especialidades,
                    "data": filtro__data,
                    "horario": document.querySelector('#horario_escolhido').value + ":00",
                    "valor": document.querySelector("#valor_procedimento").value ,
                    "plano": 0
                }


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
            const cpfTitular = document.querySelector('[name=cpf_titular]').value;
            const emailTitular = document.querySelector('[name=email_titular]').value;
            const generoTitular = document.querySelector('[name=genero_titular]').value;
            const telefoneTitular = document.querySelector('[name=telefone_titular]').value;

            const base_url = '<?php echo home_url(); ?>';
            const options = {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    "nome_titular": nomeTitular,
                    "cpf_titular": cpfTitular,
                    "email_titular": emailTitular,
                    "genero_titular": generoTitular,
                    "telefone_titular": telefoneTitular,
                    "user_id": "<?php echo $user_id ?>"
                })
            };

            fetch(`${base_url}/wp-json/api/v1/registrar-paciente`, options)
                .then(response => response.json())
                .then(response => {
                    if (response.status === 'sucesso') {
                        document.querySelector(`.step-1`).style.display = 'none';
                        document.querySelector(`.step-2`).style.display = 'block';
                        console.log(response.mensagem);
                    }
                    document.querySelector(`.step-1`).style.display = 'none';
                    document.querySelector(`.step-2`).style.display = 'block';
                    console.log(response.mensagem);
                    document.querySelector(`#stepModal`).innerText = "Forma de Pagamento";
                })
                .catch(err => console.error(err));
        }

        function maskCPF(e){
            let cpf = e.target.value;
            cpf=cpf.replace(/\D/g,"")
            cpf=cpf.replace(/(\d{3})(\d)/,"$1.$2")
            cpf=cpf.replace(/(\d{3})(\d)/,"$1.$2")
            cpf=cpf.replace(/(\d{3})(\d{1,2})$/,"$1-$2")
            e.target.value = cpf;
        }

        document.querySelector("#cpfTitular").onkeypress = function(e){
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
                if(document.querySelector(`#pagamentoLocal`).checked === false){
                    msg.style.color = "red"
                    msg.textContent = `Selecione a forma de pagamento`
                }else{
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
