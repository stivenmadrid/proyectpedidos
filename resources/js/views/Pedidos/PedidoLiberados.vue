<template>
  <div class="container" >
    <h2 class="title">Pedidos Liberados</h2>

    <div class="input-group mb-3 search-container">
      <input type="text" class="form-control" placeholder="Buscar por número de pedido" v-model="searchQuery">
      <div class="input-group-append">
        <button class="btn " @click="filterOrders">Buscar</button>
      </div>
    </div>

    <div v-if="filteredPedidos.length > 0" class="timeline">
      <div v-for="pedido in filteredPedidos" :key="pedido.id" class="timeline-item">
        <div class="card">
          <div class="card-header">
            <h5 class="card-title">Pedido #{{ pedido.id }}</h5>
            <p class="card-text">MESA: {{ pedido.mesa ? pedido.mesa.nombre : "-" }}</p>
            <p class="card-text">ESTADO PEDIDO: {{ pedido.estado }}</p>
            <p class="card-text">TOTAL PAGAR: {{ pedido.total | formatCurrency }}</p>
            <p class="card-text">FECHA PEDIDO: {{ formatDate(pedido.created_at) }}</p>
          </div>
          <div class="card-body">
            <h6 class="card-subtitle mb-2">PRODUCTOS:</h6>
            <ul class="list-group">
              <li v-for="producto in pedido.productos" :key="producto.id" class="list-group-item custom-list-item">
                {{ producto.nombre }} <span class="badge badge-secondary">{{ producto.pivot.cantidad }}</span>
              </li>
            </ul>
          </div>
          <div class="card-footer custom-footer">
            <button class="btn btn-primary" @click="generarPago(pedido.id)">
              Generar pago
            </button>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="empty-message">
      No hay pedidos disponibles para generar pago.
    </div>
  </div>
</template>

<script>
import axios from "axios";
import Swal from "sweetalert2";

export default {
  data() {
    return {
      pedidos: [],
      searchQuery: "",
      filteredPedidos: [], // Agregar la propiedad para almacenar los pedidos filtrados
    };
  },
  mounted() {
    this.getPedidos();
  },
  methods: {
    getPedidos() {
      const token = localStorage.getItem("token");

      axios
        .get("/api/v1/pedidos-liberados", {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          this.pedidos = response.data;
          this.filteredPedidos = response.data; // Inicialmente, mostrar todos los pedidos sin filtrar
          this.initDataTable();
        })
        .catch((error) => {
          console.error(error);
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "Ocurrió un error al obtener los pedidos.",
          });
        });
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString();
    },
    generarPago(pedidoId) {
      const token = localStorage.getItem("token");
      const apiUrl = "/api/v1/generarpago";

      const data = {
        pedidoId: pedidoId,
        monto: 10000,
      };

      Swal.fire({
        title: "Confirmar pago",
        text: "¿Estás seguro de que deseas generar el pago?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Confirmar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          axios
            .post(apiUrl, data, {
              headers: {
                Authorization: `Bearer ${token}`,
              },
            })
            .then((response) => {
              const message = response.data.message;
              console.log("Pago generado:", message);
              Swal.fire({
                icon: "success",
                title: "Pago generado",
                text: "El pago se ha generado correctamente.",
              });
              // Actualizar la página
              window.location.reload();
            })
            .catch((error) => {
              console.error("Error al generar el pago:", error);
              let errorMessage = "Ocurrió un error al generar el pago.";
              if (
                error.response &&
                error.response.data &&
                error.response.data.error
              ) {
                errorMessage = error.response.data.error;
              } else if (
                error.response &&
                error.response.data &&
                error.response.data.message
              ) {
                errorMessage = error.response.data.message;
              }
              Swal.fire({
                icon: "error",
                title: "Error",
                text: errorMessage,
              });
            });
        }
      });
    },
    initDataTable() {
      // ... Código anterior ...
    },
    filterOrders() {
      if (!this.searchQuery) {
        // Si el campo de búsqueda está vacío, mostrar todos los pedidos
        this.filteredPedidos = this.pedidos;
      } else {
        // Filtrar los pedidos basados en la búsqueda por número de pedido
        this.filteredPedidos = this.pedidos.filter((pedido) => {
          return pedido.id.toString().includes(this.searchQuery);
        });
      }
    },
  },
  computed: {
    // ... Código anterior ...
  },
  filters: {
    // ... Código anterior ...
  },
};
</script>



  <style scoped>
 
  .search-container {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
}

.input-group {
  width: 60%;
}
.title {
  font-size: 30px;
  font-weight: bold;
  text-align: center;
  color: #222020;
  border-radius: 30px;
  margin-bottom: 20px;
  animation: slideInUp 1s ease;
  -webkit-text-stroke: 2px rgb(12, 12, 12);
}
  .timeline {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: flex-start;
}

.timeline-item {
  flex: 0 0 calc(33.3333% - 40px);
  max-width: calc(33.3333% - 40px);
  margin: 10px;
}

.timeline-item .card {
  border: 4px solid rgba(63, 81, 181, 0.91);
}

/* Custom styles for the card header, body, and footer */
.card-header {
  background-color: #007bffe3;
  color: white;
  padding: 10px;
  text-align: center;
}

.card-body {
  padding: 10px;
}

.card-footer {
  text-align: center;
  background-color: #f8f9fa;
  padding: 10px;
}

.custom-list-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* Add media query for better responsiveness */
@media (max-width: 992px) {
  .timeline-item {
    flex: 0 0 calc(50% - 40px);
    max-width: calc(50% - 40px);
  }
}
  /* Estilos para una mejor responsividad */
  @media (max-width: 992px) {
    .timeline-item {
      /* ... Estilos para el timeline-item en pantallas medianas ... */
    }
  }

  @media (max-width: 768px) {
    .timeline-item {
      /* ... Estilos para el timeline-item en pantallas pequeñas ... */
    }
  }
@media (max-width: 768px) {
  .timeline-item {
    flex: 0 0 100%;
    max-width: 100%;
  }
}
  </style> 
