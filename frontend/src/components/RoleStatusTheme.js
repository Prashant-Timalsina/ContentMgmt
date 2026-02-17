import { useQuasar } from 'quasar'

export function RoleStatusTheme() {
  const $q = useQuasar()

  const shade = (dark, light) => ($q.dark.isActive ? dark : light)

  const RoleStatus = (status) => {
    const map = {
      pending: {
        label: 'Pending',
        color: shade('orange', 'orange-7'),
      },
      approved: {
        label: 'Approved',
        color: shade('positive', 'green-9'),
      },
      rejected: {
        label: 'Rejected',
        color: shade('red', 'negative'),
      },
    }
    return map[status] ?? { label: status, color: 'grey' }
  }

  return { RoleStatus }
}
