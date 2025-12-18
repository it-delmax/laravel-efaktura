<?php

declare(strict_types=1);

namespace ItDelmax\LaravelEfaktura\DTO;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

abstract class BaseDTO implements Arrayable, Jsonable, JsonSerializable
{
  public function __construct(array $data = [])
  {
    foreach ($data as $key => $value) {
      if (property_exists($this, $key)) {
        $this->{$key} = $value;
      }
    }
  }

  public function toArray(): array
  {
    $result = [];
    foreach (get_object_vars($this) as $key => $value) {
      if ($value !== null) {
        if ($value instanceof BaseDTO) {
          $result[$key] = $value->toArray();
        } elseif (is_array($value)) {
          $result[$key] = array_map(function ($item) {
            return $item instanceof BaseDTO ? $item->toArray() : $item;
          }, $value);
        } elseif ($value instanceof \BackedEnum) {
          $result[$key] = $value->value;
        } elseif ($value instanceof \DateTimeInterface) {
          $result[$key] = $value->format('c');
        } else {
          $result[$key] = $value;
        }
      }
    }
    return $result;
  }

  public function toJson($options = 0): string
  {
    return json_encode($this->toArray(), $options);
  }

  public function jsonSerialize(): array
  {
    return $this->toArray();
  }

  public static function fromArray(array $data): static
  {
    return new static($data);
  }

  public static function collection(array $items): array
  {
    return array_map(fn(array $item) => static::fromArray($item), $items);
  }

  public function __get(string $name): mixed
  {
    return $this->{$name} ?? null;
  }

  public function __isset(string $name): bool
  {
    return isset($this->{$name});
  }
}
