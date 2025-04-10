<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Category</title>
</head>
<body style="padding: 20px;">
    <form id="quickCategoryForm">
        @csrf
        <div style="margin-bottom: 15px;">
            <label for="name" style="display: block; margin-bottom: 5px;">Name</label>
            <input type="text" 
                   name="name" 
                   id="name" 
                   style="width: 100%; padding: 5px; border: 1px solid #ccc;"
                   required>
        </div>

        <div style="margin-bottom: 15px;">
            <label for="description" style="display: block; margin-bottom: 5px;">Description</label>
            <textarea name="description" 
                      id="description" 
                      rows="3"
                      style="width: 100%; padding: 5px; border: 1px solid #ccc;"></textarea>
        </div>

        <div>
            <button type="button" 
                    onclick="window.parent.closeModal()"
                    style="padding: 5px 15px; background: #eee; border: 1px solid #ccc; margin-right: 10px;">
                Cancel
            </button>
            <button type="submit"
                    style="padding: 5px 15px; background: #4a5568; color: white; border: 1px solid #4a5568;">
                Create Category
            </button>
        </div>
    </form>

    <script>
    document.getElementById('quickCategoryForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitButton = this.querySelector('button[type="submit"]');
        const originalContent = submitButton.innerHTML;
        
        try {
            submitButton.disabled = true;
            submitButton.innerHTML = 'Creating...';
            
            const response = await fetch('{{ route("admin.categories.store.quick") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    name: document.getElementById('name').value,
                    description: document.getElementById('description').value
                })
            });

            const data = await response.json();
            
            if (data.success) {
                window.parent.closeModal();
                window.parent.location.reload();
            } else {
                throw new Error(data.message || 'Failed to create category');
            }
        } catch (error) {
            alert(error.message);
            submitButton.disabled = false;
            submitButton.innerHTML = originalContent;
        }
    });
    </script>
</body>
</html>
