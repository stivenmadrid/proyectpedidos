<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12" id="tabla-container">
        <div class="container mt-4">
          <h1>Roles y permisos</h1>
          <button @click="abrirModalCrear" class="btn btn-primary">Agregar Rol</button>
        </div>

        <div class="table-responsive">
          <table class="table" id="userTable">
            <thead>
              <tr class="table-dark">
                <th scope="col">#</th>
                <th scope="col">Nombre del Rol</th>
                <th scope="col">Permisos</th>
                <th scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody class="table-group-divider" id="roles">
              <tr v-for="(role, index) in roles" :key="role.id">
                <td>{{ index + 1 }}</td>
                <td>{{ role.name }}</td>
                <td class="permission-cell">
                    <div class="permission-icons">
                      <span
                        v-for="(permission, permissionIndex) in role.permissions"
                        :key="permission.id"
                        class="permission-icon"
                      >
                        <i class="fas fa-check-circle"></i>
                        {{ permission.name }}
                        <span v-if="permissionIndex < role.permissions.length - 1" class="permission-divider"> | </span>
                      </span>
                    </div>
                  </td>
                <td>
                  <button @click="editarRol(role)" class="btn btn-primary">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button @click="eliminarRol(role.id)" class="btn btn-danger">
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


  <!-- //PERMISOS -->
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12" id="tabla-container">
        <div class="container mt-4">
          <h1> permisos</h1>
          <button @click="abrirModalCrearPermiso" class="btn btn-primary">Agregar Permisos</button>
        </div>

        <div class="table-responsive">
          <table class="table" id="userTable">
            <thead>
              <tr class="table-dark">
                <th scope="col">#</th>
                <th scope="col">Nombre del permiso</th>
                <th scope="col">Guard_Name</th>
                <th scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody class="table-group-divider" id="permisos">
                <tr v-for="(permiso, index) in permisos" :key="permiso.id">
                  <td>{{ index + 1 }}</td>
                  <td>{{ permiso.name }}</td>
                  <td>{{ permiso.guard_name }}</td>
                  <td>
                    <button @click="editarPermiso(permiso)" class="btn btn-primary">
                      <i class="fas fa-edit"></i>
                    </button>

                    <button @click="eliminarPermiso(permiso.id)" class="btn btn-danger">
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

  <!-- Modal permisos -->
  <div class="modal" :class="{ 'is-active': mostrarModalCrearPermiso }">
      <div class="modal-background"></div>
      <div class="modal-content">
        <div class="box">
          <h6 class="">Agregar Permiso</h6>
          <form @submit.prevent="crearPermiso">
            <div class="field">
              <label class="label">Nombre del Permiso</label>
              <div class="control">
                <input v-model="nuevoPermiso.nombre" class="form-control" type="text" required />
              </div>
            </div>
            <div class="field">
              <label class="label">Guard Name</label>
              <div class="control">
                <input v-model="nuevoPermiso.guard_name" class="form-control" type="text" required  disabled/>
              </div>
            </div>
            <div class="field">
              <div class="control">
                <br />
                <button type="submit" class="btn btn-primary">Crear Permiso</button>
                <button type="button" class="btn btn-secondary" @click="cancelarCreacionPermiso">Cancelar</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

      <!-- Modal para editar permisos -->
  <div class="modal" :class="{ 'is-active': mostrarModalEditarPermiso }">
    <div class="modal-background"></div>
    <div class="modal-content">
      <div class="box">
        <h6 class="">Editar Permiso</h6>
        <form @submit.prevent="guardarCambiosPermiso">
          <div class="field">
            <label class="label">Nombre del Permiso</label>
            <div class="control">
              <input v-model="permisoEditado.nombre" class="form-control" type="text" required />
            </div>
          </div>
          <div class="field">
            <label class="label">Guard Name</label>
            <div class="control">
              <input v-model="permisoEditado.guard_name" class="form-control" type="text" required disabled />
            </div>
          </div>
          <div class="field">
            <div class="control">
              <br />
              <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              <button type="button" class="btn btn-secondary" @click="cancelarEdicionPermiso">Cancelar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  
  <!-- Modal para agregar roles -->
  <div class="modal" :class="{ 'is-active': mostrarModal }">
    <div class="modal-background"></div>
    <div class="modal-content">
      <div class="box">
        <h6 class="">Agregar Roles y permisos</h6>
        <form @submit.prevent="crearRol">
      <div class="field">
        <label class="label">Nombre del Rol</label>
        <div class="control">
          <input v-model="nuevoRol.nombre" class="form-control" type="text" required />
        </div>
      </div>
      <div class="field">
        <label class="label">Permisos</label>
        <div v-for="permiso in permisos" :key="permiso.id" class="permission-checkbox">
          <label class="checkbox-label">
            <input
              type="checkbox"
              :value="permiso.id"
              v-model="permisosSeleccionados"
              class="styled-checkbox"
            />
            {{ permiso.name }}
          </label>
        </div>
      </div>
      <div class="field">
        <div class="control">
          <br />
          <button type="submit" class="btn btn-primary">Crear Rol</button>
          <button type="button" class="btn btn-secondary" @click="cancelarCreacion">Cancelar</button>
        </div>
      </div>
    </form>
      </div>
    </div>
  </div>


    <!-- Modal para editar roles -->
    <div class="modal" :class="{ 'is-active': mostrarModaledit }">
    <div class="modal-background"></div>
    <div class="modal-content">
      <div class="box">
        <h6 class="">Editar Rol</h6>
        <form @submit.prevent="guardarCambios">
          <div class="field">
            <label class="label">Nombre del Rol</label>
            <div class="control">
              <input v-model="rolEditado.nombre" class="form-control" type="text" required />
            </div>
          </div>
          <div class="field">
            <label class="label">Permisos</label>
            <div v-for="permiso in permisos" :key="permiso.id" class="permission-checkbox">
              <label class="checkbox-label">
                <input
                  type="checkbox"
                  :value="permiso.id"
                  v-model="permisosSeleccionadosEdit"
                  class="styled-checkbox"
                />
                {{ permiso.name }}
              </label>
            </div>
          </div>
          <div class="field">
            <div class="control">
              <br />
              <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              <button type="button" class="btn btn-secondary" @click="cancelarEdicion">Cancelar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

</template>

<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
  data() {
    return {
      mostrarModal: false,
      mostrarModaledit: false,
      mostrarModalCrearPermiso: false,
      mostrarModalEditarPermiso: false,
      nuevoRol: {
        nombre: '',
      },
      rolEditado: {
        id: null,
        nombre: '',
      },
      nuevoPermiso: {
        nombre: '',
        guard_name: 'web', // Cambia esto según tu necesidad
      },
      permisoEditado: {
        id: null,
        nombre: '',
        guard_name: 'web',
      },
      permisos: [],
      permisosSeleccionados: [],
      permisosSeleccionadosEdit: [],
      roles: [],
    };
  },
  mounted() {
    this.getRoles();
    this.getPermisos();
  },
  methods: {
    abrirModalCrearPermiso() {
      this.mostrarModalCrearPermiso = true;
    },
    cancelarCreacionPermiso() {
      this.mostrarModalCrearPermiso = false;
      this.resetForm();
    },
    abrirModalCrear() {
      this.mostrarModal = true;
      this.resetForm();
    },
    abrirModalEditar(role) {
      this.mostrarModaledit = true;
      this.rolEditado = {
        id: role.id,
        nombre: role.name,
      };
      this.permisosSeleccionadosEdit = role.permissions
        ? role.permissions.map((permission) => permission.id)
        : [];
    },
    cancelarCreacion() {
      this.cerrarModal();
    },
    cancelarEdicion() {
      this.cerrarModal();
    },
    cerrarModal() {
      this.mostrarModal = false;
      this.mostrarModaledit = false;
      this.resetForm();
    },
    resetForm() {
      this.nuevoRol = {
        nombre: '',
      };
      this.permisosSeleccionados = [];
      this.permisosSeleccionadosEdit = [];
    },
    getPermisos() {
      axios.get('/api/V1/permissions')
        .then(response => {
          this.permisos = response.data;
        })
        .catch(error => {
          console.error(error);
        });
    },
    crearRol() {
      const datos = {
        nombre: this.nuevoRol.nombre,
        guard_name: 'web',
        permisos: this.permisosSeleccionados,
      };

      const token = localStorage.getItem('token');

      axios.post('/api/v1/roles', datos, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then(response => {
        console.log(response.data);
        this.cerrarModal();
        this.getRoles();
        Swal.fire({
          icon: 'success',
          title: 'Éxito',
          text: response.data.message,
        });
      })
      .catch(error => {
        console.error(error);
        if (error.response) {
          console.log('Respuesta del servidor:', error.response.data);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response.data.message,
          });
        }
      });
    },
    editarRol(role) {
      this.mostrarModaledit = true;
      this.rolEditado = {
        id: role.id,
        nombre: role.name,
      };
      this.permisosSeleccionadosEdit = role.permissions
        ? role.permissions.map(permission => permission.id)
        : [];
    },
    guardarCambios() {
      const datos = {
        nombre: this.rolEditado.nombre,
        permisos: this.permisosSeleccionadosEdit,
      };

      const token = localStorage.getItem('token');

      axios.put(`/api/v1/roles/update/${this.rolEditado.id}`, datos, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then(response => {
        console.log(response.data);
        this.cerrarModal();
        this.getRoles();
        Swal.fire({
          icon: 'success',
          title: 'Éxito',
          text: response.data.message,
        });
      })
      .catch(error => {
        console.error(error);
        if (error.response) {
          console.log('Respuesta del servidor:', error.response.data);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response.data.message,
          });
        }
      });
    },
    eliminarRol(rolId) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: 'Esta acción eliminará el rol. Esta acción no se puede deshacer.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      const token = localStorage.getItem('token');

      axios.delete(`/api/v1/roles/destroy/${rolId}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then(response => {
        console.log(response.data);
        this.getRoles();
        Swal.fire({
          icon: 'success',
          title: 'Éxito',
          text: response.data.message,
        });
      })
      .catch(error => {
        console.error(error);
        if (error.response) {
          console.log('Respuesta del servidor:', error.response.data);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response.data.message,
          });
        }
      });
    }
  });
},

    getRoles() {
      axios.get('/api/v1/roles')
        .then(response => {
          this.roles = response.data;
        })
        .catch(error => {
          console.error(error);
        });
    },

    crearPermiso() {
    const token = localStorage.getItem('token'); // Obtén el token de autenticación

    const datos = {
      name: this.nuevoPermiso.nombre,
      guard_name: this.nuevoPermiso.guard_name,
    };

    axios.post('/api/V1/permissions', datos, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then(response => {
      console.log(response.data);
      this.cancelarCreacionPermiso(); // Cierra el modal y resetea el formulario
      this.getPermisos(); // Actualiza la lista de permisos

      // Mostrar mensaje de éxito con SweetAlert u otra librería similar
      Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: response.data.message,
      });
    })
    .catch(error => {
      console.error(error);
      if (error.response) {
        console.log('Respuesta del servidor:', error.response.data);

        // Mostrar mensaje de error con SweetAlert u otra librería similar
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: error.response.data.message,
        });
      }
    });
  },
  editarPermiso(permiso) {
      this.mostrarModalEditarPermiso = true;
      this.permisoEditado = {
        id: permiso.id,
        nombre: permiso.name,
        guard_name: permiso.guard_name,
      };
    },

    cancelarEdicionPermiso() {
      this.mostrarModalEditarPermiso = false;
      this.resetFormPermiso();
    },

    guardarCambiosPermiso() {
  const token = localStorage.getItem('token'); // Obtén el token de autenticación

  const datos = {
    name: this.permisoEditado.nombre,
    guard_name: this.permisoEditado.guard_name,
  };

  axios
    .put(`/api/V1/permissions/update/${this.permisoEditado.id}`, datos, {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    })
    .then(response => {
      console.log(response.data);
      this.cancelarEdicionPermiso(); // Cierra el modal y resetea el formulario
      this.getPermisos(); // Actualiza la lista de permisos
    })
    .catch(error => {
      console.error(error);
      if (error.response) {
        console.log('Respuesta del servidor:', error.response.data);
      }
    })
    .finally(() => {
      // Mostrar mensaje de éxito con SweetAlert u otra librería similar
      Swal.fire({
        icon: 'success',
        title: 'Éxito',
        text: 'Permiso actualizado exitosamente',
      });
    });
},
eliminarPermiso(permisoId) {
      const token = localStorage.getItem('token'); // Obtén el token de autenticación

      const url = `/api/V1/permissions/destroy/${permisoId}`; // URL de eliminación del permiso

      axios.delete(url, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      })
      .then(response => {
        console.log(response.data);
        this.getPermisos(); // Actualiza la lista de permisos después de eliminar uno

        // Mostrar mensaje de éxito con SweetAlert u otra librería similar
        Swal.fire({
          icon: 'success',
          title: 'Éxito',
          text: response.data.message,
        });
      })
      .catch(error => {
        console.error(error);
        if (error.response) {
          console.log('Respuesta del servidor:', error.response.data);

          // Mostrar mensaje de error con SweetAlert u otra librería similar
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.response.data.message,
          });
        }
      });
    },
  },



};
</script>

