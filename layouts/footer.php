<footer class="py-4 bg-light mt-auto">
    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between small">
            <div class="text-muted">&copy; Exame Sem-Automático 2023</div>
            <div>
                <a href="#">Política de Privacidade</a>
                &middot;
                <a href="#">Termos e Condições</a>
            </div>
        </div>
    </div>
</footer>
<script src="../sbadmin/js/jquery.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/scripts.js"></script>
<script src="../sbadmin/js/toastr.min.js"></script>
<script src="../sbadmin/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="../sbadmin/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>

<script type="text/javascript">
    $(document).ready(function() {
        //Sugestões de pesquisa do aluno
        $('#pesquisa_aluno').on('keyup', function() {
            $('#pesquisa_resultado').dropdown('hide');
            $('#pesquisa_resultado').html('');
            var search_value = $(this).val();
            console.log(search_value);
            $.ajax({
                type: 'POST',
                url: '../includes/pesquisa_de_aluno_ajax.php',
                data: {
                    search_value: search_value
                },
                success: function(resultado) {
                    if (resultado.length > 0) {
                        $('#pesquisa_resultado').dropdown('show');
                        $('#pesquisa_resultado').html(resultado);
                        $('.pesquisa-item-resultado').on('click', function() {
                            var href = $(this).attr('href');
                            window.open(href, '_blank');
                        });
                    } else {
                        $('#pesquisa_resultado').dropdown('hide');
                    }
                }
            });
        });
    });
</script>