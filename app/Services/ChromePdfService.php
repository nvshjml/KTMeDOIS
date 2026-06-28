<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Process\Process;

class ChromePdfService
{
    public function download(string $view, array $data, string $filename): BinaryFileResponse
    {
        $chromePath = $this->chromePath();

        abort_unless($chromePath, 500, 'Chrome or Edge is required to generate PDF downloads.');

        $directory = storage_path('app/generated-pdfs');
        File::ensureDirectoryExists($directory);

        $id = (string) Str::uuid();
        $htmlPath = $directory.DIRECTORY_SEPARATOR.$id.'.html';
        $pdfPath = $directory.DIRECTORY_SEPARATOR.$id.'.pdf';
        $profilePath = $directory.DIRECTORY_SEPARATOR.$id.'-chrome-profile';

        File::put($htmlPath, view($view, array_merge($data, [
            'autoPrint' => false,
            'pdfMode' => true,
        ]))->render());

        $process = new Process([
            $chromePath,
            '--headless=new',
            '--disable-gpu',
            '--disable-crash-reporter',
            '--disable-breakpad',
            '--disable-extensions',
            '--disable-features=Crashpad',
            '--noerrdialogs',
            '--no-sandbox',
            '--allow-file-access-from-files',
            '--user-data-dir='.$profilePath,
            '--print-to-pdf='.$pdfPath,
            $htmlPath,
        ]);
        $process->setTimeout(60);
        $process->run();

        if (! $process->isSuccessful() || ! File::exists($pdfPath)) {
            $fallbackProcess = new Process([
                $chromePath,
                '--headless',
                '--disable-gpu',
                '--disable-crash-reporter',
                '--disable-breakpad',
                '--disable-extensions',
                '--disable-features=Crashpad',
                '--noerrdialogs',
                '--no-sandbox',
                '--allow-file-access-from-files',
                '--user-data-dir='.$profilePath,
                '--print-to-pdf='.$pdfPath,
                $htmlPath,
            ]);
            $fallbackProcess->setTimeout(60);
            $fallbackProcess->run();

            abort_unless($fallbackProcess->isSuccessful() && File::exists($pdfPath), 500, 'PDF could not be generated.');
        }

        File::delete($htmlPath);
        File::deleteDirectory($profilePath);

        return response()
            ->download($pdfPath, $filename, ['Content-Type' => 'application/pdf'])
            ->deleteFileAfterSend(true);
    }

    private function chromePath(): ?string
    {
        $paths = array_filter([
            env('PDF_CHROME_PATH'),
            'C:\Program Files\Google\Chrome\Application\chrome.exe',
            'C:\Program Files (x86)\Google\Chrome\Application\chrome.exe',
            getenv('LOCALAPPDATA') ? getenv('LOCALAPPDATA').'\Google\Chrome\Application\chrome.exe' : null,
            'C:\Program Files\Microsoft\Edge\Application\msedge.exe',
            'C:\Program Files (x86)\Microsoft\Edge\Application\msedge.exe',
        ]);

        foreach ($paths as $path) {
            if (is_string($path) && File::exists($path)) {
                return $path;
            }
        }

        return null;
    }
}
