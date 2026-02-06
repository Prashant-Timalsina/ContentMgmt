// src/helper/quasaDialogs.js
import { useQuasar } from 'quasar'

export function useQuasaMsgs() {
  const $q = useQuasar()

  const notify = (type, message) => $q.notify({
    type,
    message,
    position: 'top',
    timeout: 2500
  })

  return {
    success: msg => notify('positive', msg),
    error:   msg => notify('negative', msg),
    warning: msg => notify('warning', msg),
    info:    msg => notify('info', msg),
  }
}
