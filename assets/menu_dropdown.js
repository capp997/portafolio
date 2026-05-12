document.addEventListener('DOMContentLoaded', function(){
  document.querySelectorAll('.menu-parent').forEach(function(btn){
    btn.addEventListener('click', function(){
      const group = btn.closest('.menu-group');
      if(!group) return;
      group.classList.toggle('open');
    });
  });
});
