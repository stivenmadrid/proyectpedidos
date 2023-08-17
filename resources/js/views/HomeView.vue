<template>
  <div class="home" >
    <div class="content">
      <h1 class="titles">¡Bienvenido al Sistema de Pedidos!</h1>
      <!-- <p>
        Aquí podrás administrar las mesas, realizar pedidos y gestionar tu restaurante de manera
        eficiente.
      </p> -->
    </div>
    <div class="image-container">
      <div class="overlay"></div>
      <img src="https://i.ytimg.com/vi/jUkW0_4SDw8/maxresdefault.jpg" alt="Restaurant Image" />
    </div>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <p>Todos los derechos reservados © 2023 Tecnobyte360°. <a href="https://www.tecnobyte360.com">Visita nuestra página web</a></p>
      </div>
    </footer>
  </div>
</template>

<script>
export default {
  // Puedes agregar lógica adicional si es necesario
}
</script>

<style>


/* Estilos para el contenido */
.container {
  max-width: 1200px; /* Ajusta el ancho del contenido para pantallas grandes */
  margin: 0 auto; /* Centra el contenido */
}
/* Ajustes para pantallas pequeñas */
@media (max-width: 767px) {
  .navbar-brand {
    font-size: 20px;
  }
  .navbar-nav .nav-link {
    font-size: 14px;
  }
}
.home {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 100vh; /* Asegura que ocupe al menos el 100% del viewport */
  background: linear-gradient(to bottom right, #ffdd00, #ff3366);
  padding: 20px;
  box-sizing: border-box;
  overflow: hidden; /* Evita el desplazamiento causado por elementos que exceden el contenido */
}

.content {
  text-align: center;
  max-width: 600px;
  margin-bottom: 40px;
}

.titles {
  color: white;
  font-size: 56px;
  font-weight: bold;
  margin-bottom: 20px;
  animation: slideInUp 1s ease;
}

.content p {
  font-size: 24px;
  color: #fff;
  margin-bottom: 30px;
  animation: slideInUp 1s ease;
}

.image-container {
  position: relative;
  max-width: 800px;
  width: 100%;
  overflow: hidden;
  border-radius: 8px;
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
  margin-bottom: 20px;
  animation: fadeIn 1s ease;
}

.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(to bottom right, rgba(22, 152, 24, 0.6), rgba(200, 94, 126, 0.9));
  opacity: 0;
  transition: opacity 0.3s ease;
}

.image-container:hover .overlay {
  opacity: 1;
}

.image-container img {
  width: 100%;
  height: auto;
  object-fit: cover;
  object-position: center;
}

@keyframes slideInUp {
  0% {
    transform: translateY(20px);
    opacity: 0;
  }
  100% {
    transform: translateY(0);
    opacity: 1;
  }
}

@keyframes fadeIn {
  0% {
    opacity: 0;
  }
  100% {
    opacity: 1;
  }
}

/* Estilos para el footer */
.footer {
  background-color: #333;
  color: white;
  padding: 20px;
  margin-top: 50px;
  border-top: 1px solid #666; /* Agrega un borde superior */
  box-shadow: 0px -2px 6px rgba(0, 0, 0, 0.2); /* Agrega una sombra en la parte inferior */
}

.footer a {
  color: #ff3366; /* Cambia el color del enlace */
  text-decoration: none; /* Quita la subrayado por defecto */
  transition: color 0.3s; /* Agrega una transición suave al color del enlace al pasar el mouse */
}

.footer a:hover {
  color: #ff6699; /* Cambia el color del enlace al pasar el mouse por encima */
}
</style>
