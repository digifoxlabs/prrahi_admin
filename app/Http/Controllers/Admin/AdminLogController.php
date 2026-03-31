<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileObject;

class AdminLogController extends Controller
{
    public function index(Request $request): View
    {
        $title = 'Logs';
        $logFiles = $this->logFiles();
        $selectedFile = $request->query('file');
        $selectedLog = $selectedFile ? $this->resolveLogPath($selectedFile) : $logFiles->first();

        return view('admin.logs.index', [
            'title' => $title,
            'logFiles' => $logFiles,
            'selectedFile' => $selectedLog ? $this->relativeLogPath($selectedLog) : null,
            'logContents' => $selectedLog ? $this->tailFile($selectedLog, 500) : '',
        ]);
    }

    public function clear(Request $request): RedirectResponse
    {
        $scope = $request->input('scope', 'selected');

        if ($scope === 'all') {
            foreach ($this->logFiles() as $logFile) {
                file_put_contents($logFile, '');
            }

            return redirect()
                ->route('admin.logs.index')
                ->with('success', 'All log files were cleared successfully.');
        }

        $selectedFile = $request->input('file');
        $logPath = $selectedFile ? $this->resolveLogPath($selectedFile) : null;

        if (! $logPath) {
            return redirect()
                ->route('admin.logs.index')
                ->with('error', 'Please select a valid log file to clear.');
        }

        file_put_contents($logPath, '');

        return redirect()
            ->route('admin.logs.index', ['file' => $this->relativeLogPath($logPath)])
            ->with('success', 'Selected log file was cleared successfully.');
    }

    private function logFiles(): Collection
    {
        $logsRoot = storage_path('logs');

        if (! is_dir($logsRoot)) {
            return collect();
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($logsRoot, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $files = collect();

        foreach ($iterator as $file) {
            if (! $file->isFile()) {
                continue;
            }

            $path = $file->getRealPath();

            if (! $path) {
                continue;
            }

            $files->push($path);
        }

        return $files
            ->sort()
            ->values();
    }

    private function resolveLogPath(string $relativePath): ?string
    {
        $logsRoot = realpath(storage_path('logs'));

        if (! $logsRoot) {
            return null;
        }

        $sanitizedRelativePath = ltrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath), DIRECTORY_SEPARATOR);
        $candidatePath = realpath($logsRoot.DIRECTORY_SEPARATOR.$sanitizedRelativePath);

        if (! $candidatePath || ! str_starts_with($candidatePath, $logsRoot)) {
            return null;
        }

        return is_file($candidatePath) ? $candidatePath : null;
    }

    private function relativeLogPath(string $absolutePath): string
    {
        $logsRoot = realpath(storage_path('logs'));

        return ltrim(str_replace($logsRoot ?: '', '', $absolutePath), DIRECTORY_SEPARATOR);
    }

    private function tailFile(string $path, int $maxLines = 500): string
    {
        $file = new SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $lastLine = $file->key();
        $startLine = max(0, $lastLine - $maxLines + 1);
        $lines = [];

        $file->seek($startLine);

        while (! $file->eof()) {
            $lines[] = rtrim((string) $file->current(), "\r\n");
            $file->next();
        }

        return trim(implode(PHP_EOL, $lines));
    }
}
