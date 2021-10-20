<?php
/**
 * This file is part of Leafiny.
 *
 * Copyright (C) Magentix SARL
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

/**
 * Class Ssg_Helper_Data
 */
class Ssg_Helper_Data extends Core_Helper
{
    /**
     * Generate all pages
     *
     * @throws Exception
     */
    public function generateAll(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $this->flushCache();
        $this->generatePages();
        $this->generateEntities();
        $this->flushCache();
    }

    /**
     * Generate pages from identifiers
     *
     * @throws Exception
     */
    public function generatePages(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        foreach ($this->getIdentifiers() as $identifier) {
            if (!$identifier) {
                continue;
            }
            foreach ($this->getLanguages() as $language) {
                $this->generatePage($identifier, $identifier, $language);
            }
        }
    }

    /**
     * Generate pages from entities
     *
     * @throws Exception
     */
    public function generateEntities(): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        foreach ($this->getEntities() as $entity => $config) {
            if (!($config['enabled'] ?? 1)) {
                continue;
            }
            if (!isset($config['identifier'])) {
                continue;
            }

            /** @var Core_Model $model */
            $model = App::getSingleton('model', $entity);
            $pages = $model->getList($config['filters'] ?? []);

            foreach ($pages as $page) {
                $key = $config['language'] ?? null;
                $language = null;
                if ($key) {
                    $language = $page->getData($key);
                }
                if ($language && !in_array($language, $this->getLanguages())) {
                    continue;
                }

                $url = App::getUrlRewrite($page->getData($config['identifier']), $entity);
                $this->generatePage($url, $url, $language);
            }
        }
    }

    /**
     * Generate a page and write file in the directory
     *
     * @param string      $url
     * @param string      $identifier
     * @param string|null $language
     *
     * @return string
     * @throws Exception
     */
    public function generatePage(string $url, string $identifier, ?string $language = null): string
    {
        if ($language === null) {
            $language = App::getLanguage();
        }

        /* Save current config */
        $currentLanguage = App::getLanguage();
        $currentHost = $_SERVER['HTTP_HOST'];

        /* Update current config */
        App::setLanguage($language);
        $_SERVER['HTTP_HOST'] = $this->getHost($language);

        try {
            $page = App::getPage($identifier);
            App::unsSingleton();
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            $page = $throwable->getMessage();
        }

        $this->write($url, $page, $language);

        /* Reset config */
        $_SERVER['HTTP_HOST'] = $currentHost;
        App::setLanguage($currentLanguage);

        return $page;
    }

    /**
     * Flush all the cache
     *
     * @throws Exception
     */
    protected function flushCache(): void
    {
        /** @var Core_Helper_File $file */
        $file = App::getObject('helper_file');
        /** @var Core_Helper $helper */
        $helper = App::getObject('helper');

        $file->rmdir($helper->getCacheDir());
    }

    /**
     * Write page in static directory
     *
     * @param string      $url
     * @param string      $content
     * @param string|null $language
     *
     * @return int
     * @throws Exception
     */
    public function write(string $url, string $content, ?string $language = null): int
    {
        return (int)file_put_contents($this->getFilePath($url, $language), $content);
    }

    /**
     * Retrieve file path
     *
     * @param string      $url
     * @param string|null $language
     *
     * @return string
     * @throws Exception
     */
    public function getFilePath(string $url, ?string $language = null): string
    {
        $path = App::getRootDir() . $this->getStaticDir() . $this->getLanguageDir($language) . $this->getFileName($url);

        $this->getFileHelper()->mkdir(dirname($path));

        return $path;
    }

    /**
     * Retrieve static file name from url
     *
     * @param string $url
     *
     * @return string
     */
    public function getFileName(string $url): string
    {
        $url = substr($url, -1) === '/' ? $url . 'index.html' : $url;

        return str_replace(US, DS, ltrim($url, US));
    }

    /**
     * Retrieve static directory
     *
     * @return string
     */
    public function getStaticDir(): string
    {
        return ($this->getCustom('directory') ?: 'static') . DS;
    }

    /**
     * Retrieve language directory
     *
     * @param string|null $language
     *
     * @return string
     */
    public function getLanguageDir(?string $language = null): string
    {
        if ($language === null) {
            $language = App::getLanguage();
        }

        return str_replace('_', '-', strtolower($language)) . DS;
    }

    /**
     * Retrieve languages
     *
     * @return string[]
     */
    public function getLanguages(): array
    {
        return array_keys($this->getHosts());
    }

    /**
     * Is generator enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->getCustom('enabled');
    }

    /**
     * Retrieve entities
     *
     * @return mixed[]
     */
    public function getEntities(): array
    {
        return $this->getCustom('generator')['entities'] ?? [];
    }

    /**
     * Retrieve identifiers
     *
     * @return string[]
     */
    public function getIdentifiers(): array
    {
        return $this->getCustom('generator')['identifiers'] ?? ['/'];
    }

    /**
     * Retrieve hosts
     *
     * @return string[]
     */
    public function getHosts(): array
    {
        return $this->getCustom('host') ?? [];
    }

    /**
     * Retrieve host
     *
     * @param string $language
     *
     * @return string
     */
    public function getHost(string $language): string
    {
        return $this->getHosts()[$language] ?? App::getHost();
    }

    /**
     * Retrieve file helper
     *
     * @return Core_Helper_File
     */
    protected function getFileHelper(): Core_Helper_File
    {
        return App::getSingleton('helper_file');
    }
}
