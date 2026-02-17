import { useQuasar } from 'quasar'

export function useStatusTheme() {
  const $q = useQuasar()

  const shade = (dark, light) => ($q.dark.isActive ? dark : light)

  const articleStatus = (status) => {
    const map = {
      draft: {
        label: 'Draft',
        color: shade('grey-5', 'grey-7'),
      },
      submitted: {
        label: 'Pending',
        color: shade('orange', 'orange-7'),
      },
      published: {
        label: 'Approved',
        color: shade('positive', 'green-7'),
      },
      rejected: {
        label: 'Rejected',
        color: shade('negative', 'red'),
      },
    }

    return map[status] ?? { label: status, color: 'grey' }
  }

  return { articleStatus }
}
