<?php

namespace App\Jobs;

use App\Models\FileUpload;
use App\Models\Product;
use App\Notifications\FileProcessingCompleted;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;
use League\Csv\Statement;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        protected FileUpload $fileUpload
    ) {}

    public function handle(): void
    {
        try {
            $this->fileUpload->update(['status' => 'processing']);
            
            $csv = Reader::createFromPath(storage_path('app/private/' . $this->fileUpload->stored_filename), 'r');
            $csv->setHeaderOffset(0);
            $csv->setDelimiter(',');
            
            // Clean UTF-8
            $csv->addStreamFilter('convert.iconv.UTF-8/UTF-8//IGNORE');
            
            $records = Statement::create()->process($csv);
            $this->fileUpload->update(['total_rows' => count($records)]);
            
            foreach ($records as $record) {
                $this->processRecord($record);
                $this->fileUpload->increment('processed_rows');
            }
            
            $this->fileUpload->update(['status' => 'completed']);
            
            // Send notification
            if ($this->fileUpload->user) {
                $this->fileUpload->user->notify(new FileProcessingCompleted($this->fileUpload));
            }
            
        } catch (\Exception $e) {
            Log::error('CSV Processing failed: ' . $e->getMessage(), [
                'file_upload_id' => $this->fileUpload->id,
                'error' => $e->getMessage(),
            ]);
            
            $this->fileUpload->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    protected function processRecord(array $record): void
    {
        Product::updateOrCreate(
            ['unique_key' => $record['UNIQUE_KEY']],
            [
                'product_title' => $record['PRODUCT_TITLE'],
                'product_description' => $record['PRODUCT_DESCRIPTION'],
                'style_number' => $record['STYLE#'],
                'sanmar_mainframe_color' => $record['SANMAR_MAINFRAME_COLOR'],
                'size' => $record['SIZE'],
                'color_name' => $record['COLOR_NAME'],
                'piece_price' => $record['PIECE_PRICE'],
            ]
        );
    }
}