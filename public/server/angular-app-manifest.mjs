
export default {
  bootstrap: () => import('./main.server.mjs').then(m => m.default),
  inlineCriticalCss: true,
  baseHref: '/',
  locale: undefined,
  routes: [
  {
    "renderMode": 2,
    "route": "/"
  }
],
  entryPointToBrowserMapping: undefined,
  assets: {
    'index.csr.html': {size: 7790, hash: '659422295a318f16cf6723397c38f920310cdd3bb205a491978b361007b9371a', text: () => import('./assets-chunks/index_csr_html.mjs').then(m => m.default)},
    'index.server.html': {size: 1015, hash: '2ec28d979be22b378c3e361a329d89d7194841a8b657dea062e25f5a8dc56db4', text: () => import('./assets-chunks/index_server_html.mjs').then(m => m.default)},
    'index.html': {size: 14146, hash: '6fefde5e5fa72c1b6686d3d4b334a46d2db4bd1670a6f9c2b964800c196ffb89', text: () => import('./assets-chunks/index_html.mjs').then(m => m.default)},
    'styles-UEC5XGC6.css': {size: 21915, hash: '/9H1pjq6hQg', text: () => import('./assets-chunks/styles-UEC5XGC6_css.mjs').then(m => m.default)}
  },
};
