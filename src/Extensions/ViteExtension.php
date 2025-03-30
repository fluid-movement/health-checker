<?php

namespace App\Extensions;

use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

class ViteExtension extends AbstractExtension
{
    public function __construct(
        private readonly bool $isDev,
        private readonly string $devServerUrl,
        private readonly string $manifestPath
    )
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite', [$this, 'vite'], ['is_safe' => ['html']]),
        ];
    }

    public function vite(array $entries): string
    {
        if ($this->isDev) {
            return $this->devScripts($entries);
        } else {
            return $this->prodScripts($entries);
        }
    }

    private function devScripts(array $entries): string
    {
        $tags = [];
        $tags[] = "<script type=\"module\" src=\"{$this->devServerUrl}/@vite/client\"></script>";
        foreach ($entries as $entry) {
            $tags[] = "<script type=\"module\" src=\"{$this->devServerUrl}/{$entry}\"></script>";
        }
        return implode("\n", $tags);
    }

    private function prodScripts(array $entries): string
    {
        $manifest = json_decode(file_get_contents($this->manifestPath), true);
        $tags = [];
        foreach ($entries as $entry) {
            if (isset($manifest[$entry])) {
                $file = $manifest[$entry]['file'];
                if (isset($manifest[$entry]['css'])) {
                    foreach ($manifest[$entry]['css'] as $cssFile) {
                        $tags[] = "<link rel=\"stylesheet\" href=\"/dist/{$cssFile}\">";
                    }
                }
                $tags[] = "<script type=\"module\" src=\"/dist/{$file}\"></script>";
            }
        }
        return implode("\n", $tags);
    }
}
