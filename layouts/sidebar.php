<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">Gestão</div>
            <a class="nav-link" href="index.php">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Home
            </a>

            <div class="sb-sidenav-menu-heading">Relatórios</div>
            <a class="nav-link" href="ver_aluno.php">
                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                Alunos
            </a>


            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#recolherExames" aria-expanded="false" aria-controls="collapseLayouts">
                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                Exames
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="recolherExames" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="criar_exame.php">Criar exame</a>
                    <a class="nav-link" href="ver_exame.php">Ver exames</a>
                    <a class="nav-link" href="atribuir_exame.php">Atribuir exames</a>
                </nav>
            </div>

            <a class="nav-link" href="turma.php">
                <div class="sb-nav-link-icon"><i class="fas fa-school"></i></div>
                Turma
            </a>



            <a class="nav-link" href="resultados.php">
                <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
                Resultados
            </a>
        </div>
    </div>
    <div class="sb-sidenav-footer">
        <div class="small">Conectado como:</div>
        <?php
        $professor = professorConectado();
        if ($professor) {
            echo htmlspecialchars($professor['nome'], ENT_QUOTES, 'UTF-8');
        }
        ?>
    </div>
</nav>