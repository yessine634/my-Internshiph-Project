document.addEventListener('DOMContentLoaded', function() {
  fetch('get_categories.php')
    .then(response => response.json())
    .then(categories => {
      const grid = document.getElementById('categories-grid');
      grid.innerHTML = '';
      categories.forEach(category => {
        const a = document.createElement('a');
        a.href = `categories-stock.php?id=${category.id}&name=${category.name}`;  
        a.className = "block p-4 bg-blue-50 rounded-lg shadow hover:bg-blue-100 transition duration-200 cursor-pointer mb-4";
        a.innerHTML = `
          <h3 class="text-lg font-semibold text-blue-800">${category.name}</h3>
          <p class="text-sm text-blue-600">${category.description}</p>
        `;
        grid.appendChild(a);
      });
    });
});
