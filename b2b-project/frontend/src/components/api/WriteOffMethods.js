import WriteOffsMethod from './WriteOffsMethod.vue'
import WriteOffDetailMethod from './WriteOffDetailMethod.vue'
import WriteOffCreateMethod from './WriteOffCreateMethod.vue'
import WriteOffUpdateMethod from './WriteOffUpdateMethod.vue'
import WriteOffDeleteMethod from './WriteOffDeleteMethod.vue'
import WriteOffFilesPostMethod from './WriteOffFilesPostMethod.vue'
import WriteOffFilesGetMethod from './WriteOffFilesGetMethod.vue'
import WriteOffFileDeleteMethod from './WriteOffFileDeleteMethod.vue'
import WriteOffFileDraftMethod from './WriteOffFileDraftMethod.vue'

export const writeOffMethods = [
  {
    path: '/write-offs',
    method: 'GET',
    component: WriteOffsMethod,
    title: 'Получение списка списаний'
  },
  {
    path: '/write-offs/{id}',
    method: 'GET',
    component: WriteOffDetailMethod,
    title: 'Получение списания по ID'
  },
  {
    path: '/write-offs',
    method: 'POST',
    component: WriteOffCreateMethod,
    title: 'Создание списания'
  },
  {
    path: '/write-offs/{id}',
    method: 'PUT',
    component: WriteOffUpdateMethod,
    title: 'Обновление списания'
  },
  {
    path: '/write-offs/{id}',
    method: 'DELETE',
    component: WriteOffDeleteMethod,
    title: 'Удаление списания'
  },
  {
    path: '/write-off-files',
    method: 'POST',
    component: WriteOffFilesPostMethod,
    title: 'Загрузка файлов списания'
  },
  {
    path: '/write-off-files/{writeOffId}',
    method: 'GET',
    component: WriteOffFilesGetMethod,
    title: 'Получение файлов списания'
  },
  {
    path: '/write-off-files/{id}',
    method: 'DELETE',
    component: WriteOffFileDeleteMethod,
    title: 'Удаление файла списания'
  },
  {
    path: '/write-off-files/draft',
    method: 'POST',
    component: WriteOffFileDraftMethod,
    title: 'Создание черновика файла списания'
  }
] 