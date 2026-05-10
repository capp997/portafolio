if('serviceWorker' in navigator){
window.addEventListener('load',()=>{
navigator.serviceWorker.register('/portafolio-dashboard-v4/service-worker.js')
.then(reg=>console.log('SW registrado',reg))
.catch(err=>console.log('SW error',err));
});
}

let deferredPrompt;

window.addEventListener('beforeinstallprompt',(e)=>{
e.preventDefault();
deferredPrompt=e;

const installBtn=document.getElementById('installAppBtn');

if(installBtn){
installBtn.style.display='inline-flex';

installBtn.addEventListener('click',async()=>{
deferredPrompt.prompt();
const { outcome } = await deferredPrompt.userChoice;
console.log(outcome);
deferredPrompt=null;
});
}
});
