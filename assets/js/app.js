/* Fpesa - App JS */
document.addEventListener('DOMContentLoaded',function(){
  if(typeof lucide!=='undefined')lucide.createIcons();
  // Auto-dismiss flash
  document.querySelectorAll('.flash-msg').forEach(function(el){setTimeout(function(){el.style.opacity='0';el.style.transform='translateY(-10px)';el.style.transition='all .4s';setTimeout(function(){el.remove()},400)},6000)});
  // Smooth scroll
  document.querySelectorAll('a[href^="#"]').forEach(function(a){a.addEventListener('click',function(e){e.preventDefault();var t=document.querySelector(a.getAttribute('href'));if(t)t.scrollIntoView({behavior:'smooth',block:'start'})})});
  // PWA install
  var dp;window.addEventListener('beforeinstallprompt',function(e){e.preventDefault();dp=e;var b=document.getElementById('pwaInstallBtn');if(b){b.classList.remove('hidden');b.onclick=function(){dp.prompt();dp.userChoice.then(function(){b.classList.add('hidden');dp=null})}}});
});
function formatMoney(n){return'KSH '+Math.round(n).toLocaleString()}
function calcEMI(p,r,n){var m=(r/100)/12;return m>0?p*m*Math.pow(1+m,n)/(Math.pow(1+m,n)-1):p/n}
