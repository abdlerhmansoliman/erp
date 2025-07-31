import Swal from 'sweetalert2'

export function useConfirmDialog() {
  const confirmDelete = async (itemName) => {
    const result = await Swal.fire({
      title: 'are you sure',
      text: `Are you sure you want to delete ${itemName}?`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: ' yes',
      cancelButtonText: 'cancel',
    });

    return result.isConfirmed;
  };

  return { confirmDelete };
}
