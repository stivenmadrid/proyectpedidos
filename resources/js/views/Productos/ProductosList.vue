

<template>
    <div class="container-fluid" >
        <div class="row">
            <div class="col-sm-12" id="tabla-container">
                <div class="container mt-4">
                    <h1>Productos</h1>
                    <button class="btn btn-success mr-2">
                        <i class="fas fa-file-import"></i> Importar productos
                      </button>
                      <button @click="abrirModal" class="btn btn-primary btn-block">
                        <i class="fas fa-plus-circle"></i> Crear Producto
                    </button>

                   
                   
                </div>
    
                <div class="table-responsive">
                    <table class="table " id="userTable">
                        <thead>
                            <tr>
                                <th scope="col">id#</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Descripción</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Categoría</th>
                                <th scrope="col">Stock</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <tr v-for="producto in productos" :key="producto.id">
                                <td>{{ producto.id }}</td>
                                <td>{{ producto.nombre }}</td>
                                <td>{{ producto.descripcion }}</td>
                                <td>{{ formatNumber(producto.precio) }}</td>
                                <td>{{ producto.categoria.Nombre_Categoria }}</td>
                                <td>FALTA</td>
                                <td>
                                <button class="btn btn-primary" @click="abrirModalEdicionConProducto(producto)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger" @click="eliminarProducto(producto.id)">
                                    <i class="fas fa-trash"></i>
                                </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
     <!-- Modal para agregar producto -->
  <div class="modal" :class="{ 'is-active': mostrarModal }">
      <div class="modal-background"></div>
      <div class="modal-content">
          <div class="box">
              <h3 class="title">Agregar producto</h3>
              <form @submit.prevent="crearProducto">
                  <div class="field">
                      <label class="label">Nombre</label>
                      <div class="control">
                          <input v-model="nuevoProducto.nombre" class="form-control" type="text" required />
                      </div>
                  </div>
                  <div class="field">
                      <label class="label">Descripción</label>
                      <div class="control">
                          <input v-model="nuevoProducto.descripcion" class="form-control" type="text" required />
                      </div>
                  </div>
                  <div class="field">
                      <label class="label">Precio</label>
                      <div class="control">
                          <input v-model="nuevoProducto.precio" class="form-control" type="number" required />
                      </div>
                  </div>
                  <div class="field">
                      <label class="label">Categoría</label>
                      <div class="control">
                          <select v-model="nuevoProducto.categoria_id" class="form-control" required>
                              <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                                  {{ categoria.Nombre_Categoria }}
                              </option>
                          </select>
                      </div>
                  </div>
                  <div class="fiel">
                      <label class="label">stock</label>
                      <input class="form-control" type="number" required />
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
    
  <!-- Modal para editar producto -->
  <div class="modal" :class="{ 'is-active': mostrarModalEdicion }">
      <div class="modal-background"></div>
      <div class="modal-content">
          <div class="box">
              <h3 class="title">Editar Producto</h3>
              <form @submit.prevent="editarProducto">
                  <div class="field">
                      <label class="label">Nombre</label>
                      <div class="control">
                          <input v-model="productoSeleccionado.nombre" class="form-control" type="text" required />
                      </div>
                  </div>
                  <div class="field">
                      <label class="label">Descripción</label>
                      <div class="control">
                          <input v-model="productoSeleccionado.descripcion" class="form-control" type="text"
                              required />
                      </div>
                  </div>
                  <div class="field">
                      <label class="label">Precio</label>
                      <div class="control">
                          <input v-model="productoSeleccionado.precio" class="form-control" type="number" required />
                      </div>
                  </div>
                  <div class="field">
                      <label class="label">Categoría</label>
                      <div class="control">
                          <select v-model="productoSeleccionado.categoria_id" class="form-control" required>
                              <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                                  {{ categoria.Nombre_Categoria }}
                              </option>
                          </select>
                      </div>
                  </div>
                  <div class="fiel">
                      <label class="label">stock</label>
                      <input class="form-control" type="number" required />
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
</template>

<script>
import axios from 'axios'
import $ from 'jquery'
import 'datatables.net'
import 'datatables.net-dt/css/jquery.dataTables.css'
import 'datatables.net-responsive-dt/css/responsive.dataTables.css'
export default {
  data() {
      return {
          productos: [],
          categorias: [],
          mostrarModal: false,
          mostrarModalEdicion: false,
          nuevoProducto: {
              nombre: '',
              descripcion: '',
              precio: '',
              categoria_id: ''
          },
          productoSeleccionado: {
              id: '',
              nombre: '',
              descripcion: '',
              precio: '',
              categoria_id: ''
          }
      }
  },
  mounted() {
      this.getProductos()
      this.getCategorias()
  },
  updated() {
      this.$nextTick(function() {
          if (!$.fn.dataTable.isDataTable(this.$refs.dataTable)) {
              $(this.$refs.dataTable).DataTable()
          }
      })
  },
  methods: {
      getProductos() {
          const token = localStorage.getItem('token')

          axios
              .get('/api/v1/productos', {
                  headers: {
                      Authorization: `Bearer ${token}`
                  }
              })
              .then((response) => {
                  this.productos = response.data
              })
              .catch((error) => {
                  console.error(error)
              })
      },
      getCategorias() {
          const token = localStorage.getItem('token')

          axios
              .get('/api/v1/categorias', {
                  headers: {
                      Authorization: `Bearer ${token}`
                  }
              })
              .then((response) => {
                  this.categorias = response.data
              })
              .catch((error) => {
                  console.log(error)
              })
      },
      abrirModal() {
          this.mostrarModal = true
      },
      cancelarCreacion() {
          this.cerrarModal()
      },
      cerrarModal() {
          this.mostrarModal = false
          this.resetForm()
      },
      resetForm() {
          this.nuevoProducto = {
              nombre: '',
              descripcion: '',
              precio: '',
              categoria_id: ''
          }
      },
      crearProducto() {
          const token = localStorage.getItem('token')

          axios
              .post('/api/v1/productos/store', this.nuevoProducto, {
                  headers: {
                      Authorization: `Bearer ${token}`
                  }
              })
              .then((response) => {
                  this.$swal({
                      title: 'Éxito!',
                      text: response.data.message,
                      icon: 'success'
                  })
                  this.getProductos()
                  this.cerrarModal()
              })
              .catch((error) => {
                  let errorMessage = 'Error al realizar la solicitud al servidor'
                  if (error.response) {
                      errorMessage = error.response.data.message || 'Error al crear el producto'
                  } else if (error.request) {
                      errorMessage = 'No se recibió respuesta del servidor'
                  }
                  this.$swal({
                      title: 'Error!',
                      text: errorMessage,
                      icon: 'error'
                  })
                  console.error('Error al crear el producto:', error)
              })
      },
      eliminarProducto(id) {
          const token = localStorage.getItem('token')

          axios
              .delete(`/api/v1/productos/destroy/${id}`, {
                  headers: {
                      Authorization: `Bearer ${token}`
                  }
              })
              .then((response) => {
                  this.$swal({
                      title: 'Éxito!',
                      text: response.data.message,
                      icon: 'success'
                  })
                  this.getProductos()
              })
              .catch((error) => {
                  let errorMessage = 'Error al realizar la solicitud al servidor'
                  if (error.response) {
                      errorMessage = error.response.data.message || 'Error al eliminar el producto'
                  } else if (error.request) {
                      errorMessage = 'No se recibió respuesta del servidor'
                  }
                  this.$swal({
                      title: 'Error!',
                      text: errorMessage,
                      icon: 'error'
                  })
                  console.error('Error al eliminar el producto:', error)
              })
      },
      abrirModalEdicionConProducto(producto) {
          this.productoSeleccionado = {
              ...producto
          }
          this.mostrarModalEdicion = true
      },
      cancelarEdicion() {
          this.mostrarModalEdicion = false
          this.resetForm()
      },
      editarProducto() {
          const token = localStorage.getItem('token')
          const url = `/api/v1/productos/update/${this.productoSeleccionado.id}`

          axios
              .put(url, this.productoSeleccionado, {
                  headers: {
                      Authorization: `Bearer ${token}`
                  }
              })
              .then((response) => {
                  this.$swal({
                      title: 'Éxito!',
                      text: response.data.message,
                      icon: 'success'
                  })
                  this.getProductos()
                  this.mostrarModalEdicion = false
              })
              .catch((error) => {
                  let errorMessage = 'Error al realizar la solicitud al servidor'
                  if (error.response) {
                      errorMessage = error.response.data.message || 'Error al editar el producto'
                  } else if (error.request) {
                      errorMessage = 'No se recibió respuesta del servidor'
                  }
                  this.$swal({
                      title: 'Error!',
                      text: errorMessage,
                      icon: 'error'
                  })
                  console.error('Error al editar el producto:', error)
              })
      },
      formatNumber(number) {
          return new Intl.NumberFormat().format(number)
      }
  }
}
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
    