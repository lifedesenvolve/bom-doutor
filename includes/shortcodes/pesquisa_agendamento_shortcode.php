<?php

function pesquisa_agendamento_shortcode()
{
    $api = new Api();
    $lista_unidades = $api->listUnidades();
    $lista_especialidades = $api->listEspecialidades();



?>
    <style>
        .group-modalidade {
            padding: 0% 0% 2% 0%;
            display: flex;
        }

        .btn-modalidade {
            font-family: "Open Sans", Sans-serif;
            font-size: 0.9vw;
            font-weight: 500;
            fill: #737373;
            color: #737373;
            background-color: #FFFFFF;
            padding: 1em 1.5em 1em 1.5em;
            border-radius: 3px;
            margin-right: 2%;
            height: 100%;
            max-height: 70px;
            line-height: 1;
        }
        .group-inputs {
            display: flex;
            gap: 20px;
        }
        .group-inputs select{
            background-color: #ffffff;
            border-width: 0px 0px 0px 0px;
            font-family: "Open Sans", Sans-serif;
            font-size: 1vw;
            font-weight: 400;
        }
        .btn-pesquisa{
            background-color: #2A2860;
            color: #ffffff;
            font-family: "Open Sans", Sans-serif;
            font-size: 0.9vw;
            font-weight: 500;
            padding: 18px;
            line-height: 1;
        }
    </style>
    <form id="form-agendamento">
        <div class="group-modalidade" id="tipoProcedimento">
            <button class="btn-modalidade active" value="5">
                Consulta
            </button>
            <button class="btn-modalidade">
                Retorno
            </button>
        </div>
        <div class="group-inputs">
        <select id="unidade" name="unidade">
            <?php foreach ($lista_unidades as $unidade) { ?>
                <option value="<?php echo $unidade['id'] ?>"><?php echo $unidade['cidade'] ?></option>
            <?php } ?>
        </select>
        <select id="especialidade" name="especialidade" class="modalidade-item">
            <option value="">Selecione a especialidade</option>
            <?php foreach ($lista_especialidades as $especialidade) { ?>
                <option value="<?php echo $especialidade['especialidade_id'] ?>"><?php echo $especialidade['nome'] ?></option>
            <?php } ?>
        </select>
        <input type="hidden" name="filtro__data" value='<? date("Y-m-d"); ?>'>
        <a class="elementor-button elementor-size-sm btn-pesquisa" onclick="pesquisaFeeGow()" style="cursor: pointer;">Pesquisar</a>
        </div>


    </form>
    <script>
        const tiposProcedimentos = [
            {
                "id": 1,
                "tipo": "Cirurgia"
            },
            {
                "id": 2,
                "tipo": "Consulta"
            },
            {
                "id": 3,
                "tipo": "Exame"
            },
            {
                "id": 4,
                "tipo": "Procedimento"
            },
            {
                "id": 9,
                "tipo": "Retorno"
            }
        ]
        
        tiposProcedimentos.map((item) => {
            return `<button class="btn-modalidade" value="${item.id}">
                        ${item.tipo}
                    </button>`
        }).join().replaceAll(`,`,``)

        function pesquisaFeeGow() {
            let unidade_id = document.getElementById("unidade").value;
            let especialidade = document.getElementById("especialidade").value;

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
                    // os campos foram selecionados, redireciona a página
                    window.location.assign(`<?php echo home_url() ?>/agendar/?filtro__data=<?php echo date('Y-m-d') ?>&filtro__especialidades=${especialidade}&filtro__unidade=${unidade_id}`);
                <?php } else { ?>
                    elementorProFrontend.modules.popup.showPopup({
                        id: 1376
                    });
                <?php } ?>
            }
        }
    </script>

<?php
}
add_shortcode('pesquisa_agendamento', 'pesquisa_agendamento_shortcode');
