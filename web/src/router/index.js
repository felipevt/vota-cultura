import Vue from 'vue';
import Router from 'vue-router';
import NProgress from 'nprogress';
import { publicRoute, protectedRoute } from './config';
import 'nprogress/nprogress.css';

const routes = publicRoute.concat(protectedRoute);

Vue.use(Router);
const router = new Router({
  mode: 'history',
  linkActiveClass: 'active',
  routes,
});
// router gards
router.beforeEach((to, from, next) => {
  NProgress.start();
  // auth route is authenticated
  next();
});

router.afterEach(() => {
  NProgress.done();
});

export default router;
