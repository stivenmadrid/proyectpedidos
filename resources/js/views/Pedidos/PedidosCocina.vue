<template>
  <div class="container-fluid" style="background-image: url('https://www.arcosdorados.com/wp-content/uploads/2021/07/Grand-Tasty-1-e1627506211463.jpg'); background-size: cover; background-repeat: no-repeat;">
    <h1 class="titles">PEDIDOS EN LA COCINA</h1>

    <div v-if="cargando" class="loading-message">Cargando...</div>

    <div v-else-if="pedidos.length === 0" class="empty-message">No hay pedidos disponibles</div>

    <div v-else class="scrollable-container">
      <div class="row">
        <div
          class="col-lg-4 col-md-6"
          v-for="(pedido, index) in pedidos.slice().reverse()"
          :key="pedido.id"
        >
          <div :class="['card', {'new-order': pedido.estado === 'Nuevo pedido' && pedido.id === pedidoNuevoId}]">
            <div class="card-header">
              <h2 class="card-title">Pedido #{{ pedido.id }}</h2>
              <p class="card-subtitle">
                {{ pedido.mesa ? 'Mesa: ' + pedido.mesa.nombre : 'Pedido para llevar' }}
              </p>
            </div>

            <div class="card-body">
              <p v-if="!pedido.mesa" class="card-text">Cliente: {{ pedido.cliente }}</p>
              <p :class="{'status-new': pedido.estado === 'Nuevo pedido', 'status-completed': pedido.estado === 'Completado'}">Estado: {{ pedido.estado }}</p>

              <div v-if="pedido.productos">
                <h3>Productos:</h3>
                <ul>
                  <li v-for="producto in getProductosCocina(pedido)" :key="producto.id">
                    {{ producto.nombre }} (Cantidad: {{ producto.pivot.cantidad ?? 'N/A' }})
                    <br />
                    <template v-if="producto.pivot.observacion">
                      Observaciones: {{ producto.pivot.observacion }}
                    </template>
                  </li>
                </ul>
              </div>

              <!-- Agregamos el botón "Procesar" -->
              <button class="btn btn-primary" @click="liberarPedido(pedido.id)">
                <i class="fa-solid fa-edit"></i> Procesar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'
import sonidoNuevoPedido from '@/assets/notipedidos.mp3'
import Swal from 'sweetalert2'

export default {
  data() {
    return {
      cargando: true,
      pedidos: [],
      reproduciendoSonido: false,
      pedidoNuevoId: null // Variable para guardar el ID del pedido nuevo
    }
  },
  mounted() {
    this.cargarPedidos()
    this.escucharEventos()
  },
  methods: {
    cargarPedidos() {
      console.log('Iniciando la carga de pedidos...')
      axios
        .get('api/v1/pedidos-cocina', {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        })
        .then((response) => {
          console.log('Pedidos cargados:', response.data)
          this.pedidos = response.data
          this.cargando = false
        })
        .catch((error) => {
          console.error('Error cargando los pedidos:', error)
          this.cargando = false
        })
    },
    escucharEventos() {
      console.log('Iniciando escucha de eventos...')
      window.Echo.channel('pedidos').listen('NuevoPedidoEvent', (data) => {
        console.log('Evento NuevoPedidoEvent recibido:', data)
        this.pedidos.push(data.pedido)

        if (!this.reproduciendoSonido) {
          this.reproducirSonidoNuevoPedido()
        }

        this.pedidoNuevoId = data.pedido.id // Guardamos el ID del pedido nuevo

        // Quitamos la clase "new-order" después de 5 segundos para deshacer el efecto
        setTimeout(() => {
          this.pedidoNuevoId = null
        }, 8000)
      })
    },
    getProductosCocina(pedido) {
      return pedido.productos.filter(producto => producto.pivot.cocina)
    },
    reproducirSonidoNuevoPedido() {
      console.log('Intentando reproducir el sonido...')

      this.reproduciendoSonido = true

      const audio = new Audio(sonidoNuevoPedido)
      audio
        .play()
        .then(() => {
          console.log('Sonido reproducido exitosamente.')
          this.reproduciendoSonido = false
        })
        .catch((error) => {
          console.error('Error al reproducir el sonido:', error)
          this.reproduciendoSonido = false
        })
    },
    liberarPedido(pedidoId) {
      axios
        .put(
          `/api/v1/liberar-pedido/${pedidoId}`, {}, {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`
            }
          }
        )
        .then((response) => {
          const pedidoIndex = this.pedidos.findIndex((pedido) => pedido.id === pedidoId)
          if (pedidoIndex !== -1) {
            this.pedidos[pedidoIndex].estado = 'Liberado'
            const message = response.data.message
            Swal.fire({
              icon: 'success',
              title: 'Éxito',
              text: message,
              timer: 800,
              timerProgressBar: true,
              didClose: () => {
                location.reload()
              }
            })
          }
        })
        .catch((error) => {
          console.error('Error al liberar el pedido:', error)
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo liberar el pedido. Por favor, intenta nuevamente.'
          })
        })
    },
    formatCurrency(value) {
      const formatter = new Intl.NumberFormat('es-CO')
      return formatter.format(value)
    }
  }
}

</script>

  
  <style>
  /* Estilos personalizados para las tarjetas */
  .card {
    background-color: #f8f9fa;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
  }
  
  .card-header {
    padding: 16px;
    border-bottom: 1px solid #dee2e6;
  }
  
  .card-title {
    font-size: 24px;
    margin-bottom: 8px;
  }
  
  .card-subtitle {
    font-size: 14px;
    color: #6c757d;
    margin: 0;
  }
  
  .card-body {
    padding: 16px;
  }
  
  .card-text {
    margin-bottom: 8px;
  }
  
 
  .status-new {
    color: #007bff;
    font-weight: bold;
  }
  
  .status-completed {
    color: #28a745;
    font-weight: bold;
  }
  
  .btn {
    margin-top: 16px;
  }
  
  /* Personaliza el scroll del contenedor */
  .scrollable-container {
    overflow-y: auto;
    max-height: 80vh;
  }
  
  /* Estilos para el mensaje de carga y mensaje vacío */
  .loading-message,
  .empty-message {
    padding: 16px;
    text-align: center;
    font-size: 20px;
  }
  
  .loading-message {
    color: #007bff;
  }
  
  .empty-message {
    color: #6c757d;
  }
  
  /* Estilos para el título */
  .titles {
    font-size: 32px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 16px;
    color: white;
  }
  
  /* Estilos para resaltar el pedido nuevo */
  /* Estilos para resaltar el pedido nuevo */
.new-order {
  border: 2px solid #ffc107; /* Borde amarillo */
  box-shadow: 0 2px 4px rgba(255, 193, 7, 0.4); /* Sombra amarilla */
  animation: titileo 1s infinite; /* Aplica la animación "titileo" */
}
.empty-message{
  font-size: 10px;
    font-weight: bold;
    text-align: center;
    margin-bottom: 16px;
    color: white;
}

/* Animación de titileo */
@keyframes titileo {
  0%, 100% {
    transform: scale(1); /* Tamaño normal */
  }
  50% {
    transform: scale(1.1); /* Aumenta el tamaño */
    border-color: rgb(52, 47, 196); /* Cambia el color del borde a rojo */
    border-width: 4px; /* Aumenta el ancho del borde a 4px */
    box-shadow: 0 8px 9px rgba(34, 0, 255, 0.4); /* Cambia la sombra a roja */
  }
}
  </style>
  