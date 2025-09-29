<template>
  <div>
    <VAlert v-if="error" type="error" class="mb-4">
      {{ error }}
    </VAlert>
    
    <VDataTable
      v-if="!loading"
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
                    <v-select
                      :items="vehicleTypes"
                      item-text="label"
                      item-value="option"
                      v-model="formItem.type"
                      filled
                      label="Vehicle type"
                    ></v-select>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn
                color="blue darken-1"
                text
                @click="closeEdit"
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
        <v-dialog v-model="dialogDelete" max-width="500px">
          <v-card>
            <v-card-title class="text-h5">Are you sure you want to delete this vehicle?</v-card-title>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
              <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
              <v-spacer></v-spacer>
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
      
    </VDataTable>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const vehicles = ref([]);
const vehicleTypes = ref({});
const loading = ref(true);
const error = ref(null);
const editedIndex = ref(-1);
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
    formItem.value = Object.assign({}, defaultItem);
    formTitle.value = 'New Item';
    dialog.value = true
};

const editItem = (item) => {
    formTitle.value = 'Edit Item';
    formItem.value = Object.assign({}, item)
    editedIndex.value = vehicles.value.indexOf(item)
    dialog.value = true
};
const closeEdit = () => {
    dialog.value = false
};

const fetchVehicles = async () => {
  try {
    loading.value = true;
    error.value = null;
    
    const response = await axios.get('/vehicles/list');

    vehicles.value = response.data.data;
  } catch (err) {
    console.error('Error fetching vehicles:');
    error.value = err.message || 'An error occurred while loading vehicles';
  } finally {
    loading.value = false;
  }
};

const fetchTypes = async () => {
    vehicleTypes.value = [{'option': 'passenger', 'label': 'Passenger'}, {'option': 'truck', 'label': 'Truck'}, {'option': 'bus', 'label': 'Bus'}];
    // [todo]
    // try {
    //     const response = await axios.get('/vehicles/types');
    //     vehicleTypes.value = response.data.data;
    // } catch (err) {
    //     console.error('Error fetching vehicle types:', err);
    //     return [];
    // }
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
        const response = await axios.post(url, formItem.value);
        
        if (isEdit) {
            Object.assign(vehicles.value[editedIndex.value], formItem.value)
        } else {
            const vehicle = Object.assign({}, formItem.value);
            vehicle.id = response.data.id;
            vehicle.createdAt = response.data.createdAt;
            vehicle.updatedAt = response.data.updatedAt;
            vehicles.value.push(vehicle)
        }

        dialog.value = false;
        
    } catch (err) {
        console.error(err);
        if(err.response){
            error.value = err.response.data.message;
        } else {
            error.value = err.message;
        }
    } finally {
        loading.value = false;
    }
}

const deleteItem = (item) => {
    editedIndex.value = vehicles.value.indexOf(item)
    formItem.value = Object.assign({}, item)
    dialogDelete.value = true
};

const deleteItemConfirm = () => {
    deleteVehicle(formItem.value.id)
    closeDelete()
};

const closeDelete = () => {
    dialogDelete.value = false
};

const deleteVehicle = async (id) => {
  try {
    const response = await axios.delete(`/vehicles/delete/${id}`);
    vehicles.value = vehicles.value.filter(vehicle => vehicle.id !== id);
  } catch (err) {
    console.error('Error deleting vehicle:', err);
    error.value = err.message || 'An error occurred while deleting the vehicle';
  }
};

// Fetch vehicles when component is mounted
onMounted(() => {
  fetchVehicles();
  fetchTypes();
});
</script>