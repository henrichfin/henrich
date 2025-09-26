const API_BASE = '/henrich/backend';

async function fetchJSON(url, options) {
	const res = await fetch(url, options);
	if (!res.ok) {
		const err = await res.json().catch(() => ({ error: 'Request failed' }));
		throw new Error(err.error || 'Request failed');
	}
	return res.json();
}

function renderTask(task) {
	const li = document.createElement('li');
	li.className = 'task';
	li.dataset.id = task.id;
	li.innerHTML = `
		<div>
			<div class="task-title">${task.title}</div>
			<div class="task-description">${task.description || 'No description provided'}</div>
		</div>
		<div class="task-meta">
			<span class="badge ${task.status}">${task.status.replace('_', ' ')}</span>
			<div class="task-date">${new Date(task.created_at).toLocaleDateString()}</div>
		</div>
		<div class="actions">
			<button class="button button--primary" data-action="toggle">Update Status</button>
			<button class="button button--danger" data-action="delete">Delete</button>
		</div>
	`;
	return li;
}

async function loadTasks() {
	const list = document.getElementById('task-list');
	list.innerHTML = '';
	
	try {
		const tasks = await fetchJSON(`${API_BASE}/api/tasks`);
		if (tasks.length === 0) {
			list.innerHTML = `
				<div class="empty-state">
					<h3>No tasks yet</h3>
					<p>Create your first task using the form above</p>
				</div>
			`;
			return;
		}
		tasks.forEach(t => list.appendChild(renderTask(t)));
	} catch (error) {
		list.innerHTML = `
			<div class="empty-state">
				<h3>Error loading tasks</h3>
				<p>Please check your connection and try again</p>
			</div>
		`;
		console.error('Error loading tasks:', error);
	}
}

function nextStatus(current) {
	if (current === 'pending') return 'in_progress';
	if (current === 'in_progress') return 'done';
	return 'pending';
}

async function handleListClick(e) {
	const btn = e.target.closest('button');
	if (!btn) return;
	const li = btn.closest('li.task');
	const id = li.dataset.id;
	
	if (btn.dataset.action === 'delete') {
		if (confirm('Are you sure you want to delete this task?')) {
			try {
				await fetch(`${API_BASE}/api/tasks/${id}`, { method: 'DELETE' });
				li.remove();
			} catch (error) {
				alert('Failed to delete task. Please try again.');
				console.error('Delete error:', error);
			}
		}
		return;
	}
	
	if (btn.dataset.action === 'toggle') {
		try {
			const badge = li.querySelector('.badge');
			const current = badge.textContent.trim().replace(' ', '_');
			const updated = await fetchJSON(`${API_BASE}/api/tasks/${id}`, {
				method: 'PUT',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ status: nextStatus(current) }),
			});
			const fresh = renderTask(updated);
			li.replaceWith(fresh);
		} catch (error) {
			alert('Failed to update task status. Please try again.');
			console.error('Update error:', error);
		}
	}
}

async function handleFormSubmit(e) {
	e.preventDefault();
	const title = document.getElementById('title').value.trim();
	const description = document.getElementById('description').value.trim();
	const status = document.getElementById('status').value;
	
	if (!title) {
		alert('Please enter a task title');
		return;
	}
	
	try {
		const created = await fetchJSON(`${API_BASE}/api/tasks`, {
			method: 'POST',
			headers: { 'Content-Type': 'application/json' },
			body: JSON.stringify({ title, description, status }),
		});
		document.getElementById('task-list').prepend(renderTask(created));
		e.target.reset();
	} catch (error) {
		alert('Failed to create task. Please try again.');
		console.error('Create error:', error);
	}
}

window.addEventListener('DOMContentLoaded', () => {
	document.getElementById('task-form').addEventListener('submit', handleFormSubmit);
	document.getElementById('task-list').addEventListener('click', handleListClick);
	loadTasks().catch(console.error);
});