<div class="action-buttons">
    <button class="action-btn btn-view" onclick="viewUser({{ $user->id }})" title="Ver">
        <i class="fas fa-globe"></i>
    </button>
    <button class="action-btn btn-edit" onclick="editUser({{ $user->id }})" title="Editar">
        <i class="fas fa-edit"></i>
    </button>
    <button class="action-btn btn-delete" onclick="deleteUser({{ $user->id }})" title="Eliminar">
        <i class="fas fa-trash"></i>
    </button>
</div>
