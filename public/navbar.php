<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" style="padding-bottom: 10px; height: 60px;">
    <div class="container-fluid">
        <a class="navbar-brand me-auto" href="#">Artigo Autores Lt Cloud</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Início</a>
                </li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'author'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="my_publications.php">Minhas Publicações</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="../controllers/logoutController.php">Sair</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div style="margin-top: 70px;"></div>
