
### Файл AddToCartController.php
1. Не импортирован `Uuid`.

### Файл Connector.php
1. Добавить логгирование ошибок.
2. В методе `get` необходимо использовать строку в качестве ключа, а не объект

### Файл ConnectorFacade.php
1. Добавить логгирование ошибок.

### Файл CartManager.php
1. Метод `build` уже вызывается в `ConnectorFacade`.
2. Текст "Error" не дает полного объяснения ошибки в логах.
3. Видимо, перепутан порядок аргументов в `$this->connector->set()`.
4. Использование `session_id()` без проверки, инициирована ли сессия, может вызвать ошибки.
5. При создании корзины не хватает нескольких аргументов.

### Файл ProductRepository.php
1. Запрос к БД не защищён от sql инъекций.
2. Запросы могут вернуться пустыми - этот вариант не обработан.
3. Добавить значения по-умолчанию для необязательных полей thumbnail и description.
4. Использовать `fetchAssociative()` вместо `fetchOne()` для `getByUuid()`.

### Файл CartView.php
1. Добавить логгирование ошибок.
2. Вместо того, чтобы при каждом прохождении цикла обращаться к БД, получить сначала все `Uuid` и обратиться к БД 1 раз.
3. Для каждого нового `item` выводится `total` с учетом предыдущих.

### Файл ProductsView.php
1. Добавить логгирование ошибок.
2. Необходимо исправить опечатку в названии файла и класса
3. Добавить значения по умолчанию для необязательных полей
