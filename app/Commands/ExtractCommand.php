<?php

namespace App\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use League\HTMLToMarkdown\HtmlConverter;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class ExtractCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'extract
                            {relation : The database relation}
                            {column : The column with HTML content}
                            {--header=* : Column to be used as YAML header}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Extracts a HTML content field from the database to a markdown file';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // to create header fields array
        if ($this->options('header')) {
            $headerFields = [];

            foreach ($this->options('header')['header'] as $fieldName) {
                if (strpos($fieldName, ',')) {
                    $headerFields = array_merge($headerFields, explode(',', $fieldName));

                    continue;
                }

                $headerFields[] =  $fieldName;
            }
        }

        $selectedFields = array_merge([$this->argument('column')], $headerFields ?? []);

        $rows = DB::table($this->argument('relation'))
            ->select($selectedFields)
            ->get();

        $this->line('Extraction started...');

        $extractor = new HtmlConverter();
        $rowCounter = 0;

        foreach ($rows as $row) {
            // to generate file name
            $fileName = uniqid() . '.md';

            // to generate yaml file
            if ($headerFields) {
                $header = "---\n";

                foreach ($headerFields as $headerLine) {
                    $end = $headerLine === end($headerFields) ? "\n---\n\n" : "\n";

                    $header =  $header . "{$headerLine}: {$row->{$headerLine}}" . $end;
                };
            }

            $markdownContent = $extractor->convert($row->{$this->argument('column')});
            $fileContent = $header ? $header . $markdownContent : $markdownContent;

            File::put(getcwd() . '/md/' . $fileName, $fileContent);

            $rowCounter++;
        }

        $this->line("Completed:\n");
        $this->info("\t> {$rowCounter} markdown files saved at `md` folder\n");

        // TODO:
        // - Add a way to user customize file name
        // - Add options to work with date fields:
        // $date = \Carbon\Carbon::create($row->created_at, 'America/Sao_Paulo')->format('Y-m-d');
        // - Add a way to get data from a relationship:
        // $author = DB::table('authors')->find($row->author_id);
        // - Tests
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
