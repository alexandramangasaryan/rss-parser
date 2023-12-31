
# Rss Parser
Проект "RSS парсер" – это приложение, разработанное с использованием фреймворка Laravel, предназначенное для автоматического парсинга данных из указанных RSS-лент и обработки полученной информации.
Основная цель проекта заключается в сборе данных о новых записях из RSS-лент и дальнейшей обработке этих данных для создания задач в системе управления проектами Redmine и отправки уведомлений в Telegram.

# Технические детали

- Приложение разработано с использованием фреймворка Laravel, что обеспечивает гибкость, безопасность и удобство разработки.
- Для управления зависимостями и окружением проекта используется Docker (sail).
- Парсинг RSS-лент выполняется с использованием соответствующих библиотек или компонентов в Laravel.
- Взаимодействие с базой данных, а также создание задач в Redmine и отправка уведомлений в Telegram осуществляется с использованием соответствующих API и библиотек.

## Установка проекта

### 1. Установка Docker

- Установите [Docker Desktop][link-docker].
- Если у вас Windows, убедитесь, что подсистема Windows для Linux 2 (WSL2) установлена и включена. Информацию о том, как установить и включить WSL2, можно найти в [документации][link-wsl].
- Установите и запустите Терминал (например, [Windows Powershell][link-powershell])

### 2. Подключение GIT

- Создайте директорию для проекта, например `rssParser`, и перейдите в нее.
- Клонируйте репозиторий (обратите внимание на точку в конце, она обозначает "клонировать в текущую директорию"):

```
git clone git@git.anmarto.ru:man/rss-parser.git .
```

### 3. Работа с GIT

- В git проекта основная ветка:
    - **master** - деплой из этой ветки происходит на стенд `production` (боевой сайт)
- Все доработки производятся в новой ветке, наследованной от `master`, название новой ветки `ticket/1234`, где `1234` - номер задачи в redmine.
### 4. Обновление проекта из GIT

- После клонирования проекта у вас должна появиться ветка `master`, связанная с удаленной веткой `origin/master`
- Убедитесь, что вы находитесь в ветке `master` (например, если до этого работали с другой веткой)
- Получите последние изменения (`git pull`)
- Скопируйте файл `.env.example`, переименуйте его в `.env`
- В файле .env добавьте переменную `RSS_FEEDS`
- Эта переменная должна содержать URL-адрес RSS-ленты. Пример:
```
RSS_FEEDS=https://example.com/rss/feed1
```
- Сохраните изменения в файле `.env`
- Запустите сборку docker:
```
./vendor/bin/sail up --build -d
```

### 5. Установка зависимостей composer

Установите соответствующие зависимости следующей командой:

```
./vendor/bin/sail composer install
```

### 6. Создание таблиц в базе данных

- Выполните миграции Laravel, чтобы создать необходимые таблицы в базе данных
- В случае возникновения проблем с миграциями (например, если миграции были нужна уже наполненная БД, и она выдала ошибку), просто сделайте импорт базы данных вручную

```
./vendor/bin/sail artisan migrate
```
### 7. Запуск парсера

- Для запуска парсера выполните команду:

```
./vendor/bin/sail artisan rss:parse
```

- Запуск обработчика очереди задач:

```
./vendor/bin/sail artisan queue:listen
```

### 8. Пример получения свежих изменений

```
git checkout master
git pull
./vendor/bin/sail composer install
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan optimize:clear
```

### 9. Сервер:
```
IP-адрес: 185.103.132.52
```

После выполнения этих шагов проект "RSS парсер" должен быть успешно установлен и запущен с помощью Laravel Sail.
Приложение будет периодически парсить указанные RSS-ленты, обрабатывать данные и создавать задачи в Redmine и уведомления в Telegram.

### Настройка отслеживания конкретных данных
Чтобы отслеживать конкретные данные из своих RSS-лент, выполните следующие шаги:

### 1. Откройте таблицу "settings"
- В приложении "RSS парсер" есть таблица "settings", которая содержит основные настройки для отслеживания данных.
### 2. Добавьте новые данные
- Для каждой RSS-ленты, которую вы хотите отслеживать, добавьте следующие данные в таблицу "settings":
    - **Название:** Укажите название программы из RSS-ленты, чтобы легко идентифицировать ее в дальнейшем.
    - **Redmine URL:** Укажите URL-адрес вашего Redmine-проекта, куда будут создаваться задачи на основе данных из RSS-ленты.
    - **Redmine API Key:** Укажите ваш API ключ Redmine для возможности создания задач.
    - **Project ID:** Укажите идентификатор проекта в Redmine, куда будут создаваться задачи (может быть числовым значением или строкой).
    - **Telegram Chat ID:** Укажите идентификатор чата в Telegram, куда будут отправляться уведомления.
    - **Telegram Bot Token:** Укажите токен вашего Telegram-бота для возможности отправки уведомлений.
### 3. Сохраните изменения
- Убедитесь, что все данные правильно заполнены, и сохраните изменения в таблице "settings".
  
После выполнения этих шагов, парсер будет отслеживать конкретные программы из указанных RSS-лент и автоматически создавать задачи в Redmine и отправлять уведомления в указанный чат в Telegram.

### Список полезных ссылок / документация

- [Документация Laravel][link-laravel]
- [Docker][link-docker]
- [WSL][link-wsl]
- [Node.js][link-node]


[link-laravel]: https://laravel.com/docs
[link-docker]: https://www.docker.com/products/docker-desktop
[link-wsl]: https://docs.microsoft.com/ru-ru/windows/wsl/install-win10
[link-powershell]: https://apps.microsoft.com/store/detail/windows-terminal/9N0DX20HK701
[link-node]: https://nodejs.org/

После выполнения этих шагов проект "RSS парсер" должен быть успешно установлен и запущен с помощью Laravel Sail.
Приложение будет периодически парсить указанные RSS-ленты, обрабатывать данные и создавать задачи в Redmine и уведомления в Telegram.
