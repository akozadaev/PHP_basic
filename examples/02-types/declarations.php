<?php

declare(strict_types=1);

// Примеры для лекции 2; синтаксис PHP 8.2+, среда курса — PHP 8.5.12+.
function transform(iterable $items, callable $callback): array
{
    $result = [];
    foreach ($items as $item) {
        $result[] = $callback($item);
    }
    return $result;
}

$double = fn(int $value): int => $value * 2;
echo json_encode(transform([1, 2], $double)), PHP_EOL; // [2,4]
echo json_encode(transform(new ArrayIterator([3, 4]), $double)), PHP_EOL; // [6,8]

function greeting(?string $name = null): string
{
    return $name ?? 'Гость';
}
echo greeting(), PHP_EOL; // Гость

function accepted(): true { return true; }
function rejected(): false { return false; }
function noValue(): null { return null; }
var_dump(accepted(), rejected(), noValue()); // true, false, null

function notify(): void
{
    echo 'Сообщение отправлено', PHP_EOL;
}
notify();

function fail(): never
{
    throw new RuntimeException('Операция прервана');
}
try {
    fail();
} catch (RuntimeException $error) {
    echo $error->getMessage(), PHP_EOL;
}

interface Named
{
    public function name(): string;
}

class User implements Named
{
    public function name(): string { return 'Анна'; }
    public function same(): self { return $this; }
    public static function create(): static { return new static(); }
}

class Student extends User
{
    public function asUser(): parent { return $this; }
}

function printName(Named $person): void
{
    echo $person->name(), PHP_EOL;
}

$student = Student::create();
printName($student); // Анна
echo $student->same()::class, PHP_EOL; // Student
echo $student->asUser()::class, PHP_EOL; // Student
// self и parent ограничивают допустимый тип, не преобразуют сам объект.

enum Status: string
{
    case Draft = 'draft';
    case Published = 'published';
}

function statusLabel(Status $status): string
{
    return $status->value;
}
echo statusLabel(Status::Published), PHP_EOL; // published

function itemCount(Countable&Iterator $items): int
{
    return count($items);
}

function optionalCount((Countable&Iterator)|null $items): int
{
    return $items === null ? 0 : itemCount($items);
}
echo itemCount(new ArrayIterator([10, 20])), PHP_EOL; // 2
echo optionalCount(null), PHP_EOL; // 0

// resource нельзя объявить в сигнатуре; проверяем значение во время выполнения.
$stream = fopen('php://memory', 'r+');
if (is_resource($stream)) {
    echo get_debug_type($stream), PHP_EOL; // resource (stream)
    fclose($stream);
}
