const CACHE='fpesa-v1';
const URLS=['/','/auth/login.php','/auth/register.php','/pages/loans.php','/pages/loan-calculator.php','/pages/about.php','/pages/contact.php'];

self.addEventListener('install',e=>{e.waitUntil(caches.open(CACHE).then(c=>c.addAll(URLS).catch(()=>{})));self.skipWaiting()});
self.addEventListener('activate',e=>{e.waitUntil(caches.keys().then(ks=>Promise.all(ks.filter(k=>k!==CACHE).map(k=>caches.delete(k)))));self.clients.claim()});
self.addEventListener('fetch',e=>{if(e.request.method!=='GET')return;e.respondWith(fetch(e.request).then(r=>{if(r.ok){const c=r.clone();caches.open(CACHE).then(ca=>ca.put(e.request,c))}return r}).catch(()=>caches.match(e.request).then(c=>c||new Response('Offline',{status:503}))))});
