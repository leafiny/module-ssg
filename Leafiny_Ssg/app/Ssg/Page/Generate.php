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
 * Class Ssg_Page_Generate
 */
class Ssg_Page_Generate extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        try {
            /** @var Ssg_Helper_Data $helper */
            $helper = App::getSingleton('helper', 'ssg');
            $helper->generateAll();
            App::log(App::translate('All pages have been generated'));
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        $this->redirect($this->getRefererUrl());
    }
}
