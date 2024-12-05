<template>
  <div id="app">
    <header class="header">
      <h1 class="title">NICOLAS</h1>
    </header>

    <main class="main-container">
      <!-- Panel de selección de almacenamiento -->
      <aside class="storage-selector">
        <button @click="selectStorage('class')" :class="{ active: selectedStorage === 'class' }">Class Storage</button>
        <button @click="selectStorage('json')" :class="{ active: selectedStorage === 'json' }">JSON</button>
        <button @click="selectStorage('csv')" :class="{ active: selectedStorage === 'csv' }">CSV</button>
      </aside>

      <!-- Área de acciones y resultados -->
      <section class="content">
        <!-- Botones de acciones -->
        <div class="actions">
          <button @click="getFiles">Get Files</button>
          <button @click="prepareStoreFile">Store</button>
          <button @click="prepareShowFile">Show</button>
          <button @click="prepareUpdateFile">Update</button>
          <button @click="prepareDeleteFile">Delete</button>
        </div>

        <!-- Inputs dinámicos -->
        <div v-if="showInput" class="input-container">
          <input type="text" v-model="filename" placeholder="Nombre del archivo" />
          <button @click="send">SEND</button>
        </div>
        <div v-if="storeInput" class="input-container">
          <input type="text" v-model="filename" placeholder="Nombre del archivo" />
          <textarea v-model="fileContent" placeholder="Contenido del archivo"></textarea>
          <button @click="storeFile">SEND</button>
        </div>
        <div v-if="updateInput" class="input-container">
          <input type="text" v-model="filename" placeholder="Nombre del archivo" />
          <textarea v-model="fileContent" placeholder="Nuevo contenido del archivo"></textarea>
          <button @click="updateFile">SEND</button>
        </div>
        <div v-if="deleteInput" class="input-container">
          <input type="text" v-model="filename" placeholder="Nombre del archivo a eliminar" />
          <button @click="deleteFile">DELETE</button>
        </div>

        <!-- Área de resultados -->
        <div class="result">
          <pre>{{ result }}</pre>
        </div>
      </section>
    </main>
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
      return ${baseUrls[this.selectedStorage]}${id ? /${id} : ""};
    },
    async getFiles() {
      try {
        const url = this.getApiUrl();
        const response = await axios.get(url);
        this.result = JSON.stringify(response.data, null, 2);
      } catch (error) {
        this.result = Error: ${error.response?.status} - ${error.response?.data?.mensaje || error.message};
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
        this.result = Error: ${error.response?.status} - ${error.response?.data?.mensaje || error.message};
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
        this.result = Error: ${error.response?.status} - ${error.response?.data?.mensaje || error.message};
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
        this.result = Error: ${error.response?.status} - ${error.response?.data?.mensaje || error.message};
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
        this.result = Error: ${error.response?.status} - ${error.response?.data?.mensaje || error.message};
      }
    },
  },
};
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html, body {
  font-family: Arial, sans-serif;
  height: 100%;
  background-color: #f0f4f8;
}

#app {
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 100%;
  width: 100%;
}

.header {
  background-color: #4caf50;
  color: white;
  padding: 20px;
  text-align: center;
  width: 100%;
}

.title {
  font-size: 2rem;
  font-weight: bold;
}

.main-container {
  display: flex;
  flex-direction: row;
  width: 90%;
  max-width: 1200px;
  margin-top: 20px;
}

.storage-selector {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 10px;
  background-color: white;
  border: 1px solid #ccc;
  padding: 20px;
  border-radius: 8px;
}

.storage-selector button {
  padding: 10px;
  border: none;
  background-color: #f0f4f8;
  color: #333;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.storage-selector button.active {
  background-color: #4caf50;
  color: white;
}

.storage-selector button:hover {
  background-color: #4caf50;
  color: white;
}

.content {
  flex: 3;
  display: flex;
  flex-direction: column;
  gap: 20px;
  background-color: white;
  border: 1px solid #ccc;
  padding: 20px;
  border-radius: 8px;
}

.actions {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.actions button {
  padding: 10px 20px;
  border: none;
  background-color: #4caf50;
  color: white;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.actions button:hover {
  background-color: #45a049;
}

.input-container {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.input-container input,
.input-container textarea {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  width: 100%;
}

.input-container button {
  align-self: flex-start;
  padding: 10px 20px;
  border: none;
  background-color: #4caf50;
  color: white;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.3s ease;
}

.input-container button:hover {
  background-color: #45a049;
}

.result {
  background-color: #f9f9f9;
  border: 1px solid #ccc;
  border-radius: 4px;
  padding: 20px;
  white-space: pre-wrap;
  overflow-x: auto;
}
</style>

