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

        <!-- <label for="filtro__procedimento" class="label-filtro">Procedimentos:</label><br>
        <select id="filtro__procedimento" name="filtro__procedimento">
            <option value="">Selecione o procedimento</option>
        </select><br><br> -->

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
        <input hidden id="filtro__procedimento" name="filtro__procedimento" />
        <button class="btn-filtro" id="btn-filtro">Buscar</button>
    </form>

    <script>
        const searchParams = new URLSearchParams(window.location.search);

        const filtro_data = searchParams.get('filtro__data');
        const filtro_especialidades = searchParams.get('filtro__especialidades');
        const filtro_unidade = searchParams.get('filtro__unidade');
        const filtro__procedimento = searchParams.get('filtro__procedimento');

        document.getElementById('filtro__data').value = filtro_data;
        document.getElementById('filtro__especialidades').value = filtro_especialidades;
        document.getElementById('filtro__unidade').value = filtro_unidade;
        document.getElementById('filtro__procedimento').value = filtro__procedimento;

        function lista_procedimentos() {
            const base_url = '<?php echo home_url(); ?>';
            const options = {
                method: 'GET'
            };
            fetch(`${base_url}/wp-json/api/v1/lista-procedimentos`, options)
                .then(response => response.json())
                .then(response => {
                    const selectProcedimento = document.querySelector('#filtro__procedimento');
                    selectProcedimento.innerHTML = '<option value="">Selecione o procedimento</option>';

                    response.forEach((item) => {
                        htmlProcedimentos += `<option value="${item.procedimento_id}">${item.procedimento_nome}</option>`
                        selectProcedimento.innerHTML = options;
                    });
                })
                .catch(err => console.error(err));
        }
        //lista_procedimentos();


        function lista_especialidades(tipo_procedimento) {
            const base_url = '<?php echo home_url(); ?>';
            const options = {
                method: 'GET'
            };
            fetch(`${base_url}/wp-json/api/v1/listar-especialidades/?tipo_procedimento=${tipo_procedimento}`, options)
                .then(response => response.json())
                .then(response => {
                    const selectEspecialidade = document.querySelector('#filtro__especialidades');
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
        lista_especialidades(filtro__procedimento);
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
