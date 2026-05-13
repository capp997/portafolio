// PREMIUM DASHBOARD GRID SYSTEM
(function(){
 const grid=document.querySelector('.dashboard-widget-grid'); if(!grid) return;
 const storageKey='pv5_dashboard_layout_v1'; let dragged=null;
 loadLayout(); grid.querySelectorAll('.dashboard-widget').forEach(setupWidget);

 function setupWidget(widget){
  widget.setAttribute('draggable','true');
  if(!widget.querySelector('.widget-tools')){
   const tools=document.createElement('div');
   tools.className='widget-tools';
   tools.innerHTML='<button type="button" class="widget-drag-handle" title="Mover">☰</button><button type="button" data-size="3" title="Pequeño">S</button><button type="button" data-size="6" title="Mediano">M</button><button type="button" data-size="12" title="Grande">L</button><button type="button" data-hide="1" title="Ocultar">×</button>';
   widget.appendChild(tools);
  }
  widget.addEventListener('dragstart',()=>{dragged=widget; widget.classList.add('dragging')});
  widget.addEventListener('dragend',()=>{widget.classList.remove('dragging'); dragged=null; saveLayout()});
  widget.querySelectorAll('[data-size]').forEach(btn=>btn.addEventListener('click',e=>{e.preventDefault();e.stopPropagation();setWidgetSize(widget,btn.dataset.size);saveLayout();toast('Layout actualizado')}));
  const hideBtn=widget.querySelector('[data-hide]');
  if(hideBtn){hideBtn.addEventListener('click',e=>{e.preventDefault();e.stopPropagation();widget.classList.add('hidden-widget');updatePicker();saveLayout();toast('Widget ocultado')})}
 }

 grid.addEventListener('dragover',e=>{e.preventDefault(); if(!dragged) return; const after=getAfterElement(grid,e.clientY); if(after==null){grid.appendChild(dragged)}else{grid.insertBefore(dragged,after)}});

 window.pv5SaveDashboardLayout=function(){saveLayout();toast('Layout guardado')};
 window.pv5ResetDashboardLayout=function(){localStorage.removeItem(storageKey);location.reload()};
 window.pv5ToggleWidgetPicker=function(){const picker=document.querySelector('.widget-picker'); if(picker) picker.classList.toggle('open'); updatePicker()};
 window.pv5ShowWidget=function(id){const widget=grid.querySelector(`[data-widget-id="${id}"]`); if(widget){widget.classList.remove('hidden-widget');saveLayout();updatePicker();toast('Widget restaurado')}};

 function setWidgetSize(widget,size){widget.classList.remove('widget-size-3','widget-size-4','widget-size-6','widget-size-8','widget-size-12');widget.classList.add('widget-size-'+size)}
 function saveLayout(){const layout=[...grid.querySelectorAll('.dashboard-widget')].map(w=>{let size=3;['3','4','6','8','12'].forEach(s=>{if(w.classList.contains('widget-size-'+s)) size=s});return {id:w.dataset.widgetId,size,hidden:w.classList.contains('hidden-widget')}});localStorage.setItem(storageKey,JSON.stringify(layout))}
 function loadLayout(){const raw=localStorage.getItem(storageKey); if(!raw) return; try{const layout=JSON.parse(raw);layout.forEach(item=>{const widget=grid.querySelector(`[data-widget-id="${item.id}"]`); if(!widget) return; setWidgetSize(widget,item.size||3); if(item.hidden){widget.classList.add('hidden-widget')}else{widget.classList.remove('hidden-widget')} grid.appendChild(widget)})}catch(e){}}
 function updatePicker(){const list=document.querySelector('.widget-picker-list'); if(!list) return; list.innerHTML=''; grid.querySelectorAll('.dashboard-widget').forEach(widget=>{const id=widget.dataset.widgetId; const title=widget.dataset.widgetTitle||id; const hidden=widget.classList.contains('hidden-widget'); const btn=document.createElement('button'); btn.type='button'; btn.className=hidden?'off':''; btn.textContent=hidden?'Mostrar '+title:'Ocultar '+title; btn.addEventListener('click',()=>{if(hidden){widget.classList.remove('hidden-widget')}else{widget.classList.add('hidden-widget')} saveLayout(); updatePicker()}); list.appendChild(btn)})}
 function getAfterElement(container,y){const els=[...container.querySelectorAll('.dashboard-widget:not(.dragging):not(.hidden-widget)')]; return els.reduce((closest,child)=>{const box=child.getBoundingClientRect(); const offset=y-box.top-box.height/2; if(offset<0 && offset>closest.offset){return {offset,element:child}} return closest},{offset:Number.NEGATIVE_INFINITY}).element}
 function toast(msg){let el=document.querySelector('.workspace-toast'); if(!el){el=document.createElement('div');el.className='workspace-toast';document.body.appendChild(el)} el.textContent=msg; el.classList.add('show'); setTimeout(()=>el.classList.remove('show'),1700)}
 updatePicker();
})();
