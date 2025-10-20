<?php
session_start();
require_once("config/database.php");
?>
<!DOCTYPE html>
<html lang="es">
<head>
      <meta charset="UTF-8">
    <title>contacto - chocopasteles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header>
  <?php include("includes/header.php"); ?>
</header>
<section class="ubicacion">
    <div class="ubicacion-container">
      <h2>QUIERES SABER...</h2>
        <h2>¿Dónde estamos?</h2>
        <p class="intro">¡Visítanos en nuestra tienda y vive la experiencia <b>Chocopasteles</b>!  
        Estamos ubicados en un lugar de fácil acceso, listos para endulzar tu día con nuestras creaciones artesanales.</p>

        <div class="horarios">
            <h2>🕒 Horarios de atención</h2>
            <ul>
                <li>Lunes a Viernes: <span>9:00 am - 10:00 pm</span></li>
                <li>Sábados: <span>9:00 am - 10:00 pm</span></li>
                <li>Domingos: <span>9:00 am - 8:00 pm</span></li>
            </ul>
        </div>
        <div class="direccion">
            <h3>📍 Nuestra ubicación</h3>
            <p>Dirección: <b>Warnes, Satélite Norte, Cancha sintética (lado izquierdo a 3 casetas)</b></p>
            <div style="max-width: 600px; margin: 0 auto;">
        <!-- Reemplaza el src con el enlace de tu ubicación real en Google Maps -->
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d329.5585249856813!2d-63.14644952474305!3d-17.605374888876568!2m3!1f353.4169579836255!2f0!3f0!3m2!1i1024!2i768!4f35!5e1!3m2!1ses!2sbo!4v1759072677049!5m2!1ses!2sbo" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        
        <blockquote>
          <h2>
  "✨Cada visita es un momento dulce para recordar. ¡Ven y disfruta con nosotros!✨"
</h2>
</blockquote>
</section>

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
</div>
<button id="carrito-toggle">🛒</button>

<script src="assets/js/main.js"></script>

<script>
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
document.querySelectorAll('.agregar-carrito').forEach(btn => {
  btn.onclick = () => {
    let id = btn.dataset.id;
    carrito[id] = (carrito[id] || 0) + 1;
    localStorage.setItem('carritoChoco', JSON.stringify(carrito));
    actualizarCarrito();
    document.getElementById('carrito-flotante').style.display = 'block';
  };
});
document.getElementById('carrito-toggle').onclick = () => {
  document.getElementById('carrito-flotante').style.display = 'block';
};
document.getElementById('carrito-cerrar').onclick = () => {
  document.getElementById('carrito-flotante').style.display = 'none';
};
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
actualizarCarrito();
</script>
 <?php include __DIR__ . "/includes/adminModal.php"; ?>
</body>
</html>