document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.querySelector('#menu-toggle');
  const links = document.querySelector('#nav-links');
  if (toggle && links) {
    toggle.addEventListener('click', () => {
      const open = links.classList.toggle('open');
      toggle.setAttribute('aria-expanded', String(open));
    });
    links.querySelectorAll('a').forEach(link => link.addEventListener('click', () => {
      links.classList.remove('open');
      toggle.setAttribute('aria-expanded', 'false');
    }));
  }
  const searchButton = document.querySelector('#open-search');
  const searchPanel = document.querySelector('#search-panel');
  if (searchButton && searchPanel) {
    searchButton.addEventListener('click', () => {
      searchPanel.hidden = !searchPanel.hidden;
      searchButton.setAttribute('aria-expanded', String(!searchPanel.hidden));
      if (!searchPanel.hidden) searchPanel.querySelector('input[type=search]').focus();
    });
  }
});
