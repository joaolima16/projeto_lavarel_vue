<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import ProductService from '@/services/ProductService';

const router = useRouter();

const name = ref('');
const description = ref('');
const priceCost = ref('');
const priceSale = ref('');
const images = ref<FileList | null>(null);

const saving = ref(false);
const error = ref('');
const validationError = ref('');

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files) {
    images.value = target.files;
  }
};

const handleSave = async () => {
  validationError.value = '';
  error.value = '';

  if (Number(priceSale.value) <= Number(priceCost.value) * 1.10) {
    validationError.value = 'O preço de venda deve ser maior que o preço de custo + 10%.';
    return;
  }

  saving.value = true;

  const formData = new FormData();
  formData.append('name', name.value);
  formData.append('description', description.value);
  formData.append('price_cost', priceCost.value);
  formData.append('price_sale', priceSale.value);

  if (images.value) {
    for (let i = 0; i < images.value.length; i++) {
      formData.append('images[]', images.value[i]);
    }
  }

  try {
    await ProductService.create(formData);
    router.push('/products');
  } catch (e: any) {
   
    if (e.response && e.response.data && e.response.data.message) {
        error.value = e.response.data.message;
        if(e.response.data.errors) {
             error.value += ' ' + JSON.stringify(e.response.data.errors);
        }
    } else {
        error.value = 'Erro ao cadastrar produto.';
    }
    console.error(e);
  } finally {
    saving.value = false;
  }
};
</script>

<template>
  <div class="edit-product-container">
    <div class="header">
      <h2>Novo Produto</h2>
      <button class="btn-back" @click="router.back()">Voltar</button>
    </div>

    <div class="form-container">
      <div v-if="error" class="error">{{ error }}</div>
      <div v-if="validationError" class="error">{{ validationError }}</div>
      
      <form @submit.prevent="handleSave">
        <div class="form-group">
          <label for="name">Nome:</label>
          <input id="name" v-model="name" required />
        </div>

        <div class="form-group">
          <label for="description">Descrição (Permitido: &lt;p&gt;, &lt;br&gt;, &lt;b&gt;, &lt;strong&gt;):</label>
          <textarea id="description" v-model="description" rows="4"></textarea>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="price_cost">Preço de Custo (R$):</label>
            <input id="price_cost" type="number" step="0.01" v-model="priceCost" required />
          </div>

          <div class="form-group">
            <label for="price_sale">Preço de Venda (R$):</label>
            <input id="price_sale" type="number" step="0.01" v-model="priceSale" required />
          </div>
        </div>

        <div class="form-group">
          <label for="images">Imagens:</label>
          <input id="images" type="file" multiple @change="handleFileChange" accept="image/png, image/jpeg" />
        </div>

        <div class="actions">
          <button type="submit" class="btn-save" :disabled="saving">
            {{ saving ? 'Salvando...' : 'Cadastrar' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<style scoped src="@/assets/css/EditProductPage.css"></style>
