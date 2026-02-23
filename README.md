# KOTAK

KotaK — Сайт ледового катка с бронированием коньков
Веб-приложение для ледового катка. Посетители могут купить входной билет и забронировать коньки онлайн. Администратор управляет инвентарём и просматривает заказы.

# Основные возможности

Для посетителей                 |	     Для администратора
                                |
Покупка входного билета (300₽)  |	 Управление коньками (CRUD)
Бронирование коньков (150₽/час) |	 Просмотр всех бронирований
Выбор модели и размера коньков  |	 Изменение статуса бронирования
Оплата через ЮKassa             |	 Отметка использованных билетов
Просмотр доступных размеров     |	 Очистка базы (завершённые/отменённые заказы)


## Сущности (модели)

**users** — админ
id, name, email, password, is_admin

**skate_models** — модели коньков
id, name, brand, description, image

**skates** — экземпляры коньков (размер/количество)
id, skate_model_id, size, quantity, available_quantity, price_per_hour

**bookings** — бронирования
id, full_name, phone, hours, skate_id, skate_size, total_amount, payment_id, payment_status, has_own_skates

**tickets** — билеты
id, full_name, phone, email, amount, payment_id, payment_status, is_used, used_at

**payments** — платежи 
id, user_id, full_name, phone, amount, payment_id, status, paid, type, booking_id, ticket_id

## Бизнес-правила
Входной билет — 300₽ (один раз, доступ на весь день)

Аренда коньков — 150₽/час (1–4 часа)

При бронировании можно прийти со своими коньками

Количество доступных коньков уменьшается после оплаты и возвращается при отмене/завершении

Статусы бронирования: pending, paid, completed, cancelled

Статусы билета: pending, paid, is_used

## Технологии

PHP 8.2
Laravel 11
MySQL
Tailwind CSS
ЮKassa SDK 
IMask 

## Установка и запуск (полная инструкция)

- Настройка .env:
DB_CONNECTION=mysql
DB_HOST=MySQL-8.0
DB_PORT=3306
DB_DATABASE=kotak
DB_USERNAME=root
DB_PASSWORD=

# ЮKassa (тестовые данные)
YOOKASSA_SHOP_ID=1224152
YOOKASSA_SECRET_KEY=test_TQmJq6_24Ty0Z46F7Y6kejxPCZ7VnhHrJ8l8i0VO508
YOOKASSA_RETURN_URL=http://localhost:8000/payment/success

- Миграции:
php artisan migrate

- Сидеры (админ + коньки):
php artisan db:seed 

- Запуск сервер:
php artisan serve

ВсЁ пОбЕдА
