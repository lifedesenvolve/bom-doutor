<?php
function filtro_agendamento_shortcode()
{
    wp_enqueue_style('filtro-agendamento-css');
    $api = new Api();
    $lista_unidades = $api->listUnidades();
    $lista_especialidades = $api->listEspecialidades();

?>
    <form class="form-filtro">
        <h3 class="titulo-filtro">Filtro</h3>
        <label for="filtro__data" class="label-filtro">Data:</label><br>
        <input type="date" id="filtro__data" class="filtro__data" name="filtro__data" value="<?php echo date("Y-m-d"); ?>">
        <br><br>
        <label for="filtro__especialidades" class="label-filtro">Especialidades:</label><br>
        <select id="filtro__especialidades" name="filtro__especialidades">
            <?php foreach ($lista_especialidades as $especialidade) { ?>
                <option value="<?php echo $especialidade['especialidade_id'] ?>"><?php echo $especialidade['nome'] ?></option>
            <?php } ?>
        </select><br><br>

        <label for="filtro__unidade" class="label-filtro">Unidade:</label><br>
        <select id="filtro__unidade" name="filtro__unidade">
            <?php foreach ($lista_unidades as $unidade) { ?>
                <option value="<?php echo $unidade['id'] ?>">
                    <?php echo $unidade['cidade'] ?>
                </option>
            <?php } ?>
        </select><br><br>
        <input type="hidden" name="filtro__procedimento">
        <button class="btn-filtro" id="btn-filtro">Buscar</button>
    </form>

    <script>
        const searchParams = new URLSearchParams(window.location.search);

        const filtro_data = searchParams.get('filtro__data');
        const filtro_especialidades = searchParams.get('filtro__especialidades');
        const filtro_unidade = searchParams.get('filtro__unidade');

        document.getElementById('filtro__data').value = filtro_data;
        document.getElementById('filtro__especialidades').value = filtro_especialidades;
        document.getElementById('filtro__unidade').value = filtro_unidade;
    </script>

<?php
}
add_shortcode('filtro_agendamento', 'filtro_agendamento_shortcode');

function shortcode_atribuir_valores()
{
?>
    <script>
        function atribuirValores(nome, cpf, sexo, nascimento, email, telefone) {
            document.querySelector('#form-field-paciente_nome').value = nome;
            document.querySelector('#form-field-paciente_cpf').value = cpf;
            document.querySelector('#form-field-paciente_sexo').value = sexo;
            document.querySelector('#form-field-paciente_data_nascimento').value = nascimento;
            document.querySelector('#form-field-paciente_email').value = email;
            document.querySelector('#form-field-paciente_telefone').value = telefone;
        }

        function popularInputs(dados) {
            atribuirValores(dados.content.nome, dados.content.documentos.cpf, dados.content.sexo, dados.content.nascimento, dados.content.email[0], dados.content.telefones[0]);
        }
        <?php
        $existe = strpos($_SERVER['REQUEST_URI'], 'minha-conta');
        if ($existe !== false) {
            if (get_field('user_id', 'user_' . get_current_user_id()) !== "") {
                $api = new Api();
                $getPaciente = $api->getPaciente(get_field('user_id', 'user_' . get_current_user_id()));
                echo 'const apiResponse = ' . $getPaciente . '; popularInputs(apiResponse);';
            }
        }
        ?>
    </script>
<?php
}
add_action('wp_footer', 'shortcode_atribuir_valores');
