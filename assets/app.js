function toast(msg){document.getElementById('toastMsg').textContent=msg;bootstrap.Toast.getOrCreateInstance(document.getElementById('toastBox'),{delay:2200}).show();}
document.addEventListener('click',async ev=>{
  const st=ev.target.closest('[data-step]');
  if(st){const g=st.closest('.input-group'),i=g.querySelector('input');
    const min=parseInt(i.min||0),max=parseInt(i.max||9999);
    i.value=Math.max(min,Math.min(max,(parseInt(i.value)||0)+parseInt(st.dataset.step)));
    if(i.dataset.auto)i.form.submit();return;}
  const b=ev.target.closest('[data-add]');
  if(b){ev.preventDefault();const fd=new FormData();
    fd.append('aksi','tambah');fd.append('ajax','1');fd.append('id',b.dataset.add);
    const q=document.getElementById('qty');fd.append('jumlah',q?q.value:1);
    try{const r=await fetch(BASE+'keranjang.php',{method:'POST',body:fd});const j=await r.json();
      const c=document.getElementById('cartCount');c.textContent=j.jumlah;c.classList.remove('bump');void c.offsetWidth;c.classList.add('bump');
      toast('Produk ditambahkan ke keranjang');}catch(e){toast('Gagal menambah, coba lagi');}}
});
