# Sistema de Cadastro de Produtos (Projeto Amar)

Este projeto consiste em um sistema completo para gerenciamento de produtos, desenvolvido como parte de um teste técnico. O sistema permite autenticação de usuário e operações completas de CRUD (Criar, Ler, Atualizar, Deletar/Inativar) para produtos, incluindo upload de múltiplas imagens e validações rigorosas de regras de negócio.

## 🚀 Tecnologias Utilizadas

O projeto foi construído respeitando as versões e tecnologias solicitadas, utilizando uma arquitetura moderna e separada em microsserviços (Backend API e Frontend SPA).

### Frontend
- **Framework**: [Vue.js 3](https://vuejs.org/) (Composition API)
- **Linguagem**: [TypeScript](https://www.typescriptlang.org/)
- **Build Tool**: [Vite](https://vitejs.dev/)
- **Roteamento**: Vue Router
- **Estilização**: CSS Nativo / Scoped

### Backend
- **Linguagem**: [PHP](https://www.php.net/)
- **Framework**: [Laravel](https://laravel.com/)
- **Banco de Dados**: [MySQL](https://www.mysql.com/)
- **Autenticação**: Laravel Sanctum (API Tokens)

### Infraestrutura
- **Containerização**: Docker & Docker Compose
- **Servidor Web Frontend**: Nginx

---

## 🏗️ Arquitetura

O sistema segue uma arquitetura **Client-Server**, onde o Frontend e o Backend são aplicações desacopladas que se comunicam via API REST.

1.  **Backend (API RESTful)**:
    - **Controllers**: Gerenciam a entrada de requisições HTTP (`ProductController`, `AuthController`).
    - **Form Requests**: Camada dedicada à validação de dados antes de atingirem o controller, garantindo integridade.
    - **Resources**: Transformação de modelos em respostas JSON padronizzate (ex: ocultando campos sensíveis).
    - **Models & Migrations**: Definição da estrutura do banco de dados e relacionamentos.
    - **Storage**: Gerenciamento de arquivos (imagens) no disco local/público.

2.  **Frontend (SPA)**:
    - **Services**: Camada de serviço (`ProductService.ts`) que centraliza as chamadas à API usando Axios.
    - **Views/Pages**: Componentes de página (`LoginPage`, `ProductListPage`, `EditProductPage`, etc.) que consomem os serviços.
    - **Roteamento**: Controle de navegação e proteção de rotas (Guardas de navegação para login).

---

## 📋 Funcionalidades e Regras de Negócio

### 1. Autenticação
- Login de usuário único/padrão.
- Geração de Token de acesso via Sanctum.

### 2. Gestão de Produtos
- **Listagem**: Visualização tabular dos produtos.
- **Cadastro/Edição**:
    - Título, Preço de Custo, Preço de Venda, Descrição e Imagens.
    - **Regra de Preço**: O preço de venda deve ser superior ao preço de custo + 10%.
    - **Regra de Descrição**: Aceita HTML limitado (`<p>`, `<br>`, `<b>`, `<strong>`).
    - **Imagens**: Upload de múltiplas imagens (apenas JPG/PNG).
- **Inativação**: Remoção lógica ou física do produto.

---

## 🔌 Principais Endpoints da API

| Método | Endpoint | Descrição |
| :--- | :--- | :--- |
| `POST` | `/api/login` | Realiza login e retorna o Token de acesso. |
| `GET` | `/api/products` | Retorna a lista de produtos cadastrados. |
| `POST` | `/api/products` | Cria um novo produto (suporta upload de arquivos). |
| `GET` | `/api/products/{id}` | Retorna os detalhes de um produto. |
| `POST` | `/api/products/{id}` | Atualiza um produto (enviar `_method=PUT` no FormData). |
| `DELETE` | `/api/products/{id}` | Deleta/Inativa um produto. |

---

## 🛠️ Manual de Instalação e Execução (Docker)

Siga os passos abaixo para rodar o projeto em um ambiente Dockerizado.

### 1. Clonar e Configurar
```bash
git clone <url-do-repositorio>
cd projeto-amar
```

### 2. Subir os Containers
Na raiz do projeto (onde está o `docker-compose.yml`):
```bash
docker-compose up -d --build
```

### 3. Configuração Inicial do Backend
Execute os comandos abaixo para instalar dependências e preparar o banco de dados dentro do container:
```bash
# Instalar dependências do PHP
docker-compose exec backend composer install

# Gerar chave da aplicação
docker-compose exec backend php artisan key:generate

# Rodar migrações e seeders (cria o usuário padrão)
docker-compose exec backend php artisan migrate --seed

# Linkar o storage para imagens públicas
docker-compose exec backend php artisan storage:link
```

### 4. Acesso
- **Frontend**: Acesse `http://localhost:5173` (ou a porta configurada no seu docker-compose).
- **Backend/API**: Acessível em `http://localhost:8000`.
