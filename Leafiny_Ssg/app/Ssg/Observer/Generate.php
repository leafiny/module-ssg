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
 * Class Ssg_Observer_Generate
 */
class Ssg_Observer_Generate extends Core_Observer implements Core_Interface_Observer
{
    /**
     * Generate page
     *
     * @param Leafiny_Object $object
     *
     * @return void
     * @throws Exception
     */
    public function execute(Leafiny_Object $object): void
    {
        if (!$this->getHelper()->isEnabled()) {
            return;
        }
        /** @var Core_Model $entity */
        $entity = $object->getData('object');

        $identifier = $entity->getData($this->getIdentifierField());
        if (!$identifier) {
            return;
        }

        $url = App::getUrlRewrite($identifier, $this->getEntityType());

        $language = $this->getLanguageField() ? $entity->getData($this->getLanguageField()) : null;

        $this->getHelper()->generatePage($url, $url, $language);
    }

    /**
     * Retrieve static helper
     *
     * @return Ssg_Helper_Data
     */
    public function getHelper(): Ssg_Helper_Data
    {
        return App::getSingleton('helper', 'ssg');
    }

    /**
     * Retrieve page language key
     *
     * @return string|null
     */
    public function getLanguageField(): ?string
    {
        return $this->getCustom('language');
    }

    /**
     * Retrieve page identifier key
     *
     * @return string
     */
    public function getIdentifierField(): string
    {
        return $this->getCustom('identifier');
    }

    /**
     * Retrieve model entity type
     *
     * @return string
     */
    public function getEntityType(): string
    {
        return $this->getCustom('entity_type');
    }
}
