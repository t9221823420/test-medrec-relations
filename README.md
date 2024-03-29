# Тестовое задание

## Дано
- Часть схемы БД проекта с обширной предметной областью.
- Таблица tb_source с полями MEDREC_ID, ICD, PATIENT_NAME.
- Таблица tb_rel с полями MEDREC_ID, NDC.
- MEDREC_ID - уникальный медицинский номер пациента.
- ICD - код диагноза пациента (International Classification of Diseases).
- PATIENT_NAME - имя пациента.
- NDC - код медикамента, выписанного пациенту (National Drug Code).
- Один пациент может иметь множество разных медикаментов.
- Один и тот же медикамент может быть выписан пациенту множество раз.
- Один пациент может иметь множество разных диагнозов.
- Один и тот же диагноз может быть выставлен пациенту только один раз.

## Задачи
Требуется текстовый ответ
1. SQL-код: Каким образом можно получить всех пациентов с именем, начинающимся
на “Alex” и c хотя бы одним медикаментом (NDC)?
2. SQL-код: Подсчитать количество пациентов, имеющих более 2 одинаковых
медикаментов.
3. Дать рекомендации по структуре таблиц, использованию БД и кэшированию, если:
- В БД существуют другие таблицы, связанные с описанными в этом задании.
- БД с данными таблицами относится к категории OLTP.
- Количество пациентов в БД в течение года вырастет до 5 млн.
- Еженедельно будут запускаться write-intensive операции импорта данных,
которые могут затрагивать более 50% всех пациентов.
- Бизнес-логика приложения постоянно будет расти и соответственно будет
расти количество других связанных таблиц.

## Условие.
Решите задачи 1-2 на исходной схеме БД. Можете добавить индексы

## Требуется решение в виде PHP-кода
Реализовать решение 1-ой задачи на базе паттернов Entity + Repository. Выведите
результат на web-страницу в виде HTML-таблицы.

Перед выполнением: вам потребуется ознакомиться с паттернами Entity, Repository,
Dependency Injection, если вы с ними еще не знакомы. Реализовывайте в классах только
те методы, которые необходимы для решения задания, лишняя работа не требуется.

## Условия:
- Зависимости приложения должны устанавливаться через composer.
- Автозагрузчик классов вашего приложения должен следовать актуальному
стандарту PSR.
- Можете выбрать любой PHP-фреймворк или решить задачу без него.
- Представление должно быть отделено от бизнес-логики.
- Не используйте готовые ORM. Сделайте свою простую реализацию паттернов
Entity + Repository.
- Инкапсулируйте поиск пациентов в отдельном классе-сервисе с единственным
методом public function search(string $name): array, который
получает поисковый запрос (имя пациента), обращается к Repository и возвращает
массив найденных Entity.
- Внедряйте зависимости через Dependency Injection (через конструктор).
DI-контейнер не обязателен, можно инициализировать зависимости вручную в
контроллере или другой стартовой точке.
- Если вы решите внести изменения в схему БД, то оформите эти изменения в виде
миграций БД.
- Заключите код в GIT-репозиторий.