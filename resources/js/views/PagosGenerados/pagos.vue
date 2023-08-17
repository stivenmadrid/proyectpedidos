

<template>
  <div class="container-fluid" >
      <div class="row">
          <div class="col-sm-12" id="tabla-container">
              <div class="container mt-4">
                  <h1>Pagos Generados</h1>
                  <button @click="exportarAExcel" class="btn btn-success mr-2">
                    <i class="fas fa-file-excel"></i> Exportar a Excel
                  </button>
                    <button @click="abrirModalroles" class="btn btn-info mr-2">
                      <i class="fas fa-dollar-sign"></i> Registrar pagos
                    </button>
                    <button @click="abrirModalPagosCancelados" class="btn btn-danger mr-2">
                      <i class="fas fa-ban"></i> Pagos Cancelados
                    </button>
                 
              </div>
  
              <div class="table-responsive">
                  <table class="table " id="userTable">
                      <thead>
                        <tr>
                          <th>PEDIDO #</th>
                          <th>MESA</th>
                          <th>ESTADO PEDIDO</th>
                          <th>TOTAL PAGAR</th>
                          <th>FECHA PEDIDO</th>
                          <th>PRODUCTOS</th>
                          <th>Acciones</th>
                        </tr>
                      </thead>
                      <tbody>
                <tr v-for="pedido in pedidos" :key="pedido.id">
                  <td>{{ pedido.id }}</td>
                  <td>{{ pedido.mesa ? pedido.mesa.nombre : "-" }}</td>
                  <td class="pagoestado">{{ pedido.estado }}</td>
                  <td>{{ pedido.total | formatCurrency }}</td>
                  <td>{{ formatDate(pedido.created_at) }}</td>
                  <td>
                    <ul>
                      <li v-for="producto in pedido.productos" :key="producto.id">
                        {{ producto.nombre }} (Cantidad:
                        {{ producto.pivot.cantidad }})
                      </li>
                    </ul>
                  </td>
                  <td>
                    <button class="btn btn-primary" @click="imprimirRecibo">
                      <i class="fas fa-print"></i> Imprimir Recibo
                    </button>

                
                </td>
                </tr>
          </tbody>
                  </table>
              </div>
          </div>
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
     };
   },
   mounted() {
     this.getPedidos();
   },
   methods: {
     getPedidos() {
       const token = localStorage.getItem("token");
 
       axios
         .get("/api/v1/pagos/lista", {
           headers: {
             Authorization: `Bearer ${token}`,
           },
         })
         .then((response) => {
           this.pedidos = response.data;
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
 
     initDataTable() {
       if (this.$refs.dataTable) {
         this.dataTable = new DataTable(this.$refs.dataTable, {
           // Configuración de DataTables
         });
       }
     },
   },
   beforeDestroy() {
     if (this.dataTable) {
       this.dataTable.destroy();
     }
   },
   filters: {
     formatCurrency(value) {
       const formatter = new Intl.NumberFormat("es", {
         style: "currency",
         currency: "USD",
       });
       return formatter.format(value);
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
  