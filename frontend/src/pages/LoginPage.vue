<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import LoginService from '@/services/LoginService';

interface LoginResponse {
  token: string;
  user?: {
    id: number;
    name: string;
    email: string;
  };
}

const router = useRouter();

const email = ref<string>('');
const password = ref<string>('');
const loading = ref<boolean>(false);
const errorMessage = ref<string>('');

const handleLogin = async (): Promise<void> => {
  loading.value = true;
  errorMessage.value = '';

  try {
    const response = await LoginService.loginRequest(email.value, password.value);
      router.push('/products');
    
  } catch (error: any) {
    errorMessage.value = error.response?.data?.message || 'Erro ao conectar ao servidor.';
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="login-container">
    <div class="card">
      <h2>Login do Sistema</h2>
      <form @submit.prevent="handleLogin">
        <div class="field">
          <label>E-mail</label>
          <input v-model="email" type="email" required placeholder="joao@exemplo.com" />
        </div>
        
        <div class="field">
          <label>Senha</label>
          <input v-model="password" type="password" required placeholder="••••••••" />
        </div>

        <p v-if="errorMessage" class="error-msg">{{ errorMessage }}</p>

        <button :disabled="loading" type="submit">
          {{ loading ? 'Autenticando...' : 'Entrar' }}
        </button>
      </form>
    </div>
  </div>
</template>

<style scoped src="@/assets/css/LoginPage.css"></style>