

<template>
  <div class="container-fluid" >
      <div class="row">
          <div class="col-sm-12" id="tabla-container">
              <div class="container mt-4">
                  <h1>Maestro de mesas</h1>
                    <button @click="abrirModal" class="btn btn-primary">Registrar Mesa</button>
                   <!-- Campo de búsqueda -->
          
                <div class="row justify-content-center mb-3">
                      <div class="col-md-6">
                        <div class="form-group">
                          <input v-model="busqueda" @input="filtrarMesas" type="text" class="form-control" placeholder="Buscar mesa">
                        </div>
                      </div>
                    </div>
              </div>
  
              <div class="table-responsive">
                  <table class="table " id="userTable">
                      <thead>
                        <tr class="table-dark">
                          <th scope="col">#</th>
                          <th scope="col">Nombre de la mesa</th>
                          <th scope="col">Capacidad</th>
                          <th scope="col">Estado</th>
                          <th scope="col">Descripción</th>
                          <th scope="col">Acciones</th>
                        </tr>
                      </thead>
                    
                      <tbody>
              <tr v-for="mesa in mesas" :key="mesa.id">
                <td>{{ mesa.id }}</td>
                <td>{{ mesa.nombre }}</td>
                <td>{{ mesa.capacidad }}</td>
                <td>{{ mesa.Estado }}</td>
                <td>{{ mesa.Descripcion }}</td>
                <td>
                  <button @click="abrirModalEdicion(mesa.id)" class="btn btn-primary">
                    <i class="fas fa-edit"></i> <!-- Icono de editar de Font Awesome -->
                  </button>
                  <button @click="eliminarMesa(mesa.id)" class="btn btn-danger">
                    <i class="fas fa-trash-alt"></i> <!-- Icono de eliminar de Font Awesome -->
                  </button>
                </td>
              </tr>
            </tbody>
                  </table>
              </div>
          </div>
      </div>
  </div>
    <!-- Modal para agregar mesa -->
  <div class="modal" :class="{ 'is-active': mostrarModal }">
    <div class="modal-background"></div>
    <div class="modal-content">
      <div class="box">
        <h3 class="">Agregar Mesa</h3>
        <form @submit.prevent="crearMesa">
          <div class="field">
            <label class="label">Nombre</label>
            <div class="control">
              <input v-model="nuevaMesa.nombre" class="form-control" type="text" required />
            </div>
          </div>
          <div class="field">
            <label class="label">Capacidad</label>
            <div class="control">
              <input v-model="nuevaMesa.capacidad" class="form-control" type="number" required />
            </div>
          </div>
          <div class="field">
            <label class="label">Estado</label>
            <div class="control">
              <select v-model="nuevaMesa.Estado" class="form-control" required>
                <option value="Libre">Libre</option>
                <option value="Ocupada">Ocupada</option>
              </select>
            </div>
          </div>
          <div class="field">
            <label class="label">Descripción</label>
            <div class="control">
              <textarea v-model="nuevaMesa.Descripcion" class="form-control" required></textarea>
            </div>
          </div>
          <div class="field">
            <div class="control">
              <br />
              <button type="submit" class="btn btn-primary">Crear</button>
              <button type="button" class="btn btn-secondary" @click="cancelarCreacion">
                Cancelar
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal para editar mesa -->
  <transition name="fade">
    <div class="modal" :class="{ 'is-active': mostrarModalEdicion }">
    <div class="modal-background"></div>
    <div class="modal-content">
      <div class="box">
        <h3 class="">Editar Mesa</h3>
        <form @submit.prevent="editarMesa">
          <div class="field">
            <label class="label">Nombre</label>
            <div class="control">
              <input v-model="mesaSeleccionada.nombre" class="form-control" type="text" required />
            </div>
          </div>
          <div class="field">
            <label class="label">Capacidad</label>
            <div class="control">
              <input
                v-model="mesaSeleccionada.capacidad"
                class="form-control"
                type="number"
                required
              />
            </div>
          </div>
          <div class="field">
            <label class="label">Estado</label>
            <div class="control">
              <select v-model="mesaSeleccionada.Estado" class="form-control" required>
                <option value="Libre">Libre</option>
                <option value="Ocupada">Ocupada</option>
              </select>
            </div>
          </div>
          <div class="field">
            <label class="label">Descripción</label>
            <div class="control">
              <textarea
                v-model="mesaSeleccionada.Descripcion"
                class="form-control"
                required
              ></textarea>
            </div>
          </div>
          <div class="field">
            <div class="control">
              <br />
              <button type="submit" class="btn btn-primary">Guardar</button>
              <button type="button" class="btn btn-secondary" @click="cancelarEdicion">
                Cancelar
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  </transition>
</template>
  
<script>
import axios from 'axios';
import $ from 'jquery';
import 'datatables.net';
import 'datatables.net-dt/css/jquery.dataTables.css';
import 'datatables.net-responsive';
import Paginate from 'vuejs-paginate'; // Importar el componente paginate


export default {
  data() {
    return {
      mesas: [],
      mostrarModal: false,
      mostrarModalEdicion: false,
      nuevaMesa: {
        nombre: '',
        capacidad: '',
        Estado: '',
        Descripcion: '',
      },
      mesaSeleccionada: {
        id: '',
        nombre: '',
        capacidad: '',
        Estado: '',
        Descripcion: '',
      },
      dataTable: null,
      busqueda: '', // Nueva propiedad para el término de búsqueda
    };
  },
  mounted() {
    this.getMesas();
  },
  methods: {
    getMesas() {
      const token = localStorage.getItem('token');

      axios
        .get('/api/v1/Mesas', {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          this.mesas = response.data;
          this.initializeDataTable();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    abrirModal() {
      this.mostrarModal = true;
    },
    cancelarCreacion() {
      this.cerrarModal();
    },
    cerrarModal() {
      this.mostrarModal = false;
      this.resetForm();
    },
    resetForm() {
      this.nuevaMesa = {
        nombre: '',
        capacidad: '',
        Estado: '',
        Descripcion: '',
      };
    },
    crearMesa() {
      const token = localStorage.getItem('token');

      axios
        .post('api/v1/Mesas', this.nuevaMesa, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          this.$swal({
            title: 'Éxito!',
            text: response.data.message,
            icon: 'success',
          });
          this.getMesas();
          this.cerrarModal();
        })
        .catch((error) => {
          let errorMessage = 'Error al realizar la solicitud al servidor';
          if (error.response) {
            errorMessage = error.response.data.message || 'Error al crear la mesa';
          } else if (error.request) {
            errorMessage = 'No se recibió respuesta del servidor';
          }
          this.$swal({
            title: 'Error!',
            text: errorMessage,
            icon: 'error',
          });
          console.error('Error al crear la mesa:', error);
        });
    },
    eliminarMesa(id) {
      const token = localStorage.getItem('token');

      axios
        .delete(`/api/v1/Mesas/destroy/${id}`, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          const message = response.data.message;
          this.$swal({
            title: 'Éxito!',
            text: message,
            icon: 'success',
          });
          console.log('Respuesta del backend:', response.data);
          this.getMesas();
        })
        .catch((error) => {
          let errorMessage = 'Error al realizar la solicitud al servidor';
          if (error.response) {
            errorMessage = error.response.data.message || 'Error al eliminar la mesa';
          } else if (error.request) {
            errorMessage = 'No se recibió respuesta del servidor';
          }
          this.$swal({
            title: 'Error!',
            text: errorMessage,
            icon: 'error',
          });
          console.error('Error al eliminar la mesa:', error);
        });
    },
    abrirModalEdicion(id) {
      const mesa = this.mesas.find((m) => m.id === id);

      if (mesa) {
        this.mesaSeleccionada = {
          ...mesa,
        };
        this.mostrarModalEdicion = true;
      }
    },
    cancelarEdicion() {
      this.mostrarModalEdicion = false;
      this.resetForm();
    },
    editarMesa() {
      const token = localStorage.getItem('token');

      axios
        .put(`/api/v1/Mesas/update/${this.mesaSeleccionada.id}`, this.mesaSeleccionada, {
          headers: {
            Authorization: `Bearer ${token}`,
          },
        })
        .then((response) => {
          const message = response.data.message;
          this.$swal({
            title: 'Éxito!',
            text: message,
            icon: 'success',
          });
          console.log('Respuesta del backend:', response.data);
          this.getMesas();
          this.mostrarModalEdicion = false;
        })
        .catch((error) => {
          let errorMessage = 'Error al realizar la solicitud al servidor';
          if (error.response) {
            errorMessage = error.response.data.message || 'Error al editar la mesa';
          } else if (error.request) {
            errorMessage = 'No se recibió respuesta del servidor';
          }
          this.$swal({
            title: 'Error!',
            text: errorMessage,
            icon: 'error',
          });
          console.error('Error al editar la mesa:', error);
        });
    },

    // Función para inicializar el DataTable
    initializeDataTable() {
      this.dataTable = $(this.$el).find('table').DataTable({
        responsive: true,
      });
    },

    // Nueva función para filtrar las mesas según el término de búsqueda
    filtrarMesas() {
      const textoBusqueda = this.busqueda.toLowerCase();
      this.mesas = this.mesas.filter((mesa) => {
        return mesa.nombre.toLowerCase().includes(textoBusqueda);
      });
    },
    computed: {
    // Computed para paginar las mesas
    mesas() {
      const inicio = (this.dataTable.page() - 1) * this.elementosPorPagina;
      return this.mesas.slice(inicio, inicio + this.elementosPorPagina);
    },
  },
  },
};
</script>
  
  
  <style>
  .btn-estado {
      width: 150px;
      /* ajustar el ancho según sus necesidades */
  }
  
  /* Estilos para la sección del gráfico */
  #grafico-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100px;
      width: 100%;
      background-color: #f8f9fa;
      border: 1px solid #e9ecef;
  }
  
  /* Estilos para el contenedor de la tabla */
  #tabla-container {
      padding: 20px;
      background-color: #f8f9fa;
      border: 1px solid #e9ecef;
  }
  
  /* Estilos para la tabla */
  #userTable {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
  }
  
  #userTable th,
  #userTable td {
      border: 1px solid #ced4da;
      padding: 8px;
      text-align: center;
  }
  
  #userTable th {
      background-color: #007bff;
      color: #fff;
  }
  
  #userTable tr:hover {
      background-color: #f1f1f1;
  }
  .modal.is-active {
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
  }
  .modal-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
  }
  .modal-content {
    background-color: #fefefe;
    padding: 20px;
    max-width: 500px;
    margin: 0 auto;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    transition: transform 0.3s ease;
  }
  .modal-content:hover {
    transform: scale(1.02);
  }
  .modal-close {
    position: absolute;
    top: 10px;
    right: 10px;
    color: #aaa;
    font-size: 24px;
    cursor: pointer;
  }
  
  </style>
  