<?php

function confirmacao_agendamento_shortcode()
{
    wp_enqueue_style('pesquisa-agendamento-css');
 ?>    
    <div id="dadosAgendamento" style="margin: 4em;"></div>
    <script>
        localStorage.setItem('@@bomdoutor:dados_lista_procedimentos', "");
        localStorage.setItem('@@bomdoutor:dados_filtro', "");
        const dadosAgendamento = JSON.parse(localStorage.getItem('@@bomdoutor:dados_confirmacao_agendamento'));

        console.log(dadosAgendamento)

        document.querySelector(`#dadosAgendamento`).innerHTML = `
            <div class="row"><p class="col-sm-3">Parabéns, seu agendamento está confirmado! Aqui estão os detalhes do seu compromisso:</p></div>
            <br>
            <div class="row"><b class="col-sm-3">Paciente: </b><span class="col-sm-9">${dadosAgendamento.nomePaciente}</span></div>
            <div class="row"><b class="col-sm-3">Médico: </b><span class="col-sm-9">${dadosAgendamento.nomeMedico}</span></div>
            <div class="row"><b class="col-sm-3">Especialidade: </b><span class="col-sm-9">${dadosAgendamento.nomeProcedimento}</span></div>
            <div class="row"><b class="col-sm-3">Valor: </b><span class="col-sm-9">R$ ${dadosAgendamento.valorProcedimento}</span></div>
            <div class="row"><b class="col-sm-3">Local: </b><span class="col-sm-9">Av. Afonso Pena, nº 955, Loja 03 - Centro, Belo Horizonte, MG.</span></div>
            <div class="row"><b class="col-sm-3">Data: </b><span class="col-sm-9">${dadosAgendamento.dataAgendada}</span></div>
            <br>
            <div class="row"><p class="col-sm-3">Por favor, chegue ao local de sua consulta 10 minutos antes do horário agendado. Se precisar de qualquer alteração ou cancelamento, entre em contato conosco o mais rápido possível. Estamos ansiosos para atendê-lo e garantir sua saúde e bem-estar.</p></div>
            <br>
            `;
    </script>

<?php
}
add_shortcode('confirmacao_agendamento', 'confirmacao_agendamento_shortcode');