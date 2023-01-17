<?php

function pesquisa_agendamento_shortcode()
{
    wp_enqueue_style('pesquisa-agendamento-css');
    $api = new Api();
    $lista_unidades = $api->listUnidades();
    $lista_especialidades = $api->listEspecialidades();
?>
    <div id="form-agendamento">
        <div class="group-modalidade" id="tipoProcedimento"></div>
        <div class="group-inputs">
            <select id="unidade" name="filtro__unidade">
                <?php foreach ($lista_unidades as $unidade) { ?>
                    <option value="<?php echo $unidade['id'] ?>"><?php echo $unidade['cidade'] ?></option>
                <?php } ?>
            </select>
            <select id="especialidade" name="filtro__especialidades" class="modalidade-item">
                <option value="">Selecione a especialidade</option>
            </select>
            <input type="hidden" name="filtro__data" value='<?php echo date("Y-m-d"); ?>'>
            <input type="hidden" name="filtro__procedimento" value='2'>
            <button class="elementor-button elementor-size-sm btn-pesquisa" onclick="pesquisaFeeGow()" style="cursor: pointer;">Pesquisar</button>
        </div>
    </div>

    <script>
        searchParams = new URLSearchParams(window.location.search);
        isLogin = searchParams.get('login');
        
        if(isLogin){
            window.location.href = `${window.location.origin}/agendar`
        }

        function getEspecialidadeByProcedimentoId(procedimento_id) {
            const base_url = '<?php echo home_url(); ?>';

            const body = {
                "procedimento_id": procedimento_id
            }

            const options = {
                method: 'GET'
            };
            fetch(`${base_url}/wp-json/api/v1/get-especialidades/?procedimento_id=${procedimento_id}`, options)
                .then(response => response.json())
                .then(response => {
                    console.log(response)
                })
                .catch(err => console.error(err));
        }

        function pesquisaFeeGow() {
            const unidade_id = document.getElementById("unidade").value;
            const especialidade = document.getElementById("especialidade").value;
            const filtro__procedimento_selected = document.querySelector(`[name="filtro__procedimento"]`).value;
            const data__filter = "<?php echo date('Y-m-d'); ?>"
            
            localStorage.setItem('@@bomdoutor:filtro__data', data__filter);
            localStorage.setItem('@@bomdoutor:filtro__especialidades', especialidade);
            localStorage.setItem('@@bomdoutor:filtro__unidade', unidade_id);
            localStorage.setItem('@@bomdoutor:filtro__procedimento', filtro__procedimento_selected);

            if (unidade_id === "") {
                document.getElementById("unidade").style.borderColor = "red";
            } else {
                document.getElementById("unidade").style.borderColor = "#D2D1D6";
            }
            if (especialidade === "") {
                document.getElementById("especialidade").style.borderColor = "red";
            } else {
                document.getElementById("especialidade").style.borderColor = "#D2D1D6";
            }

            if (unidade_id !== "" && especialidade !== "") {
                <?php if (is_user_logged_in()) { ?>
                    window.location.assign(`<?php echo home_url() ?>/agendar/?filtro__data=${data__filter}&filtro__especialidades=${especialidade}&filtro__unidade=${unidade_id}&filtro__procedimento=${filtro__procedimento_selected}`);
                    <?php  } else {  ?>
                    elementorProFrontend.modules.popup.showPopup({
                        id: 1376
                    });
                 <?php  } ?>
            }
            
        }

        function lista_procedimentos() {
            const base_url = '<?php echo home_url(); ?>';
            const options = {
                method: 'GET'
            };
            fetch(`${base_url}/wp-json/api/v1/lista-procedimentos`, options)
                .then(response => response.json())
                .then(response => {
                    let htmlProcedimentos = '';
                    response.forEach((item) => {
                        htmlProcedimentos += `<button class="btn-modalidade" value="${item.procedimento_id}" data-procedimento="${item.procedimento_nome}">
                    ${item.procedimento_nome}
                </button>`
                    });

                    document.querySelector('#tipoProcedimento').innerHTML = htmlProcedimentos;
                })
                .catch(err => console.error(err));
        }

        lista_procedimentos();

        function lista_especialidades(tipo_procedimento) {
            const base_url = '<?php echo home_url(); ?>';
            const options = {
                method: 'GET'
            };
            fetch(`${base_url}/wp-json/api/v1/listar-especialidades/?tipo_procedimento=${tipo_procedimento}`, options)
                .then(response => response.json())
                .then(response => {
                    const selectEspecialidade = document.querySelector('#especialidade');
                    selectEspecialidade.innerHTML = '<option value="">Selecione a especialidade</option>';

                    let options = '';
                    // Verifies if the especialidades object exists before iterating over it

                    response.especialidades.forEach(especialidade => {
                        options += `<option value="${especialidade.especialidade_id}">${especialidade.especialidade_nome}</option>`;
                        selectEspecialidade.innerHTML = options;
                    });
                })
                .catch(err => console.error(err));
        }

        function loadEspecialidades(tipoProcedimento) {
            const selectEspecialidade = document.querySelector('#especialidade');
            selectEspecialidade.innerHTML = '<option value="">Selecione a especialidade</option>';

            lista_especialidades(tipoProcedimento)
                .then(response => {
                    console.log(`tipoProcedimento`, response)
                    if (response.ok) {
                        return response.json();
                    }
                    throw new Error(response.statusText);
                })
                .then(data => {
                    let options = '';
                    // Verifies if the especialidades object exists before iterating over it
                    if (data.hasOwnProperty('especialidades')) {
                        data.especialidades.forEach(especialidade => {
                            options += `<option value="${especialidade.especialidade_id}">${especialidade.especialidade_nome}</option>`;
                        });
                        selectEspecialidade.innerHTML = options;
                    }
                    // Hides the loading message
                    loading.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Hides the loading message
                    loading.style.display = 'none';
                });
        }


        function procedimentoAtivo() {
            document.querySelectorAll(`#tipoProcedimento button`).forEach((item) => {
                if (item.value == document.querySelector(`[name="filtro__procedimento"]`).value) {
                    item.setAttribute(`class`, `btn-modalidade ativo`)
                }
            })
            document.addEventListener('click', (event) => {
                if (event.target.classList.contains('btn-modalidade')) {
                    const selectedValue = event.target.value;
                    loadEspecialidades(selectedValue)
                }
            });
        }
        procedimentoAtivo();



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
    </script>

<?php
}
add_shortcode('pesquisa_agendamento', 'pesquisa_agendamento_shortcode');
