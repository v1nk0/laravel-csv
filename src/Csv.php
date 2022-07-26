<?php

namespace V1nk0\LaravelCsv;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class Csv
{
    public array $lines = [];
    public string $delimiter = ',';

    function setDelimiter($delimiter): self
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    function addLine(array $line): self
    {
        foreach($line as $k => $v) {
            if(strstr($v, $this->delimiter)) {
                $line[$k] = str_replace($this->delimiter, '', $v);
            }
        }

        $this->lines[] = $line;
        return $this;
    }

    public function content(): string
    {
        return $this->getContent();
    }

    function getContent(): string
    {
        $content = '';
        foreach($this->lines as $line_key => $line) {
            foreach($line as $field_key => $field) {
                if($field_key !== array_key_first($line)) {
                    $content .= $this->delimiter;
                }
                $content .= $field;
            }

            if($line_key !== array_key_last($this->lines)) {
                $content .= "\r\n";
            }
        }

        return $content;
    }

    public function store(string $filePath, string $disk = 'local')
    {
        Storage::disk($disk)
            ->put($filePath, $this->content());
    }

    public function response(string $fileName = 'export.csv'): Response
    {
        $response = response($this->content(), 200);
        $response->header('Content-Type', 'application/text');
        $response->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
        return $response;
    }
}
