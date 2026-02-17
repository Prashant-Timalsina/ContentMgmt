import { useQuasar } from 'quasar'

export function useRolesTheme() {
  const $q = useQuasar()

  const shade = (light, dark) => ($q.dark.isActive ? dark : light)

  /**
   * Role / permission request type
   */
  const requestType = (type) => {
    const map = {
      role: {
        label: 'Role',
        color: shade('primary', 'blue-4'),
      },
      permission: {
        label: 'Permission',
        color: shade('teal-8', 'teal-4'),
      },
    }

    return (
      map[type] ?? {
        label: type,
        color: shade('grey-5', 'grey-7'),
      }
    )
  }

  return {
    requestType,
  }
}
