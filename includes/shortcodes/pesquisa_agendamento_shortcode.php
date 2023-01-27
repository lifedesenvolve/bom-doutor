<?php

function pesquisa_agendamento_shortcode()
{
    wp_enqueue_style('pesquisa-agendamento-css');
    $api = new Api();
    $lista_unidades = $api->listUnidades();
?>
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

        function create_select_mobile() {
            document.getElementById("tipoProcedimento").insertAdjacentHTML("afterend", "<select id='selectForMobile'></select>");

            let btnContainer = document.getElementById("tipoProcedimento");
            let select = document.getElementById('selectForMobile');
            select.id = "selectProcedimento";

            // Adiciona as opções ao select baseado nos botões existentes
            for (let i = 0; i < btnContainer.children.length; i++) {
                let option = document.createElement("option");
                option.value = btnContainer.children[i].value;
                option.text = btnContainer.children[i].textContent;
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
            let lista = dados_lista_procedimentos.filter(item => item.tipo_procedimento == tipoProcedimento && item.especialidade_id != null && item.permite_agendamento_online
 == true)

            console.log(lista);

            lista.forEach(info => {
                options += `<option value="${info.procedimento_id}">${info.nome}</option>`;
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

        async function lista_tipo_procedimentos() {
            const base_url = '<?php echo home_url(); ?>';
            const options = {
                method: 'GET'
            };

            await fetch(`${base_url}/wp-json/api/v1/lista-procedimentos`, options)
                .then(response => response.json())
                .then(response => {
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
                })
            });
        });
    </script>

<?php
}
add_shortcode('pesquisa_agendamento', 'pesquisa_agendamento_shortcode');
