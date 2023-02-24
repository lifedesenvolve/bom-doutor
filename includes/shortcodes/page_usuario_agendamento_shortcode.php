<?php

function page_usuario_agendamento_shortcode()
{ ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <section class="lista-agendamentos container-fluid">

        <div class="row py-3">
            <div class="col-6">
                <div class="card-title">Procedimentos</div>
                <p>Ut tellus elementum sagittis vitae et leo</p>
            </div>

            <div class="col-6">
                <div class="card-title">Ácidos graxos livres</div>
                <div class="container-fluid p-0">
                    <div class="row py-1">
                        <div class="col-1"><i class="fab fa-facebook"></i></div>
                        <div class="col-11">Dr. Marcelo T. Magalhães</div>
                    </div>
                    <div class="row py-1">
                        <div class="col-1"><i class="fab fa-facebook"></i></div>
                        <div class="col-3">27/03/2023</div>
                        <div class="col-1"><i class="fab fa-facebook"></i></div>
                        <div class="col-3">10:35</div>
                    </div>
                    <div class="row py-1">
                        <div class="col-1"><i class="fab fa-facebook"></i></div>
                        <div class="col-11">R. Jonas Jean, 77 - Ouro Preto, BH - MG</div>
                    </div>
                    <div class="row py-3">
                        <div class="col-3"><button class="btn btn-sm btn-info">Reagendar</button></div>
                        <div class="col-3"><button class="btn btn-sm btn-info">Cancelar</button></div>
                        <div class="col-6"></div>
                    </div>
                </div>
            </div>
            <hr>
        </div>

    </section>

    <script>
        function buscarAgendamentos(paciente_id, data_start) {

            const base_url = '<?php echo home_url(); ?>';
            const options = {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            };

            fetch(`${base_url}/wp-json/api/v1/paciente-agendamentos?paciente_id=${paciente_id}&data_start=${data_start}`, options)
                .then(response => response.json())
                .then(data => console.log(data))
                .catch(err => console.error(err));
        }

        buscarAgendamentos(149, '13-02-2023');
    </script>

<?php }
add_shortcode('page_usuario_agendamento', 'page_usuario_agendamento_shortcode');
