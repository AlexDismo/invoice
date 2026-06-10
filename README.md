# Invoice Desk

Full-stack модуль для роботи з інвойсами: Laravel 13 API + Nuxt 4 + MySQL.

## Запуск

```bash
docker compose up --build
```

| Сервіс   | URL                   |
|----------|-----------------------|
| Frontend | http://localhost:3000 |
| Backend  | http://localhost:8000 |
| MySQL    | localhost:3306        |

При першому старті контейнер автоматично встановлює залежності, виконує міграції та сідер із тестовими даними.

## Структура

```
backend/           Laravel REST API (PHP 8.4)
frontend/          Nuxt 4 + Vue 3.5 + Element Plus + TailwindCSS 4
docker-compose.yml
```

## API

| Метод  | Ендпоінт                       | Опис                                  |
|--------|--------------------------------|---------------------------------------|
| GET    | `/api/invoices`                | Список інвойсів (сортування за датою) |
| GET    | `/api/invoices/{id}`           | Один інвойс                           |
| POST   | `/api/invoices`                | Створити інвойс                       |
| PUT    | `/api/invoices/{id}`           | Редагувати (тільки `pending`)         |
| PATCH  | `/api/invoices/{id}/status`    | Змінити статус на `approved`/`rejected` |

## Відповіді на запитання з ТЗ

**1. Як структуровано frontend і backend?**

Backend — stateless Laravel API: Form Requests для валідації, API Resources для JSON, Enum для статусів. Frontend — окремий Nuxt 4 застосунок із composable `useInvoices` як єдиною точкою доступу до API. SSR звертається до внутрішнього Docker-хосту, браузер — до localhost.

**2. Які компроміси були зроблені?**

Прямі URL з CORS замість Nuxt-проксі — простіше і однаково працює для SSR та клієнта. Клієнтська валідація (vee-validate + zod) дублює частину серверних правил задля миттєвого зворотного зв'язку. SQLite in-memory залишено тільки для PHPUnit. Seeder запускається один раз при першому старті.

**3. Що б покращив у production?**

Автентифікація, пагінація за замовчуванням, audit log інвойсів, OpenAPI-специфікація, CI pipeline, production Nuxt build за nginx, індекси по `status` та `due_date`, rate limiting.

**4. Які UX edge cases враховано?**

Loading та error стани на всіх сторінках. Форма редагування заблокована для не-pending інвойсів. Gross amount перераховується автоматично. Дати валідуються на клієнті та сервері. Помилки бекенду прокидаються у відповідні поля форми. Підтвердження перед зміною статусу. Порожній стан списку з CTA.
