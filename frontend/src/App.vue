<template>
  <div id="app">
    <h1 class="title">&hearts; MI PÁGINA WEB &hearts;</h1>

    <div class="container">
      <!-- Panel de selección de almacenamiento -->
      <div class="storage-selector">
        <button @click="selectStorage('class')" :class="{ active: selectedStorage === 'class' }">Class Storage</button>
        <button @click="selectStorage('json')" :class="{ active: selectedStorage === 'json' }">JSON</button>
        <button @click="selectStorage('csv')" :class="{ active: selectedStorage === 'csv' }">CSV</button>
      </div>

      <!-- Botones de acciones -->
      <div class="actions">
        <button @click="getFiles">Get Files</button>
        <button @click="prepareStoreFile">Store</button>
        <button @click="prepareShowFile">Show</button>
        <button @click="prepareUpdateFile">Update</button>
        <button @click="prepareDeleteFile">Delete</button>
      </div>

      <!-- Inputs dinámicos -->
      <div v-if="showInput" class="send-container">
        <input type="text" v-model="filename" placeholder="Escribe el nombre del archivo" />
        <button class="send-button" @click="send">SEND</button>
      </div>
      <div v-if="storeInput" class="store-container">
        <input type="text" v-model="filename" placeholder="Nombre del archivo" />
        <textarea v-model="fileContent" placeholder="Contenido del archivo"></textarea>
        <button class="send-button" @click="storeFile">SEND</button>
      </div>
      <div v-if="updateInput" class="store-container">
        <input type="text" v-model="filename" placeholder="Nombre del archivo" />
        <textarea v-model="fileContent" placeholder="Nuevo contenido del archivo"></textarea>
        <button class="send-button" @click="updateFile">SEND</button>
      </div>
      <div v-if="deleteInput" class="send-container">
        <input type="text" v-model="filename" placeholder="Nombre del archivo a eliminar" />
        <button class="send-button" @click="deleteFile">DELETE</button>
      </div>

      <!-- Área de resultados -->
      <div class="result">
        <pre>{{ result }}</pre>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      selectedStorage: "class", // Por defecto es "class"
      result: "",
      filename: "",
      fileContent: "",
      showInput: false,
      storeInput: false,
      updateInput: false,
      deleteInput: false,
    };
  },
  methods: {
    selectStorage(storage) {
      this.selectedStorage = storage;
      this.resetInputs();
    },
    resetInputs() {
      this.showInput = false;
      this.storeInput = false;
      this.updateInput = false;
      this.deleteInput = false;
      this.result = "";
      this.filename = "";
      this.fileContent = "";
    },
    getApiUrl(action = "", id = "") {
      // Devuelve la URL dinámica según la clase seleccionada
      const baseUrls = {
        class: "http://localhost:8000/api/hello",
        json: "http://localhost:8000/api/json",
        csv: "http://localhost:8000/api/csv",
      };
      return `${baseUrls[this.selectedStorage]}${id ? `/${id}` : ""}`;
    },
    async getFiles() {
      try {
        const url = this.getApiUrl();
        const response = await axios.get(url);
        this.result = JSON.stringify(response.data, null, 2);
      } catch (error) {
        this.result = `Error: ${error.response?.status} - ${error.response?.data?.mensaje || error.message}`;
      }
    },
    prepareShowFile() {
      this.resetInputs();
      this.showInput = true;
    },
    async send() {
      try {
        if (!this.filename) {
          this.result = "Por favor, escribe el nombre del archivo.";
          return;
        }

        const url = this.getApiUrl("", this.filename);
        const response = await axios.get(url);
        this.result = JSON.stringify(response.data, null, 2);
      } catch (error) {
        this.result = `Error: ${error.response?.status} - ${error.response?.data?.mensaje || error.message}`;
      }
    },
    prepareStoreFile() {
      this.resetInputs();
      this.storeInput = true;
    },
    async storeFile() {
      try {
        if (!this.filename || !this.fileContent) {
          this.result = "Por favor, ingresa el nombre y contenido del archivo.";
          return;
        }

        // En caso de JSON, validamos el contenido
        const content =
          this.selectedStorage === "json"
            ? JSON.stringify(JSON.parse(this.fileContent)) // Validación JSON
            : this.fileContent; // Para class y csv usamos el texto directo

        const url = this.getApiUrl();
        const response = await axios.post(url, {
          filename: this.filename,
          content,
        });

        this.result = JSON.stringify(response.data, null, 2);
      } catch (error) {
        this.result = `Error: ${error.response?.status} - ${error.response?.data?.mensaje || error.message}`;
      }
    },
    prepareUpdateFile() {
      this.resetInputs();
      this.updateInput = true;
    },
    async updateFile() {
      try {
        if (!this.filename || !this.fileContent) {
          this.result = "Por favor, ingresa el nombre y contenido.";
          return;
        }

        const url = this.getApiUrl("", this.filename);
        const response = await axios.put(url, { content: this.fileContent });
        this.result = JSON.stringify(response.data, null, 2);
      } catch (error) {
        this.result = `Error: ${error.response?.status} - ${error.response?.data?.mensaje || error.message}`;
      }
    },
    prepareDeleteFile() {
      this.resetInputs();
      this.deleteInput = true;
    },
    async deleteFile() {
      try {
        if (!this.filename) {
          this.result = "Por favor, escribe el nombre del archivo a eliminar.";
          return;
        }

        const url = this.getApiUrl("", this.filename);
        const response = await axios.delete(url);
        this.result = JSON.stringify(response.data, null, 2);
      } catch (error) {
        this.result = `Error: ${error.response?.status} - ${error.response?.data?.mensaje || error.message}`;
      }
    },
  },
};
</script>



<style>
/* Estilos básicos */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body, #app {
  width: 100%;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  font-family: Arial, sans-serif;
}

/* Estilo del título centrado horizontalmente */
.title {
  font-size: 2.5em;
  font-weight: bold;
  text-align: center;
  width: 100%;
  margin: 20px 0;
}

/* Estilo para el contenedor general */
.container {
  display: grid;
  grid-template-columns: 1fr 4fr;
  grid-template-rows: auto auto 1fr auto;
  gap: 20px;
  width: 100%;
  height: 100%;
  max-width: 1200px;
  padding: 20px;
}

/* Panel de selección de almacenamiento */
.storage-selector {
  grid-column: 1 / 2;
  grid-row: 2 / 4;
  display: flex;
  flex-direction: column;
  align-items: stretch;
  padding: 10px;
  border: 1px solid #ccc;
  background-color: #f9f9f9;
  height: 100%;
}

.storage-selector button {
  margin-bottom: 10px;
  padding: 8px;
  background-color: #f0f0f0;
  border: 1px solid #ccc;
  cursor: pointer;
  width: 100%;
}

.storage-selector button.active {
  background-color: #d0e8ff;
  font-weight: bold;
}

/* Botones de acciones */
.actions {
  grid-column: 2 / 3;
  grid-row: 2 / 3;
  display: flex;
  justify-content: space-around;
  max-width: 100%;
}

.actions button {
  flex: 1;
  padding: 10px;
  background-color: #e0e0e0;
  border: 1px solid #ccc;
  cursor: pointer;
  margin: 0 5px;
}

.actions button:hover {
  background-color: #d0d0d0;
}

/* Contenedor para Show y Delete */
.send-container {
  grid-column: 2 / 3;
  grid-row: 4 / 5;
  display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
}

.send-container input {
  flex: 1;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.send-container button {
  padding: 10px 20px;
  background-color: #4caf50;
  color: #fff;
  border: none;
  cursor: pointer;
}

.send-container button:hover {
  background-color: #45a049;
}

/* Contenedor para Store y Update */
.store-container {
  grid-column: 2 / 3;
  grid-row: 4 / 5;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.store-container input,
.store-container textarea {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  width: 100%;
}

.store-container button {
  padding: 10px 20px;
  background-color: #4caf50;
  color: #fff;
  border: none;
  cursor: pointer;
}

.store-container button:hover {
  background-color: #45a049;
}

/* Área de resultados */
.result {
  grid-column: 2 / 3;
  grid-row: 3 / 4;
  height: 100%;
  padding: 15px;
  color: #000;
  border: 1px solid #ddd;
  font-size: 1em;
  font-family: monospace;
  background-color: #fafafa;
  text-align: left;
  overflow-y: auto;
  width: 100%;
  box-sizing: border-box;
}
</style>
