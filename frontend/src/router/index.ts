import { createRouter, createWebHistory, type RouteRecordRaw } from "vue-router";

const routes: RouteRecordRaw[] =[
    {
        path: '/',
        name: 'Login',
        component: () => import('../pages/LoginPage.vue')
    },
    {
        path: '/products',
        name: 'Products',
        component: () => import('../pages/ProductListPage.vue')
    }
    ,
    {
        path: '/products/create',
        name: 'CreateProduct',
        component: () => import('../pages/CreateProductPage.vue')
    }
    ,
    {
        path: '/products/details/:id',
        name: 'ProductDetails',
        component: () => import('../pages/ProductDetailsPage.vue')
    },
    {
        path: '/products/edit/:id',
        name: 'EditProduct',
        component: () => import('../pages/EditProductPage.vue')
    }
]

const router = createRouter({
  history: createWebHistory(),
  routes
});


export default router;