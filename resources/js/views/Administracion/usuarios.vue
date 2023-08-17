<template>
  <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12" id="tabla-container">
              <div class="container mt-4">
                  <h1>usuarios</h1>
                 
                  <button @click="abrirModal" class="btn btn-info mr-2">
                      <i class="fas fa-user-lock"></i> Registrar usuarios
                  </button>
                  

              </div><br>

              <div class="table-responsive">
                  <table class="table " id="userTable">
                      <thead>
                          <tr>
                              <th>#id</th>
                              <th>Nombre usuario</th>
                              <th>Correo</th>
                              <th>Rol</th>
                              <th>Acciones</th>
                          </tr>
                      </thead>
                      <tbody>
                          <tr v-for="user in users" :key="user.id">
                              <td>{{ user.id }}</td>
                              <td>{{ user.name }} {{ user.last_name }}</td>
                              <td>{{ user.email }}</td>
                              <td>
                                  <span v-for="(role, index) in user.roles" :key="index">
                                      {{ role.name }}
                                      <span v-if="index !== user.roles.length - 1">, </span>
                                  </span>
                              </td>
                              <td>
                                  
                                  <button @click="openPasswordResetModal(user.id)" class="btn btn-warning">
                                        <i class="fas fa-key"></i>
                                </button>
                                  <button @click="abrirModalEdicion(user)" class="btn btn-primary">
                                      <i class="fas fa-edit"></i>
                                  </button>


                                  <button @click="eliminarUsuario(user.id)" class="btn btn-danger">
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
 
    <!-- Modal para restablecer contraseña -->
    <div class="modal" :class="{ 'is-active': mostrarModalRestablecer }">
      <div class="modal-background"></div>
      <div class="modal-content">
        <div class="box">
          <h3>Restablecer Contraseña</h3>
          <form @submit.prevent="resetUserPassword">
            <div class="form-group">
              <label for="newPassword">Nueva Contraseña:</label>
              <input type="password" class="form-control" v-model="newPassword" required>
            </div>
            <div class="form-group">
              <label for="confirmPassword">Confirmar Contraseña:</label>
              <input type="password" class="form-control" v-model="confirmPassword" required>
            </div>
            <button type="submit" class="btn btn-primary">Restablecer Contraseña</button>
            <button type="button" class="btn btn-secondary" @click="closePasswordResetModal">
              Cancelar
            </button>
          </form>
        </div>
      </div>
    </div>

  <!-- Modal para agregar usuarios -->
  <div class="modal" :class="{ 'is-active': mostrarModal }">
      <div class="modal-background"></div>
      <div class="modal-content">
          <div class="box">
              <h3>Agregar Nuevo Usuario</h3>
              <form @submit.prevent="createUser">
                  <div class="form-group">
                      <label for="name">Nombre</label>
                      <input type="text" class="form-control" v-model="newUser.name" required>
                  </div>
                  <div class="form-group">
                      <label for="email">Correo electrónico</label>
                      <input type="email" class="form-control" v-model="newUser.email" required>
                  </div>
                  <div class="form-group">
                      <label for="password">Contraseña</label>
                      <input type="password" class="form-control" v-model="newUser.password" required>
                  </div>
                  <div class="form-group">
                      <label for="role_id">Rol</label>
                      <select v-model="newUser.role_name" class="form-control">
                          <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                      </select>
                  </div>
                  <button type="submit" class="btn btn-primary">Crear Usuario</button>
                  <button type="button" class="btn btn-secondary" @click="cancelarCreacion">
                      Cancelar
                  </button>
              </form>

          </div>
      </div>
  </div>
  <div class="modal" :class="{ 'is-active': mostrarModalEdicion }">
      <div class="modal-background"></div>
      <div class="modal-content">
          <div class="box">
              <h3>Editar Usuario</h3>
              <form @submit.prevent="editarUsuario">
                  <div class="form-group">
                      <label for="editName">Nombre</label>
                      <input type="text" class="form-control" v-model="editedUser.name" required>
                  </div>
                  <div class="form-group">
                      <label for="editEmail">Correo electrónico</label>
                      <input type="email" class="form-control" v-model="editedUser.email" required>
                  </div>
                  <div class="form-group">
                      <label for="editRole">Rol</label>
                      <select v-model="editedUser.role_name" class="form-control">
                          <option v-for="role in roles" :key="role.id" :value="role.name">{{ role.name }}</option>
                      </select>
                  </div>
                  <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                  <button type="button" class="btn btn-secondary" @click="cerrarModalEdicion">
                      Cancelar
                  </button>
              </form>
          </div>
      </div>
  </div>
   <!-- Modal para agregar roles -->
   <div class="modal" :class="{ 'is-active': mostrarModalRol }">

      <div class="modal-background"></div>
      <div class="modal-content">
        <div class="box">
          <h3>Crear Nuevo Rol</h3>
          <form @submit.prevent="createRole">
            <div class="form-group">
              <label for="roleName">Nombre del Rol</label>
              <input type="text" class="form-control" v-model="newRole.name" required>
            </div>
            <button type="submit" class="btn btn-primary">Crear Rol</button>
            <button type="button" class="btn btn-secondary" @click="cerrarModalRol">
              Cancelar
            </button>
          </form>
        </div>
      </div>
    </div>


</template>
<script>
import $ from 'jquery';
import 'datatables.net-dt/css/jquery.dataTables.css';
import 'datatables.net-dt/js/dataTables.dataTables';
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data() {
        return {
            mostrarModal: false,
            mostrarModalrol: false,
            mostrarModalEdicion: false,
            userDataTable: null,
            mostrarModalRestablecer: false,
            users: [],
            roles: [],
            permissions: [],
            newUser: {
                first_name: '',
                last_name: '',
                email: '',
                password: '',
                role_id: null,
            },
            editedUser: {
                id: null,
                name: '',
                email: '',
                role_name: '',
            },
            newRole: {
                name: '',
            },
            newPermission: {
                name: '',
            },
            selectedRoles: {},
            resetUserId: null,
            newPassword: '',
            confirmPassword: '',
        };
    },
    methods: {
        openPasswordResetModal(userId) {
            this.mostrarModalRestablecer = true;
            this.resetUserId = userId;
            this.newPassword = '';
            this.confirmPassword = '';
        },
        closePasswordResetModal() {
            this.mostrarModalRestablecer = false;
            this.resetUserId = null;
            this.newPassword = '';
            this.confirmPassword = '';
        },
        resetUserPassword() {
            if (this.newPassword !== this.confirmPassword) {
                Swal.fire('Error', 'Las contraseñas no coinciden', 'error');
                return;
            }

            axios.post(`/api/v1/reset-password/${this.resetUserId}`, {
                password: this.newPassword,
                password_confirmation: this.confirmPassword
            })
            .then(response => {
                Swal.fire('Éxito', 'Contraseña restablecida exitosamente', 'success');
                this.closePasswordResetModal();
            })
            .catch(error => {
                console.error('Error al restablecer la contraseña:', error);
                Swal.fire('Error', 'Ocurrió un error al restablecer la contraseña', 'error');
            });
        },

        abrirModal() {
            this.mostrarModal = true;
            this.resetForm();
        },
      
        cancelarCreacion() {
      this.cerrarModal();
      this.cerrarModalRol(); // También cierra el modal de roles
        },
        cerrarModal() {
            this.mostrarModal = false;
            this.resetForm();
        },
        abrirModalEdicion(user) {
            this.mostrarModalEdicion = true;
            this.editedUser = {
                ...user
            };
        },
        cerrarModalEdicion() {
            this.mostrarModalEdicion = false;
            this.editedUser = {
                id: null,
                name: '',
                email: '',
                role_name: '',
            };
        },
        editarUsuario() {
    const userData = {
        id: this.editedUser.id,
        name: this.editedUser.name,
        email: this.editedUser.email,
        role: this.editedUser.role_name,
    };

    axios
        .put(`api/v1/usuarios/actualizar/${this.editedUser.id}`, userData)
        .then((response) => {
            console.log('Respuesta del servidor:', response.data);
            this.fetchUsers();
            Swal.fire('Éxito', 'Usuario actualizado exitosamente', 'success');
            this.cerrarModalEdicion();
        })
        .catch((error) => {
            if (error.response) {
                console.error('Error en la respuesta del servidor:', error.response.data);
                if (error.response.status === 422) {
                    // Error de validación
                    const validationErrors = error.response.data.errors;
                    let errorMessage = 'Error de validación:<br>';
                    for (const key in validationErrors) {
                        if (validationErrors.hasOwnProperty(key)) {
                            errorMessage += `- ${validationErrors[key][0]}<br>`;
                        }
                    }
                    Swal.fire('Error', errorMessage, 'error');
                } else {
                    Swal.fire('Error', error.response.data.message || 'Ocurrió un error al editar el usuario', 'error');
                }
            } else if (error.request) {
                console.error('No se recibió respuesta del servidor:', error.request);
                Swal.fire('Error', 'No se pudo establecer conexión con el servidor', 'error');
            } else {
                console.error('Error en la solicitud:', error.message);
                Swal.fire('Error', 'Ocurrió un error en la solicitud', 'error');
            }
        });
},


        createUser() {
            const userData = {
                name: this.newUser.name,
                email: this.newUser.email,
                password: this.newUser.password,
                role: this.newUser.role_name,
            };

            axios
                .post('/api/V1/register', userData)
                .then((response) => {
                    console.log('Respuesta del servidor:', response.data);
                    this.fetchUsers();
                    Swal.fire('Éxito', response.data.message, 'success');
                    this.cerrarModal();
                    this.resetForm();
                    this.users.push(response.data.createdUser);
                })
                .catch((error) => {
                    console.error('Error al crear el usuario:', error.response.data);
                    Swal.fire('Error', error.response.data.message || 'Ocurrió un error al crear el usuario',
                        'error');
                });
        },

        resetForm() {
            this.newUser.name = '';
            this.newUser.email = '';
            this.newUser.password = '';
            this.newUser.role_name = '';
        },
        eliminarUsuario(userId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción eliminará el usuario permanentemente',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            axios.delete(`/api/v1/usuarios/destroy/${userId}`)
                .then(() => {
                    this.users = this.users.filter(user => user.id !== userId);
                    this.updateDataTable();
                    Swal.fire('Eliminado', 'El usuario ha sido eliminado', 'success');
                })
                .catch((error) => {
                    if (error.response) {
                        console.error('Error en la respuesta del servidor:', error.response.data);
                        Swal.fire('Error', error.response.data.message || 'Ocurrió un error al eliminar el usuario', 'error');
                    } else if (error.request) {
                        console.error('No se recibió respuesta del servidor:', error.request);
                        Swal.fire('Error', 'No se pudo establecer conexión con el servidor', 'error');
                    } else {
                        console.error('Error en la solicitud:', error.message);
                        Swal.fire('Error', 'Ocurrió un error en la solicitud', 'error');
                    }
                });
        }
    });
},

        fetchRoles() {
            axios
                .get('/api/v1/roles')
                .then((response) => {
                    this.roles = response.data;
                })
                .catch((error) => {
                    console.error('Error al obtener la lista de roles:', error);
                });
        },
        fetchUsers() {
            axios
                .get('/api/V1/usuarios')
                .then((response) => {
                    this.users = response.data;
                    this.updateDataTable();
                })
                .catch((error) => {
                    console.error('Error al obtener la lista de usuarios:', error);
                });
        },
        updateDataTable() {
            this.$nextTick(() => {
                $('#userTable').DataTable().destroy();
                this.$nextTick(() => {
                    $('#userTable').DataTable({
                        // Personaliza las opciones de DataTables aquí
                    });
                });
            });
        },
    },
    created() {
        this.fetchRoles();
        this.resetForm();
        this.fetchUsers();
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