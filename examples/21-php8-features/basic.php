<?php

declare(strict_types=1);

// Самостоятельные примеры тем статьи: https://habr.com/ru/articles/504356/
// Все возможности появились в PHP 8.0. Запускать на обновлённом PHP.

// 1. Атрибуты: итоговый синтаксис #[...], а не предварительный <<...>>.
#[Attribute(Attribute::TARGET_METHOD)]
class Lesson
{
    public string $topic;

    public function __construct(string $topic)
    {
        $this->topic = $topic;
    }
}

class Course
{
    #[Lesson('Атрибуты PHP 8')]
    public function introduction(): void
    {
        echo "Начинаем урок", PHP_EOL;
    }
}

// Метаданные сами не запускают метод: их читает код через Reflection.
$method = new ReflectionMethod(Course::class, 'introduction');
$lesson = $method->getAttributes(Lesson::class)[0]->newInstance();
echo $lesson->topic, PHP_EOL;

// 2. Union types и mixed (mixed допускает любое значение, включая null).
function total(int|float $price, int $quantity): int|float
{
    return $price * $quantity;
}

function describe(mixed $value): string
{
    return get_debug_type($value);
}

echo total(12.5, 2), PHP_EOL; // 25
echo describe(null), PHP_EOL; // null

// 3. static в возвращаемом типе сохраняет тип вызывающего подкласса.
class Message
{
    public static function create(): static
    {
        return new static();
    }
}

class WelcomeMessage extends Message
{
}

echo WelcomeMessage::create()::class, PHP_EOL; // WelcomeMessage

// 4. JIT — настройка среды. Вычисление работает и без OPcache/JIT.
function sumSquares(int $limit): int
{
    $sum = 0;
    for ($i = 1; $i <= $limit; $i++) {
        $sum += $i * $i;
    }
    return $sum;
}

echo sumSquares(100), PHP_EOL; // 338350
if (function_exists('opcache_get_status')) {
    $status = opcache_get_status(false);
    echo 'JIT включён: ', ($status['jit']['on'] ?? false) ? 'да' : 'нет', PHP_EOL;
} else {
    echo 'OPcache недоступен', PHP_EOL;
}

// 5. throw как выражение; 6. catch без переменной исключения.
function requireTitle(array $input): string
{
    return $input['title'] ?? throw new InvalidArgumentException('Нет заголовка');
}

echo requireTitle(['title' => 'Изучаем PHP']), PHP_EOL;
try {
    requireTitle([]);
} catch (InvalidArgumentException) {
    echo 'Заголовок обязателен', PHP_EOL;
}

// 7. Завершающая запятая в объявлении параметров появилась в PHP 8.0.
function formatLesson(
    string $title,
    int $number,
): string {
    return "$number. $title";
}

echo formatLesson('Строки', 11), PHP_EOL;

// 8. Новые строковые функции: регистр учитывается, пустая подстрока найдена.
$filename = 'lesson-php8.php';
var_dump(str_contains($filename, 'php8')); // true
var_dump(str_starts_with($filename, 'lesson-')); // true
var_dump(str_ends_with($filename, '.php')); // true
var_dump(str_contains($filename, 'PHP8')); // false
var_dump(str_contains($filename, '')); // true

// 9. __toString() автоматически делает класс реализацией Stringable.
class LessonTitle
{
    public function __toString(): string
    {
        return 'Основы PHP 8';
    }
}

function printTitle(Stringable $title): void
{
    echo $title, PHP_EOL;
}

$title = new LessonTitle();
printTitle($title);

// 10. Получение имени класса через объект.
echo $title::class, PHP_EOL; // LessonTitle
