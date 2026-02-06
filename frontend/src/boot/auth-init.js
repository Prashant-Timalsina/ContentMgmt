import { boot } from "quasar/wrappers";
import { useAuthStore} from 'src/stores/authStore.js'

export default boot(async () => {
    const authStore = useAuthStore()
    await authStore.init()
})