<?php


function pesquisa_agendamento_shortcode()
{
    $api = new Api();
    $lista_unidades = $api->listUnidades();
    $lista_especialidades = $api->listEspecialidades();

?>
    <form id="form-agendamento">
        <input type="radio" name="modalidade" value="consulta" checked> Consulta
        <input type="radio" name="modalidade" value="retorno"> Retorno

        <br><br>
        <label for="unidade">Selecione a unidade:</label><br>
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
