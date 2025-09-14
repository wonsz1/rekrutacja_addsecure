<template>
  <div>
    <VAlert v-if="error" type="error" class="mb-4">
      {{ error }}
    </VAlert>
    
    <v-data-table
      v-if="!loading && !error"
      :headers="headers"
      :items="vehicles"
      :items-per-page="10"
      class="elevation-1"
    >
    <template v-slot:top>
      <v-toolbar
        flat
      >
        <v-toolbar-title>Vehicles</v-toolbar-title>
        <v-divider
          class="mx-4"
          inset
          vertical
        ></v-divider>
        <v-spacer></v-spacer>

        <v-btn
              color="primary"
              dark
              class="mb-2"
              @click="addItem"
            >
              New Item
        </v-btn>

        <v-dialog
          v-model="dialog"
          max-width="500px"
        >
          <v-card>
            <v-card-title>
              <span class="text-h5">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-row>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="formItem.registrationNumber"
                      label="Registration Number"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="formItem.brand"
                      label="Brand"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="formItem.model"
                      label="Model"
                    ></v-text-field>
                  </v-col>
                  <v-col
                    cols="12"
                    sm="6"
                    md="4"
                  >
                    <v-text-field
                      v-model="formItem.type"
                      label="Vehicle type"
                    ></v-text-field>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="blue darken-1"
                text
                @click="close"
              >
                Cancel
              </v-btn>
              <v-btn
                color="blue darken-1"
                text
                @click="save"
              >
                Save
              </v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
      </v-toolbar>
    </template>

    <template v-slot:item.actions="{ item }">
        <v-icon
        small
        class="mr-2"
        @click="editItem(item)"
      >
        mdi-pencil
      </v-icon>
      <v-icon
        small
        @click="deleteItem(item)"
      >
        mdi-delete
      </v-icon>
    </template>

      <template v-slot:item.index="{ index }">
        {{ index + 1 }}
      </template>
      
    </v-data-table>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const vehicles = ref([]);
const loading = ref(true);
const error = ref(null);
const dialogDelete = ref(false);
const dialog = ref(false);
const formTitle = ref('New Item');

const headers = [
  { text: 'No.', value: 'index', sortable: false, width: '80' },
  { text: 'Registration Number', value: 'registrationNumber' },
  { text: 'Brand', value: 'brand' },
  { text: 'Model', value: 'model' },
  { text: 'Vehicle Type', value: 'type' },
  { text: 'Creation Date', value: 'createdAt' },
  { text: 'Modification Date', value: 'updatedAt' },
  { text: 'Actions', value: 'actions', sortable: false }
];

const defaultItem = {
  id: 0,
  registrationNumber: '',
  brand: '',
  model: '',
  type: ''
};
const formItem = ref({
  id: 0,
  registrationNumber: '',
  brand: '',
  model: '',
  type: ''
});

const addItem = () => {
    formItem.value = defaultItem;
    formTitle.value = 'New Item';
    dialog.value = true
};

const editItem = (item) => {
    formTitle.value = 'Edit Item';
    formItem.value = Object.assign({}, item)
    dialog.value = true
};

// const deleteItem = (item) => {
//     editedItem.value = Object.assign({}, item)
//     dialogDelete.value = true
// };

// const deleteItemConfirm = () => {
//     //desserts.value.splice(editedIndex.value, 1)
//     closeDelete()
// };


// const closeDelete = () => {
//     dialogDelete.value = false
//     nextTick(() => {
//         editedItem.value = Object.assign({}, defaultItem)
//     })
// };

const fetchVehicles = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    const response = await axios.get('/vehicles/list');

    vehicles.value = response.data.data;
  } catch (err) {
    console.error('Error fetching vehicles:', err);
    error.value = err.message || 'An error occurred while loading vehicles';
  } finally {
    loading.value = false;
  }
};

const save = async () => {
    const isEdit = formItem.value.id > 0;
    loading.value = true;
    error.value = null;
    
    const url = isEdit ? `/vehicles/save/${formItem.value.id}` : '/vehicles/save/0';
  try {

    // Basic validation
    if (!formItem.value.registrationNumber || !formItem.value.brand || !formItem.value.model || !formItem.value.type) {
      throw new Error('All fields are required');
    }
    console.log(formItem.value);
    const response = await axios.post(url, formItem.value);
    
    const data = await response.json();
    
    if (!response.ok) {
      throw new Error(data.error || `Failed to ${isEdit ? 'update' : 'create'} vehicle`);
    }
    
    if (formItem.value.id > 0) {
        vehicles.value.push(formItem.value)
    } else {
        vehicles.value.splice(vehicles.value.indexOf(formItem.value), 1, formItem.value)
    }

    // Close dialog and refresh the list
    dialog.value = false;
    //await fetchVehicles();
    
  } catch (err) {
    console.error(`Error ${isEdit ? 'updating' : 'creating'} vehicle:`, err);
    error.value = err.message || `An error occurred while ${isEdit ? 'updating' : 'creating'} the vehicle`;
  } finally {
    loading.value = false;
  }
}

const deleteVehicle = async (id) => {
  if (!confirm('Are you sure you want to delete this vehicle?')) {
    return;
  }
  
  try {
    const response = await fetch(`/vehicles/${id}`, {
      method: 'DELETE'
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.error || 'Failed to delete vehicle');
    }
    
    // Refresh the list after deletion
    fetchVehicles();
  } catch (err) {
    console.error('Error deleting vehicle:', err);
    error.value = err.message || 'An error occurred while deleting the vehicle';
  }
};

// Fetch vehicles when component is mounted
onMounted(() => {
  fetchVehicles();
});
</script>