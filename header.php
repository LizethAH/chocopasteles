<?php
require_once(__DIR__ . "/../config/database.php");
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");
?>  

<header>
<link rel="stylesheet" href="assets/css/style.css?v=3">
    <div class="header-content">
        <div class="logo">
            <a href="index.php"><img src="assets/img/LOGO.JPG" alt="Logo"></a>
        </div>
        <nav>
            <ul>
                <li><a href="index.php">INICIO</a></li>
                <li class="dropdown">
                    <a href="categorias.php">CATEGORÍAS</a>
                    <ul class="dropdown-content">
                        <?php while($cat = $categorias->fetch_assoc()): ?>
                            <li><a href="categorias.php?cat=<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></a></li>
                        <?php endwhile; ?>
                    </ul>
                </li>
                <li><a href="sobre.php">SOBRE NOSOTROS</a></li>
                <li><a href="ubicacion.php">UBICACIÓN</a></li>
                <li><a href="contacto.php">CONTACTO</a></li>
                <li>
                  <a href="admin/index.php" id="adminLoginBtn">
               <i class="fas fa-user-shield"></i> Admin
                    </a>
               </li>

            </ul>
        </nav>
    </div>
</header>
