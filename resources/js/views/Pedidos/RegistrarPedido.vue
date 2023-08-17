<template>
  <div class="container-fluid" style="background-image: url('https://www.arcosdorados.com/wp-content/uploads/2021/07/Grand-Tasty-1-e1627506211463.jpg'); background-size: cover; background-repeat: no-repeat;">
     
        <br>
        <div class="row">

            <!-- Lado izquierdo: Mesas -->
            <div class="col-md-12 mb-1">
                <div class="row">
                    <br>
                    <div class="col-md-6 mb-1">
                        <div class="card text-center">
                            <button class="btn btn-primary btn-sm" @click="abrirModalSinMesa">Crear Pedido sin
                                mesa</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-1">
                        <div class="card text-center">
            <button @click="abrirModalAdiciones" class="btn btn-primary">Adiciones</button>
            </div>
            </div>
        </div>
          
          
   
            <div class="row">
                <div class="col-md-2 col-6  mb-1 mesa-item" v-for="mesa in mesas" :key="mesa.id"
                    :class="{'mesa-ocupada': mesa.Estado === 'Ocupada', 'mesa-disponible': mesa.Estado === 'Libre'}">
                    <div class="card mb-8">
                        <div class="card-body">
                            <h5 class="card-title">Mesa {{ mesa.id }}</h5>
                            <p class="card-text">Descripción: {{ mesa.Descripcion }}</p>
                            <button class="btn btn-primary" @click="abrirModalConMesa(mesa.id)">Seleccionar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>



<!-- 
        Modal para adiciones -->
        <div v-if="showModalAdiciones" class="modal-backgroundn" @click="cerrarModalAdiciones">
    <div class="modal-contentn" @click.stop>
      <div class="modal-header">
        <h5 class="modal-title" id="adicionesModalLabel">Agregar Adición</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       
        <!-- Aquí se muestra una tabla con las adiciones -->
        <table  class="table table-bordered">
          <thead>
            <tr>
            <th>Categoria</th>
              <th>Producto</th>
              <th>Cantidad</th>
              <th>Cocina</th>
              <th>Observación</th>
              <th>Eliminar</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(adicion, index) in nuevoPedidoConMesa.productos" :key="index">

                <td>
                    <select >
                        
                    </select>
                </td>
                <td>
                <select >
                  <option v-for="producto in listaProductos" :key="producto.id" :value="producto.id">{{ producto.nombre }}</option>
                </select>
              </td>
              <td>
                <select v-model="adicion.producto_id"  class="form-control">
                  <option v-for="producto in listaProductos" :key="producto.id" :value="producto.id">{{ producto.nombre }}</option>
                </select>
              </td>
              <td>
                <input type="number"  class="form-control" v-model="adicion.cantidad" />
              </td>
              <td>
                <input type="checkbox"  v-model="adicion.cocina" />
              </td>
              <td>
                <input type="text"  class="form-control" v-model="adicion.observacion" />
              </td>
              <td>
              <!-- Ícono de papelera para eliminar la fila -->
              <i class="fas fa-trash" @click="eliminarFilaAdicion(index)"></i>
            </td>
            </tr>
          </tbody>
        </table>
        <div class="agregar-fila-icon" @click="agregarFilaAdicion">
          <i class="fas fa-plus-circle"></i>
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" @click="cerrarModalAdiciones">Cerrar</button>
        <!-- Llama a la función agregarAdicion al hacer clic en el botón "Guardar Adición" -->
        <button type="button" class="btn btn-primary" @click="agregarAdicion">Guardar Adición</button>
      </div>
    </div>
  </div>

</div>





    <!-- Modal para ingresar datos del pedido con mesa -->
    <div v-if="showModalConMesa" class="modal-backgroundn" @click="cerrarModalConMesa">
      <div class="modal-contentn" @click.stop>
        <h3 class="modal-title">Agregar Pedido con Mesa</h3>
        <form @submit.prevent="crearPedidoConMesa" class="form">
          <div class="field">
            <!-- Mostrar los detalles de la mesa seleccionada -->
            <p>Mesa seleccionada: {{ mesaSeleccionada.nombre }}</p>
          </div>

          <!-- Lista de productos para seleccionar y agregar al pedido con mesa -->
          <div class="field">
            <label class="label">Productos:</label>
            <div class="control">
              <div class="table-container">
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <!-- Encabezados de la tabla -->
                        <th>Categoria</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Cocina</th>
                        <th>Observaciones</th>
                        <th>Acciones</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Filas para agregar productos -->
                      <tr v-for="(producto, index) in nuevoPedidoConMesa.productos" :key="index">
                        <td>
                          <select v-model="producto.selectedCategoryId" @change="fetchProductsByCategory(producto)" required class="form-control">
                            <!-- Opciones para seleccionar la categoría del producto -->
                            <option value="" disabled selected>Seleccione la categoria</option>
                            <option v-for="category in categories" :value="category.id" :key="category.id">{{ category.Nombre_Categoria }}</option>
                          </select>
                        </td>
                        <td>
                          <select v-model="producto.id" @change="actualizarTotalConMesa" required class="form-control">
                            <!-- Opciones para seleccionar el producto -->
                            <option value="" disabled selected>Seleccione un producto</option>
                            <option v-for="product in producto.categoryProducts" :value="product.id" :key="product.id">{{ product.nombre }}</option>
                          </select>
                        </td>
                        <td>
                          <input v-model="producto.cantidad" class="form-control" type="number" placeholder="Cantidad" @input="actualizarTotalConMesa" required />
                        </td>
                        <td>
                          <input type="checkbox" v-model="producto.cocina" />
                        </td>
                        <td>
                          <input v-model="producto.observaciones" class="form-control" type="text" placeholder="Observaciones" />
                        </td>
                        <td>
                          <a class="icon-link" @click="eliminarProductoConMesa(index)">
                            <i class="fas fa-trash-alt"></i>
                          </a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <!-- Botón para agregar nuevo producto -->
          <div class="field">
            <div class="control">
              <a class="btn btn-primary btn-sm" @click="agregarProductoConMesa">
                <i class="fas fa-plus"></i>
              </a>
            </div>
          </div>

          <!-- Campo para mostrar el total a pagar -->
          <div class="field">
            <label class="label">Total a pagar:</label>
            <div class="control">
              <input v-model="nuevoPedidoConMesa.total" class="form-control" type="number" disabled />
            </div>
          </div>

          <!-- Botones para enviar el formulario o cancelar -->
          <div class="field">
            <div class="control">
              <button type="submit" class="btn btn-secondary">Crear Pedido con Mesa</button>
              <button type="button" class="btn btn-secondary" @click="cerrarModalConMesa">Cancelar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  
    <!-- Modal para pedidos sin mesa -->
    <div v-if="showModalSinMesa" class="modal-backgroundn" @click="cerrarModalSinMesa">
        <div class="modal-contentn" @click.stop>
            <h3 class="modal-title">Pedido sin Mesa</h3>
            <form @submit.prevent="crearPedidoSinMesa">

                <input v-model="nuevoPedidoSinMesa.cliente" class="form-control" type="text"
                    placeholder="Nombre del cliente" required />

                <div class="field">
                    <label class="label">Productos:</label>
                    <div class="control">
                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Categoria</th>
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Observaciones</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(producto, index) in nuevoPedidoSinMesa.productos" :key="index">

                                            <td>
                                                <select v-model="producto.selectedCategoryId"
                                                    @change="fetchProductsByCategory(producto)" required
                                                    class="form-control">
                                                    <option value="" disabled selected>Seleccione la categoria
                                                    </option>
                                                    <option v-for="category in categories" :value="category.id"
                                                        :key="category.id">
                                                        {{ category.Nombre_Categoria }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <select v-model="producto.id" @change="actualizarTotalConMesa" required
                                                    class="form-control">
                                                    <option value="" disabled selected>Seleccione un producto
                                                    </option>
                                                    <option v-for="product in producto.categoryProducts"
                                                        :value="product.id" :key="product.id">
                                                        {{ product.nombre }}
                                                    </option>
                                                </select>
                                            </td>
                                            <td>
                                                <input v-model="producto.cantidad" class="form-control" type="number"
                                                    placeholder="Cantidad" @input="actualizarTotalSinMesa" required />
                                            </td>
                                            <td>
                                                <input v-model="producto.observaciones" class="form-control" type="text"
                                                    placeholder="Observaciones" />
                                            </td>
                                            <td>
                                                <a class="icon-link" @click="eliminarProductoSinMesa(index)">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="field">
                    <div class="control">
                        <a class="btn btn-primary btn-sm" @click="agregarProductoSinMesa">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
                <label class="label">Total a pagar:</label>
                <div class="control">
                    <input v-model="nuevoPedidoSinMesa.total" class="form-control" type="number" disabled />
                </div>

                <br>

                <div class="control">
                    <div class="field button-container">
                        <button type="submit" class="btn btn-secondary">Crear Pedido</button>
                        <button type="button" class="btn btn-secondary" @click="cerrarModalSinMesa">Cancelar</button>

                    </div>

                </div>


            </form>
        </div>
    </div>


</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      mesas: [],
      productos: [],
      selectedCategoryId: null,
      categories: [],
      categoryProducts: [],
      imagenUrl: 'https://img.freepik.com/fotos-premium/mesa-madera-vacia-fondo-interior-borroso-cafeteria-o-restaurante-puede-utilizar-exhibicion-producto_7188-1500.jpg',
      showModalSinMesa: false,
      showModalConMesa: false,
      showModalAdiciones: false,
      nuevoPedidoSinMesa: {
        cliente: '',
        estado: 'Nuevo pedido sin mesa',
        total: 0,
        productos: [],
      },
      nuevoPedidoConMesa: {
        mesa_id: null,
        estado: 'Nuevo pedido',
        total: 0,
        productos: [],
      },
      mesaSeleccionada: {},
      cantidadAdicion: null,
      cocinaAdicion: false,
      observacionAdicion: '',
      productoSeleccionado: null,
    };
  },
  mounted() {
    this.getMesas();
    this.getProductos();
  },
  methods: {
    getMesas() {
      const token = localStorage.getItem('token');
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

      axios
        .get('/api/v1/Mesas')
        .then((response) => {
          this.mesas = response.data.map((mesa) => ({
            id: mesa.id,
            nombre: mesa.nombre,
            capacidad: mesa.capacidad,
            Estado: mesa.Estado,
            Descripcion: mesa.Descripcion,
            imagenUrl: this.imagenUrl,
          }));
        })
        .catch((error) => {
          console.error(error);
        });
    },
    getProductos() {
      axios
        .get('/api/v1/productos')
        .then((response) => {
          this.productos = response.data;
          const uniqueCategories = [...new Set(this.productos.map((producto) => producto.categoria_id))];
          axios.get('/api/v1/categorias')
            .then((response) => {
              this.categories = response.data.filter((categoria) => uniqueCategories.includes(
                categoria.id));
            })
            .catch((error) => {
              console.error(error);
            });
        })
        .catch((error) => {
          console.error(error);
        });
    },
    abrirModalSinMesa() {
      this.resetNuevoPedidoSinMesa();
      this.showModalSinMesa = true;
    },
    abrirModalAdiciones() {
      this.showModalAdiciones = true;
    },
    cerrarModalAdiciones() {
      this.showModalAdiciones = false;
    },
    cerrarModalSinMesa() {
      this.showModalSinMesa = false;
      this.resetNuevoPedidoSinMesa();
    },
    resetNuevoPedidoSinMesa() {
      this.nuevoPedidoSinMesa.cliente = '';
      this.nuevoPedidoSinMesa.total = 0;
      this.nuevoPedidoSinMesa.productos = [];
    },
    agregarProductoSinMesa() {
      this.nuevoPedidoSinMesa.productos.push({
        id: null,
        cantidad: null,
        observaciones: '',
      });
    },
    eliminarProductoSinMesa(index) {
      this.nuevoPedidoSinMesa.productos.splice(index, 1);
      this.actualizarTotalSinMesa();
    },
    actualizarTotalSinMesa() {
      let total = 0;
      for (const producto of this.nuevoPedidoSinMesa.productos) {
        const productoSeleccionado = this.productos.find((p) => p.id === producto.id);
        if (productoSeleccionado) {
          total += productoSeleccionado.precio * producto.cantidad;
        }
      }
      this.nuevoPedidoSinMesa.total = total;
    },
    crearPedidoSinMesa() {
      const token = localStorage.getItem('token');
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

      axios
        .post('/api/v1/pedidos-cocina/store', this.nuevoPedidoSinMesa)
        .then((response) => {
          Swal.fire({
            title: 'Éxito!',
            text: 'Pedido sin mesa creado exitosamente',
            icon: 'success',
            timer: 800,
            didClose: () => {
              location.reload()
            }
          });
          this.cerrarModalSinMesa();
        })
        .catch((error) => {
          let errorMessage = 'Error al realizar la solicitud al servidor';
          if (error.response) {
            errorMessage = error.response.data.message || 'Error al crear el pedido sin mesa';
          } else if (error.request) {
            errorMessage = 'No se recibió respuesta del servidor';




          }
          Swal.fire({
            title: 'Error!',
            text: errorMessage,
            icon: 'error',
          });
          console.error('Error al crear el pedido sin mesa:', error);
        });
    },
    abrirModalConMesa(mesaId) {
      this.resetNuevoPedidoConMesa();
      this.mesaSeleccionada = this.mesas.find((mesa) => mesa.id === mesaId);
      this.nuevoPedidoConMesa.mesa_id = mesaId;
      this.showModalConMesa = true;
    },
    cerrarModalConMesa() {
      this.showModalConMesa = false;
      this.resetNuevoPedidoConMesa();
    },
    resetNuevoPedidoConMesa() {
      this.nuevoPedidoConMesa.mesa_id = null;
      this.nuevoPedidoConMesa.total = 0;
      this.nuevoPedidoConMesa.productos = [];
    },
    agregarProductoConMesa() {
      this.nuevoPedidoConMesa.productos.push({
        selectedCategoryId: null,
        id: null,
        cantidad: null,
        observaciones: '',
        cocina: false, // Establecer el valor inicial a false para que siempre sea booleano
      });
    },
    eliminarProductoConMesa(index) {
      this.nuevoPedidoConMesa.productos.splice(index, 1);
      this.actualizarTotalConMesa();
    },
    eliminarFilaAdicion(index) {
    this.nuevoPedidoConMesa.productos.splice(index, 1);
  },
    actualizarTotalConMesa() {
        let total = 0;
  for (const producto of this.nuevoPedidoConMesa.productos) {
    const productoSeleccionado = this.productos.find((p) => p.id === producto.id);
    if (productoSeleccionado) {
      total += productoSeleccionado.precio * producto.cantidad;
    }
  }
  this.nuevoPedidoConMesa.total = total;
    },
    crearPedidoConMesa() {
  // Realizar la solicitud al backend incluyendo el token en los headers
  const token = localStorage.getItem('token');
  axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

  axios
    .post('/api/v1/pedidos-cocina/store', this.nuevoPedidoConMesa) // Eliminamos el filtrado de productos
    .then((response) => {
      Swal.fire({
        title: 'Éxito!',
        text: 'Pedido con mesa creado exitosamente',
        icon: 'success',
        timer: 800,
      });
      this.cerrarModalConMesa();
      window.location.reload();
    })
    .catch((error) => {
      let errorMessage = 'Error al realizar la solicitud al servidor';
      if (error.response) {
        errorMessage = error.response.data.message || 'Error al crear el pedido con mesa';
      } else if (error.request) {
        errorMessage = 'No se recibió respuesta del servidor';
      }
      Swal.fire({
        title: 'Error!',
        text: errorMessage,
        icon: 'error',
      });
      console.error('Error al crear el pedido con mesa:', error);
    });
},








    fetchProductsByCategory(producto) {
      const selectedCategoryId = producto.selectedCategoryId;
      if (selectedCategoryId) {
        const apiUrl = `/api/v1/productos/categoria/${selectedCategoryId}`;
        axios
          .get(apiUrl)
          .then((response) => {
            producto.categoryProducts = response.data;
          })
          .catch((error) => {
            console.error('Error fetching products:', error);
          });
      } else {
        producto.categoryProducts = [];
      }
    },

    agregarAdicion() {
      const nuevaAdicion = {
        producto_id: this.productoSeleccionado,
        cantidad: this.cantidadAdicion,
        cocina: this.cocinaAdicion,
        observacion: this.observacionAdicion,
      };
      this.nuevoPedidoConMesa.productos.push(nuevaAdicion);
      this.productoSeleccionado = null;
      this.cantidadAdicion = null;
      this.cocinaAdicion = false;
      this.observacionAdicion = '';
      $('#adicionesModal').modal('hide');
    },

    agregarFilaAdicion() {
      // Agregar una nueva fila vacía a la lista de productos en el modal de adiciones
      this.nuevoPedidoConMesa.productos.push({
        selectedCategoryId: null,
        id: null,
        cantidad: null,
        observaciones: '',
      });
    },
  },
};
</script>

<style>
/* Estilos generales */

/* Estilos del contenedor de los botones */
.button-container {
    display: flex;
    gap: 10px;
    justify-content: center;
    /* Alineamos los botones en el centro */
    margin-top: 20px;
    /* Agregamos un margen superior para separar los botones del contenido anterior */
}

/* Ajustamos el tamaño de los botones */

/* .container-fluid {
    align-items: center;
    min-height: 100vh;
    background: linear-gradient(to bottom right, #f4eab1, #12b992);
    padding: 20px;
    box-sizing: border-box;
    overflow: hidden;
} */

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

/* Estilo para las tarjetas de mesas ocupadas */
.mesa-ocupada .card {
    background-color: #f84909d4;
    /* Color coral suave */
    color: #fff;
    /* Texto blanco */
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
}

.mesa-ocupada .card:hover {
    background-color: #ff6347;
    /* Color coral más intenso al pasar el cursor */
    box-shadow: 0 0 10px rgba(243, 6, 6, 0.3);
}

/* Estilo para las tarjetas de mesas disponibles */
.mesa-disponible .card {
    background-color: #2bd088;
    /* Un tono suave de verde */
    color: white;
}

/* Efecto al pasar el cursor sobre las tarjetas */
.mesa-disponible .card:hover {
    background-color: #014421bd;
    /* Color coral más intenso al pasar el cursor */
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.907);
}

/* Media Query para dispositivos móviles */
@media (max-width: 768px) {
    .mesa-item {
        flex: 0 0 100%;
        /* Ocupar el ancho completo en dispositivos pequeños */
        flex: 0 0 calc(33.33% - 10px);
    }
}




/* Media Query para dispositivos móviles */
@media (max-width: 768px) {
    .mesas-list {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        /* Alinear las tarjetas una al lado de la otra */
    }




}

.producto-item {
    flex: 0 0 calc(50% - 10px);
    height: 100px;
    font-size: 14px;
}

.total-section {
    font-size: 16px;
}

.mesas-list {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    justify-content: space-between;
    /* Alinear las tarjetas una al lado de la otra */
}

.card {
    font-size: 10px;
    margin-bottom: 10px;
    padding: 5px;
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-4px);
    box-shadow: 0px 8px 24px rgba(0, 0, 0, 0.2);
}

.card-title {
    font-size: 1.6em;
    font-weight: bold;
    color: #333;
    margin-bottom: 1em;
}

.btn-icon {
    padding: 8px;
}

.btn-icon i {
    font-size: 18px;
}

.btn-danger {
    border: none;
}

.btn-primary,
.btn-secondary {
    display: inline-block;

}

/* .btn-primary {
background-color: #0051ffc5;
}

.btn-secondary {
background-color: #537895;
} */

.custom-table {
    font-size: 14px;
    /* Tamaño de fuente para el contenido de la tabla */
    border-collapse: collapse;
    /* Combina los bordes de las celdas para una apariencia más limpia */
    width: 100%;
    /* Ancho de la tabla al 100% del contenedor */
}

.custom-table th,
.custom-table td {
    padding: 20px;
    /* Espaciado interno de las celdas */
    text-align: center;
    /* Alineación del texto en el centro de las celdas */
    border: 1px solid #ccc;
    /* Agrega bordes a las celdas para una apariencia definida */
}

.custom-table th {
    background-color: #f2f2f2;
    /* Color de fondo para la fila de encabezados */
    font-weight: bold;
    /* Texto en negrita para los encabezados */
}

.btn-primary:hover,
.btn-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}

/* Estilos para el modal */
.modal-backgroundn {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(25, 25, 25, 0.299);
    display: flex;
    justify-content: center;
    align-items: center;
}

.modal-contentn {
    max-width: 600px;
    background-color: #fff;
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
    overflow-y: auto;
    max-height: 70vh;
}

.modal-title {
    font-size: 1.8em;
    color: #537895;
    margin-bottom: 1em;
    text-align: center;
    font-weight: 600;
    padding: 1em 0;
    border-bottom: 1px solid #ccc;
}

.modal-title {
    font-size: 1.6em;
    /* Tamaño de fuente más pequeño para el título del modal */
}


.form .field label {
    font-size: 1em;
    /* Tamaño de fuente más pequeño para las etiquetas del formulario */
}

.form .field .table th,
.form .field .table td {
    padding: 0.3em;
    /* Espaciado interno más pequeño en el formulario */
}

.form .field .table td select,
.form .field .table td input {
    font-size: 14px;
    /* Tamaño de fuente más grande para los inputs y select del formulario */
}

.form .field .btn-primary,
.form .field .btn-secondary {
    font-size: 1em;
    /* Tamaño de fuente más pequeño para los botones del formulario */
}


@keyframes fadeIn {
    from {
        background-color: rgba(0, 0, 0, 0);
    }

    to {
        background-color: rgba(0, 0, 0, 0.8);
    }
}

@keyframes scaleIn {
    from {
        transform: scale(0.8);
        opacity: 0;
    }

    to {
        transform: scale(1);
        opacity: 1;
    }
}
</style>