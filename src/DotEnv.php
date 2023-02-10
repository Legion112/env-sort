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
            if (empty($line) || str_starts_with( $line, '#')) {
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
            if (!array_key_exists($key, $this->mapValueByKey)){
                continue;
            }
            $value = $this->mapValueByKey[$key];
            $lines[$base->mapKeyLineNumber[$key]] = sprintf('%s=%s', $key, $value);
        }
        $diffKeys = array_diff_key($this->mapValueByKey, $base->mapValueByKey);

        if (!empty($diffKeys)) {
            $lines[] = '# Missing keys in base';
            foreach ($diffKeys as $key => $value) {
                $lines[] = sprintf('%s=%s', $key, $value);
            }
        }

        return new DotEnv(implode(PHP_EOL, $lines));
    }

}