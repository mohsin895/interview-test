import { createWebHistory, createRouter } from 'vue-router'


const router = createRouter({
    history: createWebHistory(),
    routes:[
        {
            path: '/',
            component:()=>import("@/pages/Home.vue")
        },
        {
            path: '/category',
            component:()=>import("@/pages/Category.vue")
        },
        {
            path: '/product',
            name:'productList',
            component:()=>import("@/pages/Product.vue")
        },
        {
            path: '/product/create',
            name:'ProductCreate',
            component:()=>import("@/pages/ProductCreate.vue")
        },
        {
            path: '/product/:dataId/details',
            name:'ProductDetails',
            component:()=>import("@/pages/ProductView.vue")
        },
        {
            path: '/product/:dataId/edit',
            name:'ProductEdit',
            component:()=>import("@/pages/ProductEdit.vue")
        },
    ]
})


export default router;
