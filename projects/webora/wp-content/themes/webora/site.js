document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('.menu-toggle');
  const navigation = document.querySelector('.nav-links');
  toggle?.addEventListener('click', () => {
    const open = toggle.getAttribute('aria-expanded') !== 'true';
    toggle.setAttribute('aria-expanded', String(open));
    toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
    toggle.textContent = open ? '×' : '☰';
    navigation.classList.toggle('open', open);
  });
  navigation?.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
    navigation.classList.remove('open');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', 'Open menu');
    toggle.textContent = '☰';
  }));
  document.querySelectorAll('[data-filter]').forEach(button => button.addEventListener('click', () => {
    document.querySelectorAll('[data-filter]').forEach(item => {
      item.classList.toggle('active', item === button);
      item.setAttribute('aria-pressed', String(item === button));
    });
    document.querySelectorAll('.project-card').forEach(card => {
      card.hidden = button.dataset.filter !== 'all' && card.dataset.category !== button.dataset.filter;
    });
  }));
  document.querySelectorAll('[data-language]').forEach(button => button.addEventListener('click', () => {
    document.querySelectorAll('[data-language]').forEach(item => {
      item.classList.toggle('active', item === button);
      item.setAttribute('aria-pressed', String(item === button));
    });
    document.querySelectorAll('[data-bio]').forEach(bio => bio.hidden = bio.dataset.bio !== button.dataset.language);
  }));
  document.querySelectorAll('[data-service]').forEach(link => link.addEventListener('click', () => {
    document.querySelector('#service').value = link.dataset.service;
  }));
  const projects = {
    olive: {title:'Olive & Earth', description:'A warm, nature-inspired storefront concept for a lifestyle brand. The direction brings together earthy colors, editorial typography and a clear path from discovery to shopping.', skills:['WordPress direction', 'E-commerce', 'Responsive layout']},
    rose: {title:'Maison Rose', description:'An elegant business website concept with a soft palette and expressive typography. Designed to give a creative brand a memorable first impression and a clear way to connect.', skills:['WordPress direction', 'Brand website', 'Editorial design']},
    studio: {title:'Studio / 08', description:'A bold front-end portfolio concept for a digital studio. Strong typography and a contrasting palette put the work at the center of the experience.', skills:['Front-end direction', 'Portfolio', 'Responsive layout']}
  };
  const modal = document.querySelector('#project-modal');
  document.querySelectorAll('[data-project]').forEach(button => button.addEventListener('click', () => {
    const project = projects[button.dataset.project];
    document.querySelector('#project-title').textContent = project.title;
    document.querySelector('#project-description').textContent = project.description;
    document.querySelector('#project-skills').replaceChildren(...project.skills.map(text => {
      const span = document.createElement('span'); span.textContent = text; return span;
    }));
    modal.showModal();
  }));
  document.querySelector('.close-modal')?.addEventListener('click', () => modal.close());
  modal?.addEventListener('click', event => {
    if (event.target === modal) {
      const box = modal.getBoundingClientRect();
      if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) modal.close();
    }
  });
  document.querySelector('.contact-form')?.addEventListener('submit', event => {
    event.preventDefault();
    const form = event.currentTarget;
    if (!form.reportValidity()) return;
    const values = new FormData(form);
    const subject = `Webora project inquiry — ${values.get('service') || 'New project'}`;
    const body = `Hello Webora,\n\nName: ${values.get('name')}\nEmail: ${values.get('email')}\nService: ${values.get('service') || 'Not selected'}\n\nProject details:\n${values.get('message')}\n`;
    window.location.href = `mailto:${form.dataset.email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    form.querySelector('.form-note').textContent = 'Your email draft is ready to open. If no email app opens, use the email icon to contact me directly.';
  });
});
