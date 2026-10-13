document.querySelectorAll('nav a').forEach(a=>{if(new URL(a.href).pathname===location.pathname)a.setAttribute('aria-current','page');else a.removeAttribute('aria-current');});
const recipient=(document.querySelector('.contact-info a[href^="mailto:"]')||document.querySelector('.footer-links a[href^="mailto:"]'))?.getAttribute('href')?.slice(7)||'tereza.machonova@4fin.cz';
const form=document.getElementById('contact-form');
if(form){const topics={bydleni:'Hypotéky a financování bydlení',reality:'Reality a bydlení',pojisteni:'Životní a majetkové pojištění',investice:'Investice',penze:'Příprava na penzi',plan:'Finanční plán'};const topic=topics[new URLSearchParams(location.search).get('tema')];if(topic)document.getElementById('topic').value=topic;
form.addEventListener('submit',event=>{event.preventDefault();const data=new FormData(form);const subject='Dotaz z webu: '+data.get('topic');const body=`Jméno: ${data.get('name')}\nE-mail: ${data.get('email')}\nTelefon: ${data.get('phone')||'Neuveden'}\n\n${data.get('message')}`;document.getElementById('form-status').textContent='Zpráva je připravena. Dokončete odeslání ve svém e-mailovém programu. Pokud se neotevřel, napište přímo na tereza.machonova@4fin.cz.';location.href='mailto:'+recipient+'?subject='+encodeURIComponent(subject)+'&body='+encodeURIComponent(body);});}

// Replay reveals on re-entry from either direction; reset only outside the viewport.
if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && 'IntersectionObserver' in window) {
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting && entry.intersectionRatio >= 0.08) {
        entry.target.classList.add('is-visible');
      } else if (entry.boundingClientRect.bottom <= 0 || entry.boundingClientRect.top >= window.innerHeight) {
        entry.target.classList.remove('is-visible');
      }
    });
  }, {threshold: [0, 0.08]});
  document.querySelectorAll('.section-title, .service-card, .service-preview, .split, .contact-layout, .steps article').forEach((element, index) => {
    const bounds = element.getBoundingClientRect();
    if (bounds.top < window.innerHeight && bounds.bottom > 0) element.classList.add('is-visible');
    element.classList.add('reveal');
    element.style.setProperty('--reveal-delay', `${(index % 3) * 70}ms`);
    observer.observe(element);
  });
}
