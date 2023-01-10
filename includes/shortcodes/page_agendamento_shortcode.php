<?php
function page_agendamento_shortcode()
{ 
  if (is_user_logged_in()) {
    $usuario = wp_get_current_user();
    $email = $usuario->user_email;
  }
  if (get_field('user_id', 'user_' . get_current_user_id()) == "") {


?>
    <script>
      window.location.assign("/minha-conta");
    </script>
  <?php } 


      
      if (isset($_GET['filtro__data'])) {
        $filtro_data =$_GET['filtro__data'];
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
    .info-data{
      font-family: 'Inter';
      font-style: normal;
      font-weight: 500;
      font-size: 16px;
      line-height: 19px;

      color: #383838;
    }
  </style>

  <h1 class="titulo-especialidade" id="tituloEspecialidade"></h1>
  <h3 class="info-data">Segunda-feira, 06 de Março de 2023</h3>

  <div class="lista-profissionais" id="listaProfissionais"></div>


  <!-- Modal -->
  <div class="modal fade modal-xl" id="modalAgendamento" tabindex="-1" aria-labelledby="stepModal" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="stepModal">Formulário</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="step-1">
          <div class="steps"><img src="<?php echo PLUGIN_URL . "/assets/image/etapa-1.png" ?>"></div>
            <input hidden class="form-control" id="horario_escolhido" name="horario_escolhido" type="text">
            <h3>Nome do titular </h3>
            <input class="form-control" name="nome_titular" type="text" placeholder="Nome completo" aria-label="Nome do titular">
            <br>

            <h3>CPF do titular</h3>
            <input class="form-control" name="cpf_titular" type="text" placeholder="CPF do Titular" aria-label="CPF do Titular">
            <br>

            <h3>Data de nascimento do titular</h3>
            <input class="form-control" name="data_nascimento_titular" type="date" aria-label="Data de nascimento do titular">
            <br>

            <input hidden class="form-control" name="email_titular" value="<?php echo $email; ?>" type="date">

            <h3>Gênero do titular</h3>
            <select required class="form-control" name="genero_titular" type="date" aria-label="Gênero do titular">
              <option value="M">Masculino</option>
              <option value="F">Feminino</option>
            </select>
            <br>

            <h3>Telefone do titular</h3>
            <input class="form-control" name="telefone_titular" type="phone" aria-label="Telefone do titular">
            <br>

            <button type="button" class="btn btn-default cta" id="step1">Proxima Etapa</button>
          </div>
          <div class="step-2" style="display:none;">
          <div class="steps"><img src="<?php echo PLUGIN_URL . "/assets/image/etapa-2.png" ?>"></div>
            <div>Forma de pagamento</div>
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" role="switch" id="pagamentoLocal">
              <label class="form-check-label" for="pagamentoLocal">Pagamento na Clinica</label>
            </div>
            <br>
            <button type="button" class="btn btn-default cta" id="step2">Proxima Etapa</button>
          </div>
          <div class="step-3" style="display:none;">
            <div class="steps"><img src="<?php echo PLUGIN_URL . "/assets/image/etapa-3.png" ?>"></div>
            <div>Confirmação de agendamento</div>
              Sua consulta foi agendada com sucesso.
              <br>
            <button type="button" class="btn btn-default cta">Enviar</button>
          </div>

        </div>
      </div>
    </div>
  </div>

  <script>
    const urlPlugin = "<?php echo PLUGIN_URL ;?>"
    const listaProfissionais = document.querySelector(`#listaProfissionais`);
    const tituloEspecialidade = document.querySelector(`#tituloEspecialidade`);

    function capturarDados() {
      const nomeTitular = document.querySelector('[name=nome_titular]').value;
      const cpfTitular = document.querySelector('[name=cpf_titular]').value;
      const dataNascimentoTitular = document.querySelector('[name=data_nascimento_titular]').value;
      const emailTitular = document.querySelector('[name=email_titular]').value;
      const generoTitular = document.querySelector('[name=genero_titular]').value;
      const telefoneTitular = document.querySelector('[name=telefone_titular]').value;
      const horarioEscolhido = document.querySelector('[name=horario_escolhido]').value;

      const dados = {
        nome_titular: nomeTitular,
        cpf_titular: cpfTitular,
        data_nascimento_titular: dataNascimentoTitular,
        email_titular: emailTitular,
        genero_titular: generoTitular,
        telefone_titular: telefoneTitular,
        horario_escolhido: horarioEscolhido
      };


      return dados;
    }

    const searchURL = new URLSearchParams(window.location.search);
    const params = {
      unidade: searchURL.get('filtro__unidade'),
      especialidade: searchURL.get('filtro__especialidades'),
      data: searchURL.get('filtro__data'),
    };
    
    const url =  origin +`/wp-json/api/v1/lista-profissionais/`;

    const queryString = new URLSearchParams(params).toString();
    const options = {method: 'GET'};

    fetch(`${url}?${queryString}`, options)
      .then(response => response.json())
      .then(response => {console.log(response)
        const {
          profissionais
        } = response;
        console.log(`response:`, response)

        html = profissionais.map(profissional => {

        const { horarios_disponiveis } = profissional;
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
        });
        listaProfissionais.innerHTML = html.join().replaceAll(`,`, ``);
        const botoes = document.querySelectorAll('.btn-horario');
        
        
        const select = document.querySelector('#filtro__especialidades');
        document.querySelector(`#tituloEspecialidade`).innerText = select.options[select.selectedIndex].text;

        botoes.forEach(botao => {
          botao.addEventListener('click', function() {
            console.log(this.innerText);
            document.querySelector('#horario_escolhido').value = this.innerHTML;
          });
        });

      })
      .catch(err => {
        console.error(err)
        document.querySelector(`#tituloEspecialidade`).innerText = "Nenhum horário disponível";
      });

      window.onload = function() {
      document.querySelector(`#step1`).onclick = function() {
        if (capturarDados()) {

          const dados = capturarDados();

          fetch('<?php echo home_url('wp-json/api/v1/registrar-paciente'); ?>', {
            method: 'POST',
            body: JSON.stringify(dados)
          }).then(function(response) {
            return response.json();
          }).then(function(resultado) {
            console.log(resultado);
          });

          document.querySelector(`.step-1`).style.display = 'none';
          document.querySelector(`.step-2`).style.display = 'block';
        } else {
          alert('preencha todos os campos');
        }
      }
      document.querySelector(`#step2`).onclick = function() {
        document.querySelector(`.step-2`).style.display = 'none';
        document.querySelector(`.step-3`).style.display = 'block';
      }

    }
  </script>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js" integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous"></script>
  </body>
  <?php
}
add_shortcode('page_agendamento', 'page_agendamento_shortcode');
