<?php
require_once("config/database.php");

// Obtener productos
$productos = $conn->query("SELECT p.*, c.nombre AS categoria FROM productos p LEFT JOIN categorias c ON p.categoria_id = c.id ORDER BY p.id DESC");

// Obtener categorías para el menú
$categorias = $conn->query("SELECT * FROM categorias ORDER BY nombre ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Chocopasteles - Inicio</title>
  <?php include __DIR__ . "/includes/head.php"; ?>
  <link rel="stylesheet" href="assets/css/styles.css">
 <?php include __DIR__ . "/includes/adminModal.php"; ?>
  <title>Chocopasteles</title>
</head>
<body>
<header>
   <?php include("includes/header.php"); ?>
</header>
<main>
  
    <section class="portada" style="background-image: url('assets/img/banner.jpg');">
  <div class="contenido-portada">
    <h1>¡Descubre nuestros postres artesanales!</h1>
    <h1>
      Busca el postre de tu gusto y saborealo en nuestra tienda.
      Deliciosas tortas y postres artesanales hechos con amor para cada persona❤️</h1>

    <!-- Botón de calificación -->
    <a href="https://maps.app.goo.gl/AcouodwsR6MPbUzq9" target="_blank" class="btn-maps">
      ⭐ Califícanos en Google Maps
    </a>
  </div>
</section>

    <div class="productos-lista">
        <?php while($prod = $productos->fetch_assoc()): ?>
        <div class="producto-card">
            <?php if ($prod['imagen']): ?>
                <img src="assets/img/<?= htmlspecialchars($prod['imagen']) ?>" alt="<?= htmlspecialchars($prod['nombre']) ?>" width="180">
            <?php endif; ?>
            <h3><?= htmlspecialchars($prod['nombre']) ?></h3>
            <t1><?= htmlspecialchars($prod['descripcion']) ?></t1>
            <span class="precio">S/ <?= number_format($prod['precio'], 2) ?></span>
            <button class="agregar-carrito" data-id="<?= $prod['id'] ?>">Agregar al carrito</button>
        </div>
        <?php endwhile; ?>
    </div>
</main>
<footer style="background:#5D4037;color:#fff;">
    <?php include("includes/footer.php"); ?>
</footer>
<!-- Carrito flotante -->
<div id="carrito-flotante">
  <div id="carrito-header">
    <span>🛒 Carrito (<span id="carrito-cantidad">0</span>)</span>
    <button id="carrito-cerrar">×</button>
  </div>
  <div id="carrito-lista"></div>
  <div id="carrito-total"></div>
  <button id="carrito-whatsapp">Enviar por WhatsApp</button>
  <button id="carrito-qr">Pagar por QR</button>
  <div id="qr-container" 
  style="text-align:center; margin-top:10px;"></div>
</div>
<button id="carrito-toggle">🛒</button>

<script src="assets/js/main.js"></script>

<script>
// Productos desde PHP para JS
// Productos desde PHP para JS
const productosData = {};
<?php
$prods = $conn->query("SELECT id, nombre, precio FROM productos");
while($p = $prods->fetch_assoc()):
?>
productosData[<?= $p['id'] ?>] = {
  nombre: "<?= addslashes($p['nombre']) ?>",
  precio: <?= $p['precio'] ?>
};
<?php endwhile; ?>

let carrito = JSON.parse(localStorage.getItem('carritoChoco')) || {};

function actualizarCarrito() {
  let lista = '';
  let total = 0;
  let cantidad = 0;
  for (let id in carrito) {
    let prod = productosData[id];
    let cant = carrito[id];
    total += prod.precio * cant;
    cantidad += cant;
    lista += `<div>
      <b>${prod.nombre}</b> x${cant} - S/ ${(prod.precio * cant).toFixed(2)}
      <button onclick="cambiarCantidad(${id},-1)">-</button>
      <button onclick="cambiarCantidad(${id},1)">+</button>
      <button onclick="eliminarProducto(${id})">🗑️</button>
    </div>`;
  }
  document.getElementById('carrito-lista').innerHTML = lista || '<em>Carrito vacío</em>';
  document.getElementById('carrito-total').innerHTML = 'Total: S/ ' + total.toFixed(2);
  document.getElementById('carrito-cantidad').textContent = cantidad;
}

function cambiarCantidad(id, delta) {
  carrito[id] = (carrito[id] || 0) + delta;
  if (carrito[id] <= 0) delete carrito[id];
  localStorage.setItem('carritoChoco', JSON.stringify(carrito));
  actualizarCarrito();
}

function eliminarProducto(id) {
  delete carrito[id];
  localStorage.setItem('carritoChoco', JSON.stringify(carrito));
  actualizarCarrito();
}

// Agregar producto con SweetAlert
document.querySelectorAll('.agregar-carrito').forEach(btn => {
  btn.onclick = () => {
    let id = btn.dataset.id;
    carrito[id] = (carrito[id] || 0) + 1;
    localStorage.setItem('carritoChoco', JSON.stringify(carrito));
    actualizarCarrito();
    document.getElementById('carrito-flotante').style.display = 'block';

    // SweetAlert notificación
    Swal.fire({
      position: 'top-end',
      icon: 'success',
      title: 'Producto agregado al carrito',
      showConfirmButton: false,
      timer: 1200,
      toast: true
    });
  };
});

// Toggle carrito
document.getElementById('carrito-toggle').onclick = () => {
  document.getElementById('carrito-flotante').style.display = 'block';
};
document.getElementById('carrito-cerrar').onclick = () => {
  document.getElementById('carrito-flotante').style.display = 'none';
};

// Enviar por WhatsApp
document.getElementById('carrito-whatsapp').onclick = () => {
  let mensaje = "¡Hola! Quiero pedir:\n";
  for (let id in carrito) {
    let prod = productosData[id];
    mensaje += `• ${prod.nombre} x${carrito[id]} - S/ ${(prod.precio * carrito[id]).toFixed(2)}\n`;
  }
  mensaje += "\nTotal: S/ " + Object.keys(carrito).reduce((t, id) => t + productosData[id].precio * carrito[id], 0).toFixed(2);
  window.open("https://wa.me/59164939922?text=" + encodeURIComponent(mensaje), "_blank");
  
  carrito = {};
  localStorage.removeItem('carritoChoco');
  actualizarCarrito();
  document.getElementById('carrito-flotante').style.display = 'none';
};

// Generar QR de pago
document.getElementById('carrito-qr').onclick = () => {
  let total = Object.keys(carrito).reduce((t, id) => t + productosData[id].precio * carrito[id], 0).toFixed(2);
  if (total <= 0) {
    Swal.fire("Tu carrito está vacío", "", "warning");
    return;
  }

  document.getElementById('qr-container').innerHTML = ""; // limpiar QR anterior

  new QRCode(document.getElementById("qr-container"), {
    text: "Pago Chocopasteles - Total: S/ " + total,
    width: 200,
    height: 200,
    colorDark : "#5D4037",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H
  });

  Swal.fire({
    title: 'Escanea para pagar',
    html: `<div id="qr-modal"></div><p>Total: <b>S/ ${total}</b></p>`,
    showConfirmButton: false,
    didOpen: () => {
      new QRCode(document.getElementById("qr-modal"), {
        text: "Pago Chocopasteles - Total: S/ " + total,
        width: 200,
        height: 200,
        colorDark : "#5D4037",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
      });
    }
  });
};

actualizarCarrito();

</script>   
<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Librería para generar QR -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
document.getElementById('carrito-qr').onclick = () => {
  let total = Object.keys(carrito).reduce((t, id) => t + productosData[id].precio * carrito[id], 0).toFixed(2);
  if (total == 0) {
    Swal.fire('Carrito vacío', 'Agrega productos al carrito antes de generar el QR.', 'info');
    return;
  }
  let qrContainer = document.getElementById('qr-container');
  qrContainer.innerHTML = '';
  let qrData = `Pagar S/ ${total} a Chocopasteles`;
  new QRCode(qrContainer, {
    text: qrData,
    width: 200,
    height: 200
  });
  Swal.fire({
    title: 'Escanea para pagar',
    html: qrContainer,
    showCloseButton: true,
    showCancelButton: true,
    focusConfirm: false,
    confirmButtonText: 'He pagado',
    cancelButtonText: 'Cerrar'
  }).then((result) => {
    if (result.isConfirmed) {
      carrito = {};
      localStorage.removeItem('carritoChoco');
      actualizarCarrito();
      document.getElementById('carrito-flotante').style.display = 'none';
      Swal.fire('Gracias por tu pago', 'Tu pedido será procesado pronto.', 'success');
    }
  });
};
</script>
</body>
</html>