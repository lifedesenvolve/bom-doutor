<?php
function filtro_agendamento_shortcode()
{
    wp_enqueue_style('filtro-agendamento-css');
    $api = new Api();
    $lista_unidades = $api->listUnidades();
    //$lista_especialidades = $api->listEspecialidades();

?>
    <form class="form-filtro" id="form-filtro">
        <h3 class="titulo-filtro">Filtro</h3>
        <label for="filtro__data" class="label-filtro">Data:</label><br>
        <input type="date" id="filtro__data" class="filtro__data" name="filtro__data" value="<?php echo date("Y-m-d"); ?>">
        <br><br>

        <label for="filtro__procedimentos" class="label-filtro">Especialidades:</label><br>
        <select id="filtro__procedimentos" name="filtro__procedimentos">
        </select><br><br>

        <label for="filtro__unidade" class="label-filtro">Unidade:</label><br>
        <select id="filtro__unidade" name="filtro__unidade">
            <?php foreach ($lista_unidades as $unidade) { ?>
                <option value="<?php echo $unidade['id'] ?>">
                    <?php echo $unidade['cidade'] ?>
                </option>
            <?php } ?>
        </select><br><br>
        <input hidden id="filtro__tipo_procedimento" name="filtro__tipo_procedimento" />
        <button class="btn-filtro" id="btn-filtro" onclick="setStorange()">Buscar</button>
    </form>

    <script>      
        function carregar_dados_filtro(){

            lista = JSON.parse(localStorage.getItem('@@bomdoutor:dados_lista_procedimentos'))
            filtro = JSON.parse(localStorage.getItem('@@bomdoutor:dados_filtro'))

            document.getElementById('filtro__data').value = filtro.filtro__data;
            document.getElementById('filtro__procedimentos').value = filtro.filtro__procedimento_id;
            document.getElementById('filtro__unidade').value = filtro.filtro__unidade_id;
            document.getElementById('filtro__tipo_procedimento').value = filtro.filtro__tipo_procedimento;



            document.querySelector(`select#filtro__procedimentos`).innerHTML = lista.filter((procedimento)=> procedimento.tipo_procedimento == filtro.filtro__tipo_procedimento && procedimento.especialidade_id
!= null)
                .map((procedimento) => {
                return `<option value="${procedimento.procedimento_id}" ${procedimento.procedimento_id == filtro.filtro__procedimento_id ? `selected`: ``}>
                        ${procedimento.nome}
                    </option>`
            }).join().replaceAll(`,`, ``);

        }
        carregar_dados_filtro();

        function setStorange() {
            document.getElementById('form-filtro').addEventListener('submit', (event) => {
               dados_filtro = {
                "filtro__data": document.getElementById('filtro__data').value,
                "filtro__procedimento_id": document.getElementById('filtro__procedimentos').value,
                "filtro__tipo_procedimento": document.getElementById('filtro__tipo_procedimento').value,
                "filtro__unidade_id": document.getElementById('filtro__unidade').value,
               }

                localStorage.setItem('@@bomdoutor:dados_filtro', JSON.stringify(dados_filtro))
                
                event.preventDefault();
                window.location.reload(true);
            });
        }
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
