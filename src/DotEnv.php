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
            $lines[] = PHP_EOL;
            $lines[] = '# Missing keys in base';
            foreach ($diffKeys as $key => $value) {
                $lines[] = sprintf('%s=%s', $key, $value);
            }
        }

        return new DotEnv(implode(PHP_EOL, $lines));
    }

    public function group():self
    {
        $groupKeys = [];
        $lines = $this->lines;
        foreach ($this->mapValueByKey as $key => $value) {
            if (!$this->hasGroup($key)){
                $groupKeys[$key] = [$key];
                continue;
            }
            [$group] = explode('_', $key);
            if (!array_key_exists($group, $groupKeys)) {
                $groupKeys[$group] = [];
            } else {
                unset($lines[$this->mapKeyLineNumber[$key]]);
            }
            $groupKeys[$group][] = $key;
        }

        foreach ($groupKeys as $keys) {
            $firstKey = $keys[0];
            $lineReplacement = [];

            foreach ($keys as $key) {
                $lineReplacement[] = sprintf('%s=%s', $key, $this->mapValueByKey[$key]);
            }
            $lines[$this->mapKeyLineNumber[$firstKey]] = $lineReplacement;
        }
        $groupedLines = [];
        foreach ($lines as $keyValueOrMultipleValues) {
            if (is_array($keyValueOrMultipleValues)) {
                array_push($groupedLines, ...$keyValueOrMultipleValues);
            } else {
                $groupedLines[] = $keyValueOrMultipleValues;
            }
        }

        return new DotEnv(implode(PHP_EOL, $groupedLines));
    }

    private function hasGroup(string $key):bool
    {
        return str_contains($key, '_');
    }


}