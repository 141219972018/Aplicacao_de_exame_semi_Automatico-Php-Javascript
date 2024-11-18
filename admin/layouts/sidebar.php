<nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
    <div class="sb-sidenav-menu">
        <div class="nav">
            <div class="sb-sidenav-menu-heading">Gestão</div>
            <a class="nav-link" href="index.php">
                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                Home
            </a>
            <div class="sb-sidenav-menu-heading">Utilizadores</div>
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
                <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
                Professores
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="adicionar_professor.php">Adicionar Professor</a>
                    <a class="nav-link" href="ver_professor.php">Ver professor</a>
                </nav>
            </div>
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseStudent" aria-expanded="false" aria-controls="collapsePages">
                <div class="sb-nav-link-icon"><i class="fas fa-book-open"></i></div>
                Alunos
                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
            </a>
            <div class="collapse" id="collapseStudent" aria-labelledby="headingOne" data-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                    <a class="nav-link" href="adicionar_aluno.php">Adicionar Aluno</a>
                    <a class="nav-link" href="ver_aluno.php">Ver Aluno</a>
                </nav>
            </div>
            <div class="sb-sidenav-menu-heading">Gestão de Exame</div>
            <a class="nav-link" href="turma.php">
                <div class="sb-nav-link-icon"><i class="fas fa-school"></i></div>
                Turmas 
            </a>
            <a class="nav-link" href="exame.php">
                <div class="sb-nav-link-icon"><i class="fas fa-chart-area"></i></div>
                Exames
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
            $admin = loggedAdmin();
            if($admin){
                echo htmlspecialchars($admin['nome'], ENT_QUOTES, 'UTF-8');
            }
        ?>
    </div>
</nav>