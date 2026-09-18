
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
    'index.csr.html': {size: 7790, hash: 'ad30b8f190e55b071cec6fc5d1ea359697c2e971959e1b54b3ab2c9472b097d7', text: () => import('./assets-chunks/index_csr_html.mjs').then(m => m.default)},
    'index.server.html': {size: 1015, hash: '1202a0cac4f6955134c3eb6db9a7da29697ba54a82dba6b800504260c10b95f7', text: () => import('./assets-chunks/index_server_html.mjs').then(m => m.default)},
    'index.html': {size: 14146, hash: '5d96aaf3c4a154404e804e6e23e36172ac629be7649f5b8ec48c5b31a944d7a9', text: () => import('./assets-chunks/index_html.mjs').then(m => m.default)},
    'styles-UEC5XGC6.css': {size: 21915, hash: '/9H1pjq6hQg', text: () => import('./assets-chunks/styles-UEC5XGC6_css.mjs').then(m => m.default)}
  },
};
