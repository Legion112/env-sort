<?php

declare(strict_types=1);

namespace App;

class DotEnv
{
    /** @var list<string>  */
    private array $lines;
    /** @var array<string, int> */
    private array $mapKeyLineNumber;

    /** @var array<string, string> */
    private array $mapValueByKey;
    public function __construct(string $content)
    {
        $this->lines = explode(PHP_EOL, $content);
        foreach ($this->lines as $lineIndex => $line) {
            if (str_starts_with( $line, '#') || empty($line)) {
                continue;
            }
            [$key, $value] = explode('=', $line);
            $this->mapKeyLineNumber[$key] = $lineIndex;
            $this->mapValueByKey[$key] = $value;
        }
    }

    public function toString():string
    {
        return implode(PHP_EOL, $this->lines);
    }

    public function sortAsIn(self $base):self
    {
        $lines = $base->lines;
        foreach ($base->mapKeyLineNumber as $key => $lineNumber) {
            $value = $this->mapValueByKey[$key];
            $lines[$base->mapKeyLineNumber[$key]] = sprintf('%s=%s', $key, $value);
        }

        return new DotEnv(implode(PHP_EOL, $lines));
    }

}