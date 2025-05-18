<?php


function nf($number): int|string
{
    $v = number_format($number, 3, ".", " ");
    $v = rtrim(trim($v, "0"), ".");
    if (!$v) return 0;
    if ($v[0] == ".") $v = "0" . $v;

    return $v;
}

function underscoreCaseToSeparateWords(string $attribute): string
{
    return implode(' ', array_map('ucfirst', explode('_', $attribute)));
}

function sanitizeOptionValue(string $value): string
{
    $value = preg_replace('/\s+/', ' ', $value);
    $value = preg_replace('/\xc2\xa0/', ' ', $value);
    $value = trim($value);
    return mb_strtolower($value, 'UTF-8');
}

function stringToDecimal(?string $text, bool $isUzcard = false): array|string
{
    if (empty($text))
        return 0;

    if ($isUzcard)
        return str_replace(['UZS', ' ', '.00'], ['', '',], $text);
    else
        return str_replace(['UZS', '.', ','], ['', '', '.'], $text);
}

function convertToMinutesAndSeconds($timeInMinutes)
{
    $seconds = $timeInMinutes * 60;
    $minutes = floor($seconds / 60);
    $remainingSeconds = round($seconds % 60);

    return "{$minutes} мин {$remainingSeconds} сек";
}

function balanceFormat($old, $new)
{
    return ($old == $new ? nf($old) : (nf($old) . ' > ' . nf($new)));
}
