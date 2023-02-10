<?php

function pesquisa_agendamento_shortcode()
{
    wp_enqueue_style('pesquisa-agendamento-css');
    $api = new Api();
    $lista_unidades = $api->listUnidades();
?>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <div id="form-agendamento">
        <div class="group-modalidade" id="tipoProcedimento"></div>
        <div class="group-inputs">
            <select id="unidade" name="filtro__unidade">
                <?php foreach ($lista_unidades as $unidade) { ?>
                    <option value="<?php echo $unidade['id'] ?>"><?php echo $unidade['cidade'] ?></option>
                <?php } ?>
            </select>
            <select id="procedimento" name="filtro__procedimento" class="modalidade-item">
                <option value="">Selecione a especialidade</option>
            </select>
            <input type="hidden" name="filtro__data" value='<?php echo date("Y-m-d"); ?>'>
            <input type="hidden" name="filtro__procedimento" value='2'>
            <button class="elementor-button elementor-size-sm btn-pesquisa" onclick="pesquisaFeeGow()" style="cursor: pointer;">Pesquisar</button>
        </div>
    </div>
    <script>
        localStorage.setItem('@@bomdoutor:dados_lista_procedimentos', "");
        localStorage.setItem('@@bomdoutor:dados_filtro', "");
        localStorage.setItem('@@bomdoutor:dados_confirmacao_agendamento', "");
        $(document).ready(function() {
            $("#procedimento").select2();
        });

        function create_select_mobile() {
            document.getElementById("tipoProcedimento").insertAdjacentHTML("afterend", "<select id='selectForMobile'></select>");

            let btnContainer = document.getElementById("tipoProcedimento");
            let select = document.getElementById('selectForMobile');
            select.id = "selectProcedimento";

            for (let i = 0; i < btnContainer.children.length; i++) {
                let option = document.createElement("option");
                if (btnContainer.children[i].value == 2) {
                    option.value = btnContainer.children[i].value;
                    option.text = btnContainer.children[i].textContent;
                    option.setAttribute('selected', 'selected');
                } else {
                    option.value = btnContainer.children[i].value;
                    option.text = btnContainer.children[i].textContent;
                }
                select.appendChild(option);
            }

            select.addEventListener("change", function() {
                var selectedValue = this.value;
                var button = document.querySelector(`button[value="${selectedValue}"]`);
                button.click();
            });
        }

        function loadProcedimentos(tipoProcedimento, dados_lista_procedimentos) {
            const selectProcedimentos = document.querySelector('#procedimento');
            let options = '';
            let lista = dados_lista_procedimentos.filter(item => item.tipo_procedimento == tipoProcedimento && item.especialidade_id != null && item.permite_agendamento_online ==
                true)

            console.log(lista);

            lista.forEach(info => {
                const valor = String(info.valor).replace(/([0-9]{2})$/g, ",$1");
                options += `<option value="${info.procedimento_id}">${info.nome} - R$ ${valor}</option>`;
            });
            selectProcedimentos.innerHTML = options;
        }

        async function getEspecialidadeByProcedimentoId(procedimento_id) {
            const base_url = '<?php echo home_url(); ?>';

            const options = {
                method: 'GET'
            };

            try {
                const response = await fetch(`${base_url}/wp-json/api/v1/get-especialidades/?procedimento_id=${procedimento_id}`, options);
                const jsonData = await response.json();
                return jsonData;

            } catch (err) {
                console.error(err);
            }
        }

        function procedimentoAtivo(dados_lista_procedimentos) {
            document.querySelectorAll(`#tipoProcedimento button`).forEach((item) => {
                if (item.value == document.querySelector(`[name="filtro__procedimento"]`).value) {
                    item.setAttribute(`class`, `btn-modalidade ativo`)
                }
            })
            document.addEventListener('click', (event) => {
                if (event.target.classList.contains('btn-modalidade')) {
                    const selectedValue = event.target.value;
                    loadProcedimentos(selectedValue, dados_lista_procedimentos)
                }
            });

        }

        function pesquisaFeeGow() {
            const unidade_id = document.getElementById("unidade").value;
            const tipo_procedimento = document.querySelector(`#tipoProcedimento .btn-modalidade.ativo`).value;
            const procedimento_id = document.getElementById("procedimento").value;
            const filtro__data = "<?php echo date('Y-m-d'); ?>"

            const dados_filtro = {
                filtro__data: filtro__data,
                filtro__procedimento_id: procedimento_id,
                filtro__tipo_procedimento: tipo_procedimento,
                filtro__unidade_id: unidade_id
            }

            localStorage.setItem('@@bomdoutor:dados_lista_procedimentos', JSON.stringify(dados_lista_procedimentos));
            localStorage.setItem('@@bomdoutor:dados_filtro', JSON.stringify(dados_filtro));



            if (unidade_id === "") {
                document.getElementById("unidade").style.borderColor = "red";
            } else {
                document.getElementById("unidade").style.borderColor = "#D2D1D6";
            }

            if (unidade_id !== "") {
                <?php if (is_user_logged_in()) { ?>
                    window.location.assign(`<?php echo home_url() ?>/agendar/`);
                <?php  } else {  ?>
                    elementorProFrontend.modules.popup.showPopup({
                        id: 1376
                    });
                <?php  } ?>
            }

        }

        function redirect_whatsapp() {
            const retorno = document.querySelector('btn-modalidade')
        }

        async function lista_tipo_procedimentos() {
            const base_url = '<?php echo home_url(); ?>';
            const options = {
                method: 'GET'
            };

            await fetch(`${base_url}/wp-json/api/v1/lista-procedimentos`, options)
                .then(response => response.json())
                .then(response => {

                    let form_shortcode = document.getElementById('form-agendamento');

                    if (response.length < 0) {
                        form_shortcode.style = 'display: none';
                        return;
                    } else {
                        form_shortcode.style = 'display: block';
                    }

                    let htmlProcedimentos = '';
                    response.forEach((item) => {
                        htmlProcedimentos += `
                            <button class="btn-modalidade" 
                                value="${item.procedimento_id}" 
                            >${item.procedimento_nome}
                            </button>`
                    });

                    document.querySelector('#tipoProcedimento').innerHTML = htmlProcedimentos;
                    create_select_mobile();
                })
                .catch(err => console.error(err));
        }

        const dados_lista_procedimentos = [];
        (async () => {
            await lista_tipo_procedimentos();
            await getEspecialidadeByProcedimentoId(2)
                .then((response) => {
                    dados_lista_procedimentos.push(...response);

                    return response;
                }).then((response) => {

                    //document.querySelector('[data-procedimento="Consulta"]').click()

                    return response;
                }).then((response) => procedimentoAtivo(response))
                .then(() => {
                    document.querySelector(`#tipoProcedimento .btn-modalidade[value="2"]`).click()
                });
        })();

        window.addEventListener('load', (event) => {
            searchParams = new URLSearchParams(window.location.search);
            isLogin = searchParams.get('login');

            if (isLogin) {
                window.location.href = `${window.location.origin}/agendar`
            }

            const tipoProcedimento = document.getElementById("tipoProcedimento");
            tipoProcedimento.addEventListener("click", function(event) {

                event.preventDefault();

                const modalidade = document.querySelectorAll('.btn-modalidade');

                modalidade.forEach((el) => {
                    el.addEventListener('click', () => {
                        if (el.value == 9) {
                            document.querySelector(".btn-pesquisa").addEventListener("click", function() {
                                let procedimento = document.querySelector('.select2-selection__rendered').title;
                                parts = procedimento.split(" - ")
                                window.location.href = `https://api.whatsapp.com/send/?phone=553136588135&text=Gostaria%20de%20agendar%20o%20retorno%20da%20minha%20consulta%20da%20especialidade%20${parts[0]}%20&type=phone_number&app_absent=0`;
                            });
                        }
                    });
                })

                if (event.target.classList.contains("btn-modalidade")) {
                    const buttons = tipoProcedimento.querySelectorAll(".btn-modalidade");
                    buttons.forEach(function(button) {
                        button.classList.remove("ativo");
                    });
                    event.target.classList.add("ativo");
                }

                document.querySelectorAll(`#tipoProcedimento .btn-modalidade`).forEach(function(button) {
                    if (button.classList.value === "btn-modalidade ativo") {
                        document.querySelector(`[name="filtro__procedimento"]`).value = button.value
                    }
                });

            });
        });
    </script>

<?php
}
add_shortcode('pesquisa_agendamento', 'pesquisa_agendamento_shortcode');
