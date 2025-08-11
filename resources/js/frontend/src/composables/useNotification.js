import {ref,reactive} from 'vue';
const notifications = ref([]);
let notificationId = 0;
export function useNotification() {
    const showNotification=(notification)=>{
        const id= ++notificationId;
        const defaultNotification = {
            id,
            type: 'info',
            title: '',
            message: '',
            duration: 5000,
            persistent: false,
            actions: []
        }
        const newNotification = {...defaultNotification, ...notification};
        notifications.value.push(newNotification);
        if(!newNotification.persistent && newNotification.duration > 0) {
            setTimeout(() => {
                dismissNotification(id);
            }, newNotification.duration);
        }
        return id;
    }
    const showSuccess = (message, title = 'Success', options = {}) => {
        return showNotification({
        type: 'success',
        title,
        message,
        ...options
        })
    }
    const showError = (message, title = 'Error', options = {}) => {
        return showNotification({
        type: 'error',
        title,
        message,
        duration: 8000, 
        ...options
        })
    }


    const showWarning = (message, title = 'Warning', options = {}) => {
        return showNotification({
        type: 'warning',
        title,
        message,
        ...options
        })
    }
    const showInfo=(message,title='Info',options={})=>{
        return showNotification({
            type: 'info',
            title,
            message,
            ...options
        })
    }
    const dismissNotification=(id)=>{
        const index= notification.value.findIndex(n=>n.id===id)
        if(index !==-1){
            notifications.value.splice(index, 1);
        }
    }
    const clearAll=()=>{
        notifications.value = [];
    }
    const clearByType=(type)=>{
        notifications.value=notifications.value.filter(n => n.type !== type);
    }
    const showConfirm = (message, title = 'Confirm', options = {}) => {
    return new Promise((resolve) => {
      const id = showNotification({
        type: 'warning',
        title,
        message,
        persistent: true,
        actions: [
          {
            label: options.cancelLabel || 'Cancel',
            action: () => {
              dismissNotification(id)
              resolve(false)
            },
            style: 'secondary'
          },
          {
            label: options.confirmLabel || 'Confirm',
            action: () => {
              dismissNotification(id)
              resolve(true)
            },
            style: 'primary'
          }
        ],
        ...options
      })
    })
  }

  /**
   * Show validation errors
   */
  const showValidationErrors = (errors, title = 'Validation Errors') => {
    const errorMessages = []
    
    if (typeof errors === 'object') {
      Object.keys(errors).forEach(field => {
        const fieldErrors = Array.isArray(errors[field]) ? errors[field] : [errors[field]]
        fieldErrors.forEach(error => {
          errorMessages.push(`${field}: ${error}`)
        })
      })
    } else {
      errorMessages.push(errors)
    }
    
    return showError(errorMessages.join('\n'), title, {
      duration: 10000 // Validation errors stay longer
    })
  }

  return {
    // State
    notifications,
    
    // Methods
    showNotification,
    showSuccess,
    showError,
    showWarning,
    showInfo,
    showConfirm,
    showValidationErrors,
    dismissNotification,
    clearAll,
    clearByType
  }

}