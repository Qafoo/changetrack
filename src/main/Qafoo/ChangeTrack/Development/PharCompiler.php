<?php

namespace Qafoo\ChangeTrack\Development;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;

/**
 * The Compiler class compiles composer into a phar
 *
 * Converted from Composer's compiler.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class PharCompiler
{
    /**
     * @var mixed
     */
    private $version;

    /**
     * @var string
     */
    private $directory;

    /**
     * @param string $directory
     */
    public function __construct($directory)
    {
        $this->directory = realpath($directory);
    }

    /**
     * Compiles ChangeTrack into a single PHAR file.
     */
    public function compile()
    {
        $pharFile = 'changetrack.phar';

        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $process = new Process('git log --pretty="%H" -n1 HEAD', $this->directory);
        if ($process->run() != 0) {
            throw new \RuntimeException(
                "Can't run git log. You must ensure to run compile from git repository clone and that git binary is available."
            );
        }
        $this->version = trim($process->getOutput());

        $process = new Process('git describe --tags HEAD');
        if ($process->run() == 0) {
            $this->version = trim($process->getOutput());
        }

        $phar = new \Phar($pharFile, 0, 'changetrack.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')
            ->notName('PharCompiler.php')
            ->in($this->directory . '/src/main');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->in($this->directory . '/src/config');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }


        $finder = new Finder();
        $finder->files()
            ->ignoreVCS(true)
            ->name('*.php')->name('*.xsd')
            ->exclude('test')
            ->exclude('features')
            ->in($this->directory . '/vendor');

        foreach ($finder as $file) {
            $this->addFile($phar, $file);
        }

        $this->addTrackBin($phar);

        // Stubs
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        unset($phar);
    }

    /**
     * Adds $file to $phar, optionally stripping whitespaces
     *
     * @param \Phar $phar
     * @param \SplFileInfo $file
     * @param bool $strip
     */
    private function addFile($phar, $file, $strip = true)
    {
        $path = str_replace($this->directory . DIRECTORY_SEPARATOR, '', $file->getRealPath());

        $content = file_get_contents($file);
        if ($strip) {
            $content = $this->stripWhitespace($content);
        } elseif ('LICENSE' === basename($file)) {
            $content = "\n".$content."\n";
        }

        $content = str_replace('@package_version@', $this->version, $content);

        $phar->addFromString($path, $content);
    }

    /**
     * Adds the main binary to the $phar
     *
     * @param \Phar $phar
     */
    private function addTrackBin($phar)
    {
        $content = file_get_contents($this->directory . '/src/bin/track');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);
        $phar->addFromString('src/bin/track', $content);
    }

    /**
     * Removes whitespace from a PHP source string while preserving line numbers.
     *
     * @param  string $source A PHP string
     * @return string The PHP string with the whitespace removed
     */
    private function stripWhitespace($source)
    {
        if (!function_exists('token_get_all')) {
            return $source;
        }

        $output = '';
        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $output .= $token;
            } elseif (in_array($token[0], array(T_COMMENT, T_DOC_COMMENT))) {
                $output .= str_repeat("\n", substr_count($token[1], "\n"));
            } elseif (T_WHITESPACE === $token[0]) {
                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $token[1]);
                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);
                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);
                $output .= $whitespace;
            } else {
                $output .= $token[1];
            }
        }

        return $output;
    }

    /**
     * Returns the stub for the PHAR archive
     *
     * @return string
     */
    private function getStub()
    {
        return <<<'EOF'
#!/usr/bin/env php
<?php
Phar::mapPhar('changetrack.phar');

require 'phar://changetrack.phar/src/bin/track';

__HALT_COMPILER();
EOF;
    }
}
