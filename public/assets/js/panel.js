// sidebar collapse/expand (desktop) + show/hide (mobile)
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainArea = document.getElementById('mainArea');
    const overlay = document.getElementById('overlay');
    if (window.innerWidth < 992) {
        sidebar.classList.toggle('show');
        overlay.classList.toggle('show');
    } else {
        sidebar.classList.toggle('collapsed');
        mainArea.classList.toggle('collapsed');
    }
}

function closeMobileSidebar() {
    document.getElementById('sidebar').classList.remove('show');
    document.getElementById('overlay').classList.remove('show');
}

// switches visible section + highlights active nav item
function showSection(id, el) {
    document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
    document.getElementById(id).classList.add('active');
    document.querySelectorAll('.nav-link-item').forEach(n => n.classList.remove('active'));
    el.classList.add('active');
    closeMobileSidebar();
}

// removes a table row after confirmation
function deleteRow(btn) {
    if (confirm('Yeh entry delete karna hai?')) {
        btn.closest('tr').remove();
    }
}

// adds a new row to the content table from the modal form
function addContentRow(e) {
    e.preventDefault();
    const title = document.getElementById('newTitle').value;
    const slug = document.getElementById('newSlug').value;
    const status = document.getElementById('newStatus').value;
    const badgeClass = status === 'Published' ? 'badge-published' : 'badge-draft';

    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${title}</td>
      <td class="text-muted">${slug}</td>
      <td><span class="badge-status ${badgeClass}">${status}</span></td>
      <td>Just now</td>
      <td class="text-end">
        <button class="row-action-btn"><i class="bi bi-pencil"></i></button>
        <button class="row-action-btn delete" onclick="deleteRow(this)"><i class="bi bi-trash"></i></button>
      </td>`;
    document.getElementById('contentTableBody').prepend(row);

    e.target.reset();
    bootstrap.Modal.getInstance(document.getElementById('contentModal')).hide();
}

// demo save handler for settings form
function saveSettings(e) {
    e.preventDefault();
    alert('Settings saved! (yahan aap apna backend API call lagayenge)');
}

// Automatically activate tab section based on URL query parameter (?section=...)
document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const sectionId = params.get('section');
    if (sectionId) {
        const matchingLink = document.querySelector(`.nav-link-item[data-section="${sectionId}"]`);
        const matchingSection = document.getElementById(sectionId);
        if (matchingLink && matchingSection) {
            showSection(sectionId, matchingLink);
        }
    }
});