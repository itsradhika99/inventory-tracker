document.addEventListener('DOMContentLoaded', function () {
  fetchItems();

  document.getElementById('itemForm').addEventListener('submit', function (e) {
    e.preventDefault();
    addItem();
  });

  // Debounce search input for better UX
  let searchTimeout;
  document.getElementById('searchInput').addEventListener('input', function () {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
      searchItems();
    }, 500);
  });

  // Load items on filter change
  document.getElementById('stockFilter').addEventListener('change', filterByStock);
});

function fetchItems(query = '') {
  const url = query ? `api/item/search.php?s=${encodeURIComponent(query)}` : 'api/item/read.php';

  fetch(url)
    .then(res => res.json())
    .then(data => {
      const itemList = document.getElementById('itemList');
      itemList.innerHTML = '';

      if (data.records && data.records.length > 0) {
        displayItems(data.records);
      } else {
        itemList.innerHTML = `<p class="no-items">No items found.</p>`;
      }
    });
}

function displayItems(records) {
  const itemList = document.getElementById('itemList');
  itemList.innerHTML = '';

  if (records.length > 0) {
    records.forEach(item => {
      const div = document.createElement('div');
      div.classList.add('item-card');
      div.innerHTML = `
        <div class="item-header">
          <h3>${item.name} <span class="qty">(${item.quantity})</span></h3>
          <p>${item.description || 'No description.'}</p>
          <div class="actions">
            <button onclick="incrementQty(${item.id}, ${item.quantity})">➕</button>
            <button onclick="decrementQty(${item.id}, ${item.quantity})">➖</button>
            <button onclick="editItem(${item.id}, '${escapeQuotes(item.name)}', '${escapeQuotes(item.description)}', ${item.quantity})">✏️ Edit</button>
            <button onclick="deleteItem(${item.id})">🗑️ Delete</button>
          </div>
        </div>
      `;
      itemList.appendChild(div);
    });
  } else {
    itemList.innerHTML = `<p class="no-items">No items found.</p>`;
  }
}

function escapeQuotes(str) {
  if (!str) return '';
  return str.replace(/'/g, "\\'").replace(/"/g, '\\"');
}

function incrementQty(id, currentQty) {
  updateQty(id, currentQty + 1);
}

function decrementQty(id, currentQty) {
  if (currentQty > 1) updateQty(id, currentQty - 1);
}

function updateQty(id, newQty) {
  fetch('api/item/update.php', {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, quantity: newQty })
  })
  .then(res => res.json())
  .then(() => {
    fetchItems();
  });
}

function deleteItem(id) {
  if (confirm('Are you sure you want to delete this item?')) {
    fetch('api/item/delete.php', {
      method: 'DELETE',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id })
    })
    .then(res => res.json())
    .then(() => {
      fetchItems();
    });
  }
}

function editItem(id, currentName, currentDesc, currentQty) {
  const newName = prompt("Enter new name:", currentName);
  if (!newName) return alert("Name cannot be empty.");

  const newDesc = prompt("Enter new description:", currentDesc) || '';

  let newQty = prompt("Enter new quantity:", currentQty);
  newQty = parseInt(newQty);
  if (isNaN(newQty) || newQty < 0) return alert("Please enter a valid quantity.");

  fetch('api/item/update.php', {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ id, name: newName, description: newDesc, quantity: newQty })
  })
  .then(res => res.json())
  .then(() => {
    fetchItems();
  });
}

function filterByStock() {
  const filter = document.getElementById('stockFilter').value;

  fetch('api/item/read.php')
    .then(res => res.json())
    .then(data => {
      if (!data.records) return displayItems([]);

      let filtered = data.records;

      if (filter === 'low') {
        filtered = filtered.filter(item => item.quantity <= 5);
      } else if (filter === 'medium') {
        filtered = filtered.filter(item => item.quantity > 5 && item.quantity <= 20);
      } else if (filter === 'high') {
        filtered = filtered.filter(item => item.quantity > 20);
      }

      displayItems(filtered);
    });
}

function searchItems() {
  const keywords = document.getElementById('searchInput').value.trim();
  if (keywords === '') {
    fetchItems();
  } else {
    fetchItems(keywords);
  }
}

function addItem() {
  const name = document.getElementById('itemName').value.trim();
  const description = document.getElementById('itemDesc').value.trim();
  const quantity = parseInt(document.getElementById('itemQty').value.trim());

  if (!name || !description || isNaN(quantity) || quantity < 0) {
    alert('Please enter valid name, description, and quantity.');
    return;
  }

  fetch('api/item/create.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name, description, quantity })
  })
  .then(res => res.json())
  .then(data => {
    alert(data.message || 'Item added!');
    document.getElementById('itemForm').reset();
    fetchItems();
  });
}
