document.addEventListener("DOMContentLoaded", function(){
  document.querySelectorAll(".menu-parent").forEach(btn => {
    btn.addEventListener("click", () => {
      const group = btn.closest(".menu-group");
      if(group){ group.classList.toggle("open"); }
    });
  });
});
