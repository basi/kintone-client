<?php

declare(strict_types=1);

/**
 * Kintone API Client Generator
 * 
 * This script generates PHP client code from Kintone's OpenAPI specification
 */

class KintoneClientGenerator
{
    private string $projectRoot;
    private string $specPath;
    private string $outputPath;

    public function __construct()
    {
        $this->projectRoot = dirname(__DIR__);
        $this->specPath = $this->projectRoot . '/rest-api-spec';
        $this->outputPath = $this->projectRoot . '/src';
    }

    public function generate(): void
    {
        echo "🚀 Starting Kintone API Client generation...\n";

        // Find the latest spec version
        $latestVersion = $this->findLatestSpecVersion();
        if (!$latestVersion) {
            throw new RuntimeException('No OpenAPI specification found');
        }

        echo "📋 Found latest spec version: $latestVersion\n";

        $openApiFile = $this->specPath . "/kintone/$latestVersion/openapi.yaml";
        if (!file_exists($openApiFile)) {
            throw new RuntimeException("OpenAPI file not found: $openApiFile");
        }

        // Use bundled version if available
        $bundledFile = $this->specPath . "/kintone/$latestVersion/bundled/openapi.yaml";
        if (file_exists($bundledFile)) {
            $openApiFile = $bundledFile;
            echo "📦 Using bundled OpenAPI specification\n";
        }

        echo "📄 Using OpenAPI file: $openApiFile\n";

        // Check if openapi-generator is available
        $this->checkOpenApiGenerator();

        // Clean output directory
        $this->cleanOutputDirectory();

        // Generate PHP client
        $this->generatePhpClient($openApiFile);

        // Post-process generated files
        $this->postProcess();

        echo "✅ Generation completed successfully!\n";
        echo "📁 Generated files are in: {$this->outputPath}\n";
    }

    private function findLatestSpecVersion(): ?string
    {
        $kintoneDir = $this->specPath . '/kintone';
        if (!is_dir($kintoneDir)) {
            return null;
        }

        $versions = [];
        $iterator = new DirectoryIterator($kintoneDir);
        
        foreach ($iterator as $fileInfo) {
            if ($fileInfo->isDot() || !$fileInfo->isDir()) {
                continue;
            }
            
            $dirname = $fileInfo->getFilename();
            if (preg_match('/^\d{14}$/', $dirname)) {
                $versions[] = $dirname;
            }
        }

        if (empty($versions)) {
            return null;
        }

        rsort($versions);
        return $versions[0];
    }

    private function checkOpenApiGenerator(): void
    {
        // Check if Java is available
        exec('java -version 2>&1', $javaOutput, $javaReturnCode);
        if ($javaReturnCode !== 0) {
            throw new RuntimeException(
                'Java is required for OpenAPI Generator. Please install Java:\n' .
                '  Ubuntu/Debian: sudo apt install default-jdk\n' .
                '  macOS: brew install openjdk\n' .
                '  Windows: Download from https://adoptium.net/'
            );
        }

        // Check if openapi-generator-cli is available via npx
        exec('npx @openapitools/openapi-generator-cli version 2>&1', $output, $returnCode);
        
        if ($returnCode !== 0) {
            echo "⚠️  @openapitools/openapi-generator-cli not found. Installing via npm...\n";
            
            // Try to install via npm
            exec('npm install -g @openapitools/openapi-generator-cli 2>&1', $installOutput, $installReturnCode);
            
            if ($installReturnCode !== 0) {
                throw new RuntimeException(
                    'Failed to install openapi-generator-cli. Please install it manually: ' .
                    'npm install -g @openapitools/openapi-generator-cli'
                );
            }
        }

        echo "✅ OpenAPI Generator is available\n";
    }

    private function cleanOutputDirectory(): void
    {
        if (is_dir($this->outputPath)) {
            echo "🧹 Cleaning output directory...\n";
            $this->removeDirectory($this->outputPath);
        }
        mkdir($this->outputPath, 0755, true);
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    private function generatePhpClient(string $openApiFile): void
    {
        echo "⚙️  Generating PHP client from OpenAPI specification...\n";

        $command = sprintf(
            'npx @openapitools/openapi-generator-cli generate ' .
            '-i %s ' .
            '-g php ' .
            '-o %s ' .
            '--package-name=Kintone ' .
            '--invoker-package=Kintone ' .
            '--model-package=Model ' .
            '--api-package=Api ' .
            '--additional-properties=srcBasePath=lib',
            escapeshellarg($openApiFile),
            escapeshellarg($this->projectRoot . '/temp')
        );

        echo "Running: $command\n";
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new RuntimeException('Failed to generate PHP client: ' . implode("\n", $output));
        }

        // Move generated files to src directory
        $tempLibDir = $this->projectRoot . '/temp/lib';
        if (is_dir($tempLibDir)) {
            $this->moveDirectory($tempLibDir, $this->outputPath);
        }

        // Clean up temp directory
        $tempDir = $this->projectRoot . '/temp';
        if (is_dir($tempDir)) {
            $this->removeDirectory($tempDir);
        }
    }

    private function moveDirectory(string $source, string $destination): void
    {
        if (!is_dir($source)) {
            return;
        }

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $target = $destination . '/' . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                copy($item->getPathname(), $target);
            }
        }
    }

    private function postProcess(): void
    {
        echo "🔧 Post-processing generated files...\n";
        
        // Add any custom post-processing here
        // For example:
        // - Fix namespace issues
        // - Add custom headers
        // - Adjust authentication methods
        
        echo "✅ Post-processing completed\n";
    }
}

// Run the generator
try {
    $generator = new KintoneClientGenerator();
    $generator->generate();
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}