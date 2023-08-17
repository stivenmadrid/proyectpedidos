<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12" id="tabla-container">
                <div class="container mt-4">
                    <h1>Categorias</h1>
                    <button @click="abrirModal" class="btn btn-primary">Agregar Categoría</button>
                    <!-- Campo de búsqueda -->
                    <div class="row justify-content-center mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input v-model="busqueda" @input="filtrarCategorias" type="text" class="form-control"
                                    placeholder="Buscar categoría">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table " id="userTable">
                        <thead>
                            <tr class="table-dark">
                                <th scope="col">#</th>
                                <th scope="col">Nombre de la Categoría</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="table-group-divider" id="contenidocategorias">
                            <tr v-for="categoria in categorias" :key="categoria.id">
                                <td>{{ categoria.id }}</td>
                                <td>{{ categoria.Nombre_Categoria }}</td>
                                <td>
                                    <button @click="abrirModalEdicion(categoria)" class="btn btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button @click="eliminarCategoria(categoria.id)" class="btn btn-danger">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Agregar la paginación -->

    <!-- Modal para agregar categoría -->
    <div class="modal" :class="{ 'is-active': mostrarModal }">
        <div class="modal-background"></div>
        <div class="modal-content">
            <div class="box">
                <h6 class="">Agregar Categoría</h6>
                <form @submit.prevent="crearCategoria">
                    <div class="field">
                        <label class="label">Nombre de la Categoría</label>
                        <div class="control">
                            <input v-model="nuevaCategoria.Nombre_Categoria" class="form-control" type="text"
                                required />
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

    <!-- Modal para editar categoría -->
    <div class="modal" :class="{ 'is-active': mostrarModalEdicion }">
        <div class="modal-background"></div>
        <div class="modal-content">
            <div class="box">
                <h3 class="">Editar Categoría</h3>
                <form @submit.prevent="editarCategoria">
                    <div class="field">
                        <label class="label">Nombre de la Categoría</label>
                        <div class="control">
                            <input v-model="categoriaSeleccionada.Nombre_Categoria" class="form-control" type="text"
                                required />
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
            categorias: [],
            mostrarModal: false,
            mostrarModalEdicion: false,
            nuevaCategoria: {
                Nombre_Categoria: ''
            },
            categoriaSeleccionada: {
                id: '',
                Nombre_Categoria: ''
            },
            dataTable: null
        }
    },
    mounted() {
        this.getCategorias()
    },
    methods: {
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
                    this.initializeDataTable()
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
            this.nuevaCategoria = {
                Nombre_Categoria: ''
            }
        },
        crearCategoria() {
            const token = localStorage.getItem('token')

            axios
                .post('/api/v1/categorias/store', this.nuevaCategoria, {
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
                    this.getCategorias()
                    this.cerrarModal()
                })
                .catch((error) => {
                    let errorMessage = 'Error al realizar la solicitud al servidor'
                    if (error.response) {
                        errorMessage = error.response.data.message || 'Error al crear la categoría'
                    } else if (error.request) {
                        errorMessage = 'No se recibió respuesta del servidor'
                    }
                    this.$swal({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error'
                    })
                    console.error('Error al crear la categoría:', error)
                })
        },
        eliminarCategoria(id) {
            const token = localStorage.getItem('token')

            axios
                .delete(`/api/v1/categorias/destroy/${id}`, {
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
                    this.getCategorias()
                })
                .catch((error) => {
                    let errorMessage = 'Error al realizar la solicitud al servidor'
                    if (error.response) {
                        errorMessage = error.response.data.message || 'Error al eliminar la categoría'
                    } else if (error.request) {
                        errorMessage = 'No se recibió respuesta del servidor'
                    }
                    this.$swal({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error'
                    })
                    console.error('Error al eliminar la categoría:', error)
                })
        },
        abrirModalEdicion(categoria) {
            this.categoriaSeleccionada = {
                ...categoria
            }
            this.mostrarModalEdicion = true
        },
        cancelarEdicion() {
            this.mostrarModalEdicion = false
            this.resetForm()
        },
        editarCategoria() {
            const token = localStorage.getItem('token')

            axios
                .put(
                    `/api/v1/categorias/update/${this.categoriaSeleccionada.id}`,
                    this.categoriaSeleccionada, {
                        headers: {
                            Authorization: `Bearer ${token}`
                        }
                    }
                )
                .then((response) => {
                    this.$swal({
                        title: 'Éxito!',
                        text: response.data.message,
                        icon: 'success'
                    })
                    this.getCategorias()
                    this.mostrarModalEdicion = false
                })
                .catch((error) => {
                    let errorMessage = 'Error al realizar la solicitud al servidor'
                    if (error.response) {
                        errorMessage = error.response.data.message || 'Error al editar la categoría'
                    } else if (error.request) {
                        errorMessage = 'No se recibió respuesta del servidor'
                    }
                    this.$swal({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error'
                    })
                    console.error('Error al editar la categoría:', error)
                })
        },

        // Nueva función para filtrar las mesas según el término de búsqueda
        filtrarCategorias() {
            const textoBusqueda = this.busqueda.toLowerCase();
            this.categorias = this.categorias.filter((categoria) => {
                return categoria.Nombre_Categoria.toLowerCase().includes(textoBusqueda);
            });
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