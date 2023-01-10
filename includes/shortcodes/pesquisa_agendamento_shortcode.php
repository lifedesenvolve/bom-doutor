<?php


function pesquisa_agendamento_shortcode()
{
    $api = new Api();
    $lista_unidades = $api->listUnidades();
    $lista_especialidades = $api->listEspecialidades();

?>
<style>
    .group-modalidade {
        background: green;
        padding: 0% 33.5% 1% 22.7%;
    }
    .btn-modalidade{
        font-family: "Open Sans", Sans-serif;
        font-size: 0.9vw;
        font-weight: 500;
        fill: #737373;
        color: #737373;
        background-color: #FFFFFF;
        padding: 1em 1.5em 1em 1.5em;
        border-radius: 3px;
    }
    #unidade{
        font-family: "Open Sans", Sans-serif;
        font-size: 1vw;
        font-weight: 400;
    }
</style>
    <form id="form-agendamento">
        <div class="group-modalidade">
            <button class="btn-modalidade">
                Consulta
            </button>
            <button class="btn-modalidade">
                Retorno
            </button>
        </div>
        <br><br>
        <select id="unidade" name="unidade">
            <option value="">Selecione a Unidade</option>
            <?php foreach ($lista_unidades as $unidade) { ?>
                <option value="<?php echo $unidade['id'] ?>"><?php echo $unidade['cidade'] ?></option>
            <?php } ?>
        </select>
        <br><br>
        <label for="especialidade" class="modalidade-item">Selecione a especialidade:</label><br>
        <select id="especialidade" name="especialidade" class="modalidade-item">
            <option value="">Selecione a especialidade</option>
            <?php foreach ($lista_especialidades as $especialidade) { ?>
                <option value="<?php echo $especialidade['especialidade_id'] ?>"><?php echo $especialidade['nome'] ?></option>
            <?php } ?>
        </select>
        <br><br>
        <a class="elementor-button elementor-size-sm" onclick="pesquisaFeeGow()" style="cursor: pointer;">Pesquisar</a>
    </form>
    <script>
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
                    window.location.assign(`<?php echo home_url() ?>/agendar/?filtro__especialidades=${especialidade}&filtro__unidade=${unidade_id}`);
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
